<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopedToCompany;
use App\Models\Project;
use App\Models\ProjectVariation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProjectVariationController extends Controller
{
    use ScopedToCompany;

    public function store(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeProject($project);
        abort_unless(auth()->user()->can('variations.create'), 403);

        $data = $request->validate([
            'type'        => ['required', 'in:extra,less'],
            'description' => ['required', 'string', 'max:500'],
            'amount'      => ['required', 'numeric', 'min:0'],
            'date'        => ['required', 'date'],
            'status'      => ['required', 'in:pending,approved,rejected'],
            'notes'       => ['nullable', 'string'],
        ]);

        $project->variations()->create(array_merge($data, [
            'company_id'  => $this->companyId(),
            'recorded_by' => auth()->id(),
        ]));

        return back()->with('success', ucfirst($data['type']) . ' work variation added.');
    }

    public function update(Request $request, Project $project, ProjectVariation $variation): RedirectResponse
    {
        $this->authorizeProject($project);
        abort_unless(auth()->user()->can('variations.edit'), 403);
        abort_if($variation->project_id !== $project->id, 403);

        $data = $request->validate([
            'type'        => ['required', 'in:extra,less'],
            'description' => ['required', 'string', 'max:500'],
            'amount'      => ['required', 'numeric', 'min:0'],
            'date'        => ['required', 'date'],
            'status'      => ['required', 'in:pending,approved,rejected'],
            'notes'       => ['nullable', 'string'],
        ]);

        $variation->update($data);

        return back()->with('success', 'Variation updated.');
    }

    public function destroy(Project $project, ProjectVariation $variation): RedirectResponse
    {
        $this->authorizeProject($project);
        abort_unless(auth()->user()->can('variations.delete'), 403);
        abort_if($variation->project_id !== $project->id, 403);

        $variation->delete();

        return back()->with('success', 'Variation deleted.');
    }

    private function authorizeProject(Project $project): void
    {
        abort_if($project->company_id !== $this->companyId(), 403);
    }
}
