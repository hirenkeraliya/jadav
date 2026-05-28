<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopedToCompany;
use App\Models\Customer;
use App\Models\CustomField;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\ProjectType;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProjectController extends Controller
{
    use ScopedToCompany;

    public function index(Request $request): View
    {
        $cid = $this->companyId();
        $query = Project::where('company_id', $cid)
            ->with(['customer', 'projectTypes', 'leadBy'])
            ->withSum(['financeEntries as total_received' => fn($q) => $q->where('type', 'credit')], 'amount')
            ->withSum(['financeEntries as total_expense' => fn($q) => $q->where('type', 'debit')], 'amount');

        if ($s = $request->input('search')) {
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")->orWhere('project_code', 'like', "%{$s}%"));
        }
        if ($status = $request->input('status')) $query->where('status', $status);
        if ($type = $request->input('project_type_id')) {
            $query->whereHas('projectTypes', fn($q) => $q->where('project_types.id', $type));
        }
        if ($customer = $request->input('customer_id')) $query->where('customer_id', $customer);
        if ($lead = $request->input('lead_by')) $query->where('lead_by', $lead);

        $projects = $query->latest()->paginate(15)->withQueryString();
        $projectTypes = ProjectType::where('company_id', $cid)->where('is_active', true)->get();
        $customers = Customer::where('company_id', $cid)->where('status', 'active')->get();
        $users = User::whereHas('companies', fn($q) => $q->where('companies.id', $cid))->get();

        return view('projects.index', compact('projects', 'projectTypes', 'customers', 'users'));
    }

    public function create(): View
    {
        $cid = $this->companyId();
        $customers = Customer::where('company_id', $cid)->where('status', 'active')->get();
        $projectTypes = ProjectType::where('company_id', $cid)->where('is_active', true)->get();
        $users = User::whereHas('companies', fn($q) => $q->where('companies.id', $cid))->get();
        $customFields = CustomField::where('company_id', $cid)->where('module', 'projects')->where('is_active', true)->orderBy('sort_order')->get();
        return view('projects.create', compact('customers', 'projectTypes', 'users', 'customFields'));
    }

    public function store(Request $request): RedirectResponse
    {
        $cid = $this->companyId();
        $data = $request->validate([
            'project_code'     => ['required', 'string', 'max:50', Rule::unique('projects', 'project_code')],
            'name'             => ['required', 'string', 'max:255'],
            'customer_id'      => ['required', 'exists:customers,id'],
            'project_type_ids'   => ['required', 'array', 'min:1'],
            'project_type_ids.*' => ['exists:project_types,id'],
            'location'         => ['nullable', 'string'],
            'site_address'     => ['nullable', 'string'],
            'start_date'       => ['required', 'date'],
            'end_date'         => ['required', 'date', 'after_or_equal:start_date'],
            'lead_by'          => ['nullable', 'exists:users,id'],
            'scope_of_work'    => ['nullable', 'string'],
            'priority'         => ['required', 'in:low,medium,high'],
            'internal_notes'   => ['nullable', 'string'],
        ]);

        $typeIds = $data['project_type_ids'] ?? [];
        unset($data['project_type_ids']);

        $project = Project::create(array_merge($data, [
            'company_id' => $cid,
            'status'     => 'running',
        ]));
        $project->projectTypes()->sync($typeIds);
        $this->saveCustomFields($request, $project, $cid);

        return redirect()->route('projects.show', $project)->with('success', 'Project created.');
    }

    public function show(Project $project): View
    {
        $this->authorizeCompany($project);
        $project->load(['customer', 'projectTypes', 'leadBy', 'files', 'tasks.assignee', 'quotation', 'completion.items', 'completion.payments.recorder']);

        $cid = $this->companyId();
        $financeEntries = $project->financeEntries()->with(['entryType', 'paymentType', 'recorder'])->latest('date')->get();
        $totalReceived = $financeEntries->where('type', 'credit')->sum('amount');
        $totalExpense  = $financeEntries->where('type', 'debit')->sum('amount');
        $profitLoss    = $totalReceived - $totalExpense;
        $customFields  = CustomField::where('company_id', $cid)->where('module', 'projects')->where('is_active', true)->get();
        $customValues  = $project->customFieldValues()->with('customField')->get()->keyBy('custom_field_id');
        $users         = User::whereHas('companies', fn($q) => $q->where('companies.id', $cid))->get();

        return view('projects.show', compact(
            'project', 'financeEntries', 'totalReceived', 'totalExpense',
            'profitLoss', 'customFields', 'customValues', 'users'
        ));
    }

    public function edit(Project $project): View
    {
        $this->authorizeCompany($project);
        $cid = $this->companyId();
        $customers = Customer::where('company_id', $cid)->where('status', 'active')->get();
        $projectTypes = ProjectType::where('company_id', $cid)->where('is_active', true)->get();
        $users = User::whereHas('companies', fn($q) => $q->where('companies.id', $cid))->get();
        $customFields = CustomField::where('company_id', $cid)->where('module', 'projects')->where('is_active', true)->orderBy('sort_order')->get();
        $customValues = $project->customFieldValues()->get()->keyBy('custom_field_id');
        $project->load('projectTypes');
        return view('projects.edit', compact('project', 'customers', 'projectTypes', 'users', 'customFields', 'customValues'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeCompany($project);
        $cid = $this->companyId();

        $data = $request->validate([
            'project_code'       => ['required', 'string', 'max:50', Rule::unique('projects', 'project_code')->ignore($project->id)],
            'name'               => ['required', 'string', 'max:255'],
            'customer_id'        => ['required', 'exists:customers,id'],
            'project_type_ids'   => ['required', 'array', 'min:1'],
            'project_type_ids.*' => ['exists:project_types,id'],
            'location'           => ['nullable', 'string'],
            'site_address'       => ['nullable', 'string'],
            'start_date'         => ['required', 'date'],
            'end_date'           => ['required', 'date', 'after_or_equal:start_date'],
            'lead_by'            => ['nullable', 'exists:users,id'],
            'scope_of_work'      => ['nullable', 'string'],
            'status'             => ['required', 'in:quotation,running,on_hold,delayed,completed,cancelled'],
            'priority'           => ['required', 'in:low,medium,high'],
            'internal_notes'     => ['nullable', 'string'],
        ]);

        $typeIds = $data['project_type_ids'] ?? [];
        unset($data['project_type_ids']);

        $project->update($data);
        $project->projectTypes()->sync($typeIds);
        $this->saveCustomFields($request, $project, $cid);

        return redirect()->route('projects.show', $project)->with('success', 'Project updated.');
    }

    public function changeStatus(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeCompany($project);
        abort_unless(auth()->user()->can('projects.change_status'), 403);

        $request->validate([
            'status' => ['required', 'in:quotation,running,on_hold,delayed,completed,cancelled'],
        ]);

        $project->update(['status' => $request->status]);

        return back()->with('success', 'Project status updated to ' . ucfirst(str_replace('_', ' ', $request->status)) . '.');
    }

    public function updateWork(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeCompany($project);
        abort_unless(auth()->user()->can('projects.edit'), 403);

        $request->validate([
            'extra_work' => ['nullable', 'string'],
            'less_work'  => ['nullable', 'string'],
        ]);

        $project->update([
            'extra_work' => $request->extra_work,
            'less_work'  => $request->less_work,
        ]);

        return back()->with('success', 'Extra / Less work updated.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->authorizeCompany($project);
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted.');
    }

    public function uploadFile(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeCompany($project);
        $request->validate(['file' => ['required', 'file', 'max:20480'], 'category' => ['nullable', 'string']]);

        $uploaded = $request->file('file');
        $path = $uploaded->store("projects/{$project->id}/files", 'public');

        ProjectFile::create([
            'project_id'    => $project->id,
            'original_name' => $uploaded->getClientOriginalName(),
            'path'          => $path,
            'mime_type'     => $uploaded->getMimeType(),
            'size'          => $uploaded->getSize(),
            'category'      => $request->input('category'),
            'uploaded_by'   => auth()->id(),
        ]);

        return back()->with('success', 'File uploaded.');
    }

    public function pdf(Project $project): Response
    {
        $this->authorizeCompany($project);
        $project->load(['customer', 'projectTypes', 'leadBy', 'tasks.assignee', 'quotation']);

        $cid = $this->companyId();
        $company = $this->company();
        $financeEntries = $project->financeEntries()->with(['entryType', 'paymentType'])->latest('date')->get();
        $totalReceived  = $financeEntries->where('type', 'credit')->sum('amount');
        $totalExpense   = $financeEntries->where('type', 'debit')->sum('amount');
        $customFields   = CustomField::where('company_id', $cid)->where('module', 'projects')->where('is_active', true)->orderBy('sort_order')->get();
        $customValues   = $project->customFieldValues()->get()->keyBy('custom_field_id');

        $pdf = Pdf::loadView('pdf.project', compact(
            'project', 'company', 'financeEntries', 'totalReceived', 'totalExpense', 'customFields', 'customValues'
        ))->setPaper('a4');

        return $pdf->stream('project-' . $project->project_code . '.pdf');
    }

    public function deleteFile(Project $project, ProjectFile $file): RedirectResponse
    {
        $this->authorizeCompany($project);
        abort_if($file->project_id !== $project->id, 403);
        Storage::disk('public')->delete($file->path);
        $file->delete();
        return back()->with('success', 'File removed.');
    }

    private function saveCustomFields(Request $request, Project $project, int $cid): void
    {
        $fields = CustomField::where('company_id', $cid)->where('module', 'projects')->get();
        foreach ($fields as $field) {
            $value = $request->input('custom_' . $field->field_key);
            $project->customFieldValues()->updateOrCreate(
                ['custom_field_id' => $field->id, 'record_type' => Project::class, 'record_id' => $project->id],
                ['value' => is_array($value) ? json_encode($value) : $value]
            );
        }
    }

    private function authorizeCompany(Project $project): void
    {
        abort_if($project->company_id !== $this->companyId(), 403);
    }
}
