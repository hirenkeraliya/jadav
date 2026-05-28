<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Customer;
use App\Models\FinanceEntry;
use App\Models\FinanceEntryType;
use App\Models\PaymentType;
use App\Models\Project;
use App\Models\ProjectCompletion;
use App\Models\ProjectCompletionItem;
use App\Models\ProjectCompletionPayment;
use App\Models\ProjectType;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Task;
use App\Models\TermsTemplate;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Seeds all demo data: users, settings, customers, projects,
 * tasks, quotations, finance entries and completions.
 *
 * Run: php artisan db:seed --class=FullDataSeeder
 */
class FullDataSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ── Foundation (users + settings) ─────────────────────────────────────
        $this->call(UsersAndSettingsSeeder::class);

        $company  = Company::where('email', 'demo@studiostudio.com')->firstOrFail();
        $admin    = User::where('email', 'admin@studiostudio.com')->firstOrFail();
        $manager  = User::where('email', 'manager@studiostudio.com')->firstOrFail();
        $staff    = User::where('email', 'staff@studiostudio.com')->firstOrFail();

        $creditType  = FinanceEntryType::where('company_id', $company->id)->where('direction', 'credit')->first();
        $debitType   = FinanceEntryType::where('company_id', $company->id)->where('direction', 'debit')->first();
        $upi         = PaymentType::where('company_id', $company->id)->where('name', 'UPI')->first();
        $bank        = PaymentType::where('company_id', $company->id)->where('name', 'Bank Transfer')->first();
        $terms       = TermsTemplate::where('company_id', $company->id)->where('is_default_quotation', true)->first();
        $projectType = ProjectType::where('company_id', $company->id)->first();

        // ── Customers ─────────────────────────────────────────────────────────
        $customers = [
            [
                'customer_code' => 'CUST-001',
                'name'          => 'Anil Sharma',
                'email'         => 'anil.sharma@example.com',
                'mobile'        => '+91 98100 11111',
                'organization'  => 'Sharma & Sons Pvt. Ltd.',
                'address'       => '45, Park Avenue, New Delhi 110001',
                'source'        => 'Referral',
                'status'        => 'active',
            ],
            [
                'customer_code' => 'CUST-002',
                'name'          => 'Meera Patel',
                'email'         => 'meera.patel@example.com',
                'mobile'        => '+91 98200 22222',
                'organization'  => null,
                'address'       => '7, Sea View Apartments, Mumbai 400005',
                'source'        => 'Social Media',
                'status'        => 'active',
            ],
            [
                'customer_code' => 'CUST-003',
                'name'          => 'Horizon Hospitality',
                'email'         => 'projects@horizonhotel.in',
                'mobile'        => '+91 98300 33333',
                'organization'  => 'Horizon Hotels Group',
                'address'       => '200, MG Road, Bangalore 560001',
                'source'        => 'Walk-in',
                'status'        => 'active',
            ],
            [
                'customer_code' => 'CUST-004',
                'name'          => 'Sunita Reddy',
                'email'         => 'sunita.reddy@example.com',
                'mobile'        => '+91 98400 44444',
                'organization'  => null,
                'address'       => '12, Lake View Colony, Hyderabad 500034',
                'source'        => 'Referral',
                'status'        => 'active',
            ],
            [
                'customer_code' => 'CUST-005',
                'name'          => 'TechNest Offices',
                'email'         => 'facilities@technest.io',
                'mobile'        => '+91 98500 55555',
                'organization'  => 'TechNest Solutions Pvt. Ltd.',
                'address'       => 'Tower B, Cyber City, Gurgaon 122002',
                'source'        => 'Online',
                'status'        => 'active',
            ],
        ];

        $createdCustomers = [];
        foreach ($customers as $data) {
            $createdCustomers[] = Customer::firstOrCreate(
                ['customer_code' => $data['customer_code'], 'company_id' => $company->id],
                array_merge($data, ['company_id' => $company->id])
            );
        }

        [$cAnil, $cMeera, $cHorizon, $cSunita, $cTechNest] = $createdCustomers;

        // ── Projects ──────────────────────────────────────────────────────────
        $projectsData = [
            [
                'project_code'  => 'PRJ-0001',
                'name'          => 'Sharma Residence — 3BHK Renovation',
                'customer_id'   => $cAnil->id,
                'location'      => 'New Delhi',
                'site_address'  => '45, Park Avenue, New Delhi 110001',
                'start_date'    => '2026-01-10',
                'end_date'      => '2026-06-30',
                'lead_by'       => $admin->id,
                'scope_of_work' => 'Complete interior renovation including living room, 3 bedrooms, kitchen and 2 bathrooms. Custom joinery, false ceiling, lighting design.',
                'status'        => 'running',
                'priority'      => 'high',
                'internal_notes'=> 'Client has strict timeline for housewarming in July.',
            ],
            [
                'project_code'  => 'PRJ-0002',
                'name'          => 'Patel Apartment — Full Interior',
                'customer_id'   => $cMeera->id,
                'location'      => 'Mumbai',
                'site_address'  => '7, Sea View Apartments, Mumbai 400005',
                'start_date'    => '2026-03-01',
                'end_date'      => '2026-08-15',
                'lead_by'       => $manager->id,
                'scope_of_work' => '2BHK full interior with modular kitchen, wardrobes, and living area furniture.',
                'status'        => 'pending',
                'priority'      => 'medium',
                'internal_notes'=> null,
            ],
            [
                'project_code'  => 'PRJ-0003',
                'name'          => 'Horizon Hotel — Lobby & Restaurant Fitout',
                'customer_id'   => $cHorizon->id,
                'location'      => 'Bangalore',
                'site_address'  => '200, MG Road, Bangalore 560001',
                'start_date'    => '2025-10-01',
                'end_date'      => '2026-04-30',
                'lead_by'       => $admin->id,
                'scope_of_work' => 'Luxury lobby fitout, 80-cover restaurant interior, bar counter, and concierge area.',
                'status'        => 'completed',
                'priority'      => 'high',
                'internal_notes'=> 'Approved brand guidelines must be followed. Final snag list pending sign-off.',
            ],
            [
                'project_code'  => 'PRJ-0004',
                'name'          => 'Reddy Residence — Master Bedroom & Study',
                'customer_id'   => $cSunita->id,
                'location'      => 'Hyderabad',
                'site_address'  => '12, Lake View Colony, Hyderabad 500034',
                'start_date'    => '2026-04-15',
                'end_date'      => '2026-07-31',
                'lead_by'       => $staff->id,
                'scope_of_work' => 'Master bedroom wardrobe, study unit, false ceiling and ambient lighting.',
                'status'        => 'running',
                'priority'      => 'medium',
                'internal_notes'=> null,
            ],
            [
                'project_code'  => 'PRJ-0005',
                'name'          => 'TechNest Office — 3rd Floor Fitout',
                'customer_id'   => $cTechNest->id,
                'location'      => 'Gurgaon',
                'site_address'  => 'Tower B, Cyber City, Gurgaon 122002',
                'start_date'    => '2026-05-01',
                'end_date'      => '2026-10-31',
                'lead_by'       => $manager->id,
                'scope_of_work' => 'Open-plan office fitout for 120 workstations, 4 conference rooms, reception and breakout areas.',
                'status'        => 'pending',
                'priority'      => 'high',
                'internal_notes'=> 'LEED certification requirements apply.',
            ],
        ];

        $createdProjects = [];
        foreach ($projectsData as $data) {
            $createdProjects[] = Project::firstOrCreate(
                ['project_code' => $data['project_code'], 'company_id' => $company->id],
                array_merge($data, ['company_id' => $company->id])
            );
            // Attach project type
            if ($projectType) {
                $createdProjects[array_key_last($createdProjects)]->projectTypes()->syncWithoutDetaching([$projectType->id]);
            }
        }

        [$pSharma, $pPatel, $pHorizon, $pReddy, $pTechNest] = $createdProjects;

        // ── Tasks ─────────────────────────────────────────────────────────────
        $tasksData = [
            // Sharma project tasks
            ['project_id' => $pSharma->id,  'title' => 'Site measurement & survey',    'assigned_to' => $staff->id,   'due_date' => '2026-01-15', 'priority' => 'high',   'status' => 'completed'],
            ['project_id' => $pSharma->id,  'title' => 'Concept design approval',       'assigned_to' => $admin->id,   'due_date' => '2026-02-01', 'priority' => 'high',   'status' => 'completed'],
            ['project_id' => $pSharma->id,  'title' => 'False ceiling execution',       'assigned_to' => $staff->id,   'due_date' => '2026-04-30', 'priority' => 'medium', 'status' => 'in_progress'],
            ['project_id' => $pSharma->id,  'title' => 'Kitchen modular installation',  'assigned_to' => $manager->id, 'due_date' => '2026-05-15', 'priority' => 'high',   'status' => 'pending'],

            // Patel project tasks
            ['project_id' => $pPatel->id,   'title' => 'Initial client walkthrough',    'assigned_to' => $manager->id, 'due_date' => '2026-03-05', 'priority' => 'medium', 'status' => 'completed'],
            ['project_id' => $pPatel->id,   'title' => 'Modular kitchen design',        'assigned_to' => $admin->id,   'due_date' => '2026-04-01', 'priority' => 'medium', 'status' => 'pending'],

            // Horizon project tasks
            ['project_id' => $pHorizon->id, 'title' => 'Material procurement',         'assigned_to' => $staff->id,   'due_date' => '2025-11-01', 'priority' => 'high',   'status' => 'completed'],
            ['project_id' => $pHorizon->id, 'title' => 'Lobby flooring installation',  'assigned_to' => $staff->id,   'due_date' => '2026-01-31', 'priority' => 'high',   'status' => 'completed'],
            ['project_id' => $pHorizon->id, 'title' => 'Snag list resolution',         'assigned_to' => $manager->id, 'due_date' => '2026-05-10', 'priority' => 'medium', 'status' => 'in_progress'],

            // Reddy project tasks
            ['project_id' => $pReddy->id,   'title' => 'Wardrobe design finalisation', 'assigned_to' => $staff->id,   'due_date' => '2026-04-20', 'priority' => 'medium', 'status' => 'completed'],
            ['project_id' => $pReddy->id,   'title' => 'Civil & carpentry work',       'assigned_to' => $staff->id,   'due_date' => '2026-06-15', 'priority' => 'medium', 'status' => 'in_progress'],

            // TechNest project tasks
            ['project_id' => $pTechNest->id,'title' => 'Space planning & BOQ',        'assigned_to' => $admin->id,   'due_date' => '2026-05-20', 'priority' => 'high',   'status' => 'pending'],
            ['project_id' => $pTechNest->id,'title' => 'Client presentation',         'assigned_to' => $manager->id, 'due_date' => '2026-06-01', 'priority' => 'high',   'status' => 'pending'],
        ];

        foreach ($tasksData as $task) {
            Task::firstOrCreate(
                ['project_id' => $task['project_id'], 'title' => $task['title']],
                $task
            );
        }

        // ── Quotations ────────────────────────────────────────────────────────
        $quotationsData = [
            [
                'company_id'       => $company->id,
                'customer_id'      => $cAnil->id,
                'quotation_number' => 'QT-2026-001',
                'version'          => 1,
                'date'             => '2025-12-15',
                'valid_until'      => '2026-01-15',
                'tax_label'        => 'GST',
                'tax_rate'         => 18,
                'discount_type'    => 'percentage',
                'discount_value'   => 5,
                'subtotal'         => 850000,
                'discount_amount'  => 42500,
                'tax_amount'       => 145350,
                'total'            => 952850,
                'status'           => 'accepted',
                'terms_template_id'=> $terms?->id,
                'notes'            => 'Inclusive of design fees, material supply and installation.',
                'items' => [
                    ['name' => 'Design & Consultancy Fees',    'qty' => 1,   'unit' => 'LS',   'unit_rate' => 150000, 'sort_order' => 1],
                    ['name' => 'Living Room — False Ceiling',  'qty' => 280, 'unit' => 'sqft', 'unit_rate' => 350,    'sort_order' => 2],
                    ['name' => 'Master Bedroom — Wardrobe',    'qty' => 1,   'unit' => 'LS',   'unit_rate' => 120000, 'sort_order' => 3],
                    ['name' => 'Modular Kitchen',              'qty' => 1,   'unit' => 'LS',   'unit_rate' => 280000, 'sort_order' => 4],
                    ['name' => 'Painting & Polish Work',       'qty' => 900, 'unit' => 'sqft', 'unit_rate' => 120,    'sort_order' => 5],
                ],
            ],
            [
                'company_id'       => $company->id,
                'customer_id'      => $cHorizon->id,
                'quotation_number' => 'QT-2026-002',
                'version'          => 1,
                'date'             => '2025-09-01',
                'valid_until'      => '2025-10-01',
                'tax_label'        => 'GST',
                'tax_rate'         => 18,
                'discount_type'    => 'fixed',
                'discount_value'   => 100000,
                'subtotal'         => 4500000,
                'discount_amount'  => 100000,
                'tax_amount'       => 792000,
                'total'            => 5192000,
                'status'           => 'accepted',
                'terms_template_id'=> $terms?->id,
                'notes'            => 'All FF&E items as per approved brand specification.',
                'items' => [
                    ['name' => 'Lobby Flooring — Italian Marble',      'qty' => 1200, 'unit' => 'sqft', 'unit_rate' => 850,  'sort_order' => 1],
                    ['name' => 'Reception Counter & Joinery',          'qty' => 1,    'unit' => 'LS',   'unit_rate' => 450000, 'sort_order' => 2],
                    ['name' => 'Restaurant — Furniture & Upholstery',  'qty' => 80,   'unit' => 'covers', 'unit_rate' => 18000, 'sort_order' => 3],
                    ['name' => 'Lighting Design & Fixtures',           'qty' => 1,    'unit' => 'LS',   'unit_rate' => 350000, 'sort_order' => 4],
                    ['name' => 'Bar Counter & Back Bar',               'qty' => 1,    'unit' => 'LS',   'unit_rate' => 280000, 'sort_order' => 5],
                ],
            ],
            [
                'company_id'       => $company->id,
                'customer_id'      => $cTechNest->id,
                'quotation_number' => 'QT-2026-003',
                'version'          => 1,
                'date'             => '2026-04-20',
                'valid_until'      => '2026-05-20',
                'tax_label'        => 'GST',
                'tax_rate'         => 18,
                'discount_type'    => 'percentage',
                'discount_value'   => 3,
                'subtotal'         => 3200000,
                'discount_amount'  => 96000,
                'tax_amount'       => 559440,
                'total'            => 3663440,
                'status'           => 'sent',
                'terms_template_id'=> $terms?->id,
                'notes'            => 'Subject to final headcount approval from client.',
                'items' => [
                    ['name' => 'Workstation Fitout (120 seats)',   'qty' => 120, 'unit' => 'seats', 'unit_rate' => 15000, 'sort_order' => 1],
                    ['name' => 'Conference Rooms (4 nos)',         'qty' => 4,   'unit' => 'rooms', 'unit_rate' => 250000, 'sort_order' => 2],
                    ['name' => 'Reception Area',                   'qty' => 1,   'unit' => 'LS',    'unit_rate' => 300000, 'sort_order' => 3],
                    ['name' => 'Breakout & Pantry Area',          'qty' => 1,   'unit' => 'LS',    'unit_rate' => 250000, 'sort_order' => 4],
                ],
            ],
        ];

        foreach ($quotationsData as $data) {
            $items = $data['items'];
            unset($data['items']);

            $quotation = Quotation::firstOrCreate(
                ['quotation_number' => $data['quotation_number'], 'company_id' => $company->id],
                $data
            );

            if ($quotation->wasRecentlyCreated) {
                foreach ($items as $item) {
                    QuotationItem::create(array_merge($item, [
                        'quotation_id' => $quotation->id,
                        'amount'       => $item['qty'] * $item['unit_rate'],
                    ]));
                }
            }
        }

        // ── Finance Entries ───────────────────────────────────────────────────
        $financeData = [
            // Sharma — Running project with advance & partial payment
            ['project_id' => $pSharma->id, 'type' => 'credit', 'entry_type_id' => $creditType?->id, 'payment_type_id' => $upi?->id,  'amount' => 250000, 'date' => '2026-01-12', 'reference_number' => 'UPI-2026-001', 'remarks' => 'Advance payment — 25%'],
            ['project_id' => $pSharma->id, 'type' => 'debit',  'entry_type_id' => $debitType?->id,  'payment_type_id' => $bank?->id,  'amount' => 85000,  'date' => '2026-02-05', 'reference_number' => null,           'remarks' => 'Material purchase — tiles & fittings'],
            ['project_id' => $pSharma->id, 'type' => 'debit',  'entry_type_id' => $debitType?->id,  'payment_type_id' => $bank?->id,  'amount' => 45000,  'date' => '2026-03-10', 'reference_number' => null,           'remarks' => 'Labour — false ceiling team'],
            ['project_id' => $pSharma->id, 'type' => 'credit', 'entry_type_id' => $creditType?->id, 'payment_type_id' => $bank?->id,  'amount' => 300000, 'date' => '2026-04-01', 'reference_number' => 'NEFT-2026-021','remarks' => 'Progress payment — 2nd instalment'],

            // Horizon — Completed project, full settlement
            ['project_id' => $pHorizon->id,'type' => 'credit', 'entry_type_id' => $creditType?->id, 'payment_type_id' => $bank?->id,  'amount' => 1500000,'date' => '2025-10-05', 'reference_number' => 'RTGS-2025-101','remarks' => 'Mobilisation advance — 30%'],
            ['project_id' => $pHorizon->id,'type' => 'debit',  'entry_type_id' => $debitType?->id,  'payment_type_id' => $bank?->id,  'amount' => 680000, 'date' => '2025-11-15', 'reference_number' => null,           'remarks' => 'Flooring material supply'],
            ['project_id' => $pHorizon->id,'type' => 'debit',  'entry_type_id' => $debitType?->id,  'payment_type_id' => $bank?->id,  'amount' => 320000, 'date' => '2026-01-10', 'reference_number' => null,           'remarks' => 'Furniture & upholstery vendor'],
            ['project_id' => $pHorizon->id,'type' => 'credit', 'entry_type_id' => $creditType?->id, 'payment_type_id' => $bank?->id,  'amount' => 2000000,'date' => '2026-02-20', 'reference_number' => 'RTGS-2026-045','remarks' => 'Progress payment — 2nd'],
            ['project_id' => $pHorizon->id,'type' => 'credit', 'entry_type_id' => $creditType?->id, 'payment_type_id' => $bank?->id,  'amount' => 1692000,'date' => '2026-04-15', 'reference_number' => 'RTGS-2026-089','remarks' => 'Final settlement'],

            // Reddy — Early stage
            ['project_id' => $pReddy->id,  'type' => 'credit', 'entry_type_id' => $creditType?->id, 'payment_type_id' => $upi?->id,  'amount' => 50000,  'date' => '2026-04-17', 'reference_number' => 'UPI-2026-042', 'remarks' => 'Token advance'],
        ];

        foreach ($financeData as $entry) {
            FinanceEntry::firstOrCreate(
                [
                    'project_id' => $entry['project_id'],
                    'date'       => $entry['date'],
                    'amount'     => $entry['amount'],
                    'remarks'    => $entry['remarks'],
                ],
                [
                    'company_id'           => $company->id,
                    'type'                 => $entry['type'],
                    'finance_entry_type_id'=> $entry['entry_type_id'],
                    'payment_type_id'      => $entry['payment_type_id'],
                    'reference_number'     => $entry['reference_number'] ?? null,
                    'recorded_by'          => $admin->id,
                ]
            );
        }

        // ── Project Completions (invoices) ────────────────────────────────────
        $completionHorizon = ProjectCompletion::firstOrCreate(
            ['project_id' => $pHorizon->id, 'invoice_number' => 'INV-2026-001'],
            [
                'company_id'     => $company->id,
                'notes'          => 'Final completion invoice — all works completed as per contract.',
                'subtotal'       => 5192000,
                'total'          => 5192000,
                'paid_amount'    => 5192000,
                'payment_status' => 'paid',
                'created_by'     => $admin->id,
            ]
        );

        if ($completionHorizon->wasRecentlyCreated) {
            ProjectCompletionItem::create(['completion_id' => $completionHorizon->id, 'description' => 'Lobby Flooring — Italian Marble',     'qty' => 1200, 'rate' => 850,    'amount' => 1020000, 'sort_order' => 1]);
            ProjectCompletionItem::create(['completion_id' => $completionHorizon->id, 'description' => 'Reception Counter & Joinery',          'qty' => 1,    'rate' => 450000, 'amount' => 450000,  'sort_order' => 2]);
            ProjectCompletionItem::create(['completion_id' => $completionHorizon->id, 'description' => 'Restaurant Furniture & Upholstery',    'qty' => 80,   'rate' => 18000,  'amount' => 1440000, 'sort_order' => 3]);
            ProjectCompletionItem::create(['completion_id' => $completionHorizon->id, 'description' => 'Lighting Design & Fixtures',           'qty' => 1,    'rate' => 350000, 'amount' => 350000,  'sort_order' => 4]);
            ProjectCompletionItem::create(['completion_id' => $completionHorizon->id, 'description' => 'Bar Counter & Back Bar',               'qty' => 1,    'rate' => 280000, 'amount' => 280000,  'sort_order' => 5]);
            ProjectCompletionItem::create(['completion_id' => $completionHorizon->id, 'description' => 'Variation — Outdoor Seating Addition', 'qty' => 1,    'rate' => 180000, 'amount' => 180000,  'sort_order' => 6]);
            ProjectCompletionItem::create(['completion_id' => $completionHorizon->id, 'description' => 'Less: Bar Counter Omission Variation', 'qty' => 1,    'rate' => -60000, 'amount' => -60000,  'sort_order' => 7]);

            ProjectCompletionPayment::create(['completion_id' => $completionHorizon->id, 'amount' => 1500000, 'date' => '2025-10-05', 'reference' => 'RTGS-2025-101', 'notes' => 'Mobilisation advance',    'recorded_by' => $admin->id]);
            ProjectCompletionPayment::create(['completion_id' => $completionHorizon->id, 'amount' => 2000000, 'date' => '2026-02-20', 'reference' => 'RTGS-2026-045', 'notes' => 'Progress payment 2nd',   'recorded_by' => $admin->id]);
            ProjectCompletionPayment::create(['completion_id' => $completionHorizon->id, 'amount' => 1692000, 'date' => '2026-04-15', 'reference' => 'RTGS-2026-089', 'notes' => 'Final settlement',       'recorded_by' => $admin->id]);
        }

        $this->command->info('Full demo data seeded successfully.');
    }
}
