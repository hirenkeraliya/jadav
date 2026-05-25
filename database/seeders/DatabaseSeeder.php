<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\FinanceEntryType;
use App\Models\PaymentType;
use App\Models\ProjectType;
use App\Models\TermsTemplate;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Roles
        $roles = ['Admin', 'Manager', 'Staff', 'Viewer'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // Demo company
        $company = Company::firstOrCreate(
            ['email' => 'demo@studiostudio.com'],
            [
                'name'                => 'Studio Interior Design',
                'phone'               => '+91 98765 43210',
                'address'             => '12, Design District, Mumbai 400001',
                'currency'            => 'INR',
                'currency_symbol'     => '₹',
                'primary_color'       => '#6366f1',
                'secondary_color'     => '#f59e0b',
                'invoice_prefix'      => 'INV',
                'quotation_prefix'    => 'QT',
                'financial_year_start'=> 4,
                'tax_label'           => 'GST',
                'is_active'           => true,
            ]
        );

        // Super admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'           => 'Super Admin',
                'password'       => Hash::make('password'),
                'is_super_admin' => true,
                'is_active'      => true,
                'must_change_password' => false,
            ]
        );
        $superAdmin->companies()->syncWithoutDetaching([$company->id]);
        $superAdmin->assignRole('Admin');

        // Demo admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@studiostudio.com'],
            [
                'name'     => 'Studio Admin',
                'password' => Hash::make('password'),
                'is_active'=> true,
                'must_change_password' => false,
            ]
        );
        $admin->companies()->syncWithoutDetaching([$company->id]);
        $admin->assignRole('Admin');

        // Demo manager
        $manager = User::firstOrCreate(
            ['email' => 'manager@studiostudio.com'],
            [
                'name'     => 'Priya Manager',
                'password' => Hash::make('password'),
                'is_active'=> true,
                'must_change_password' => false,
            ]
        );
        $manager->companies()->syncWithoutDetaching([$company->id]);
        $manager->assignRole('Manager');

        // Payment types
        $paymentTypes = ['Cash', 'Bank Transfer', 'Cheque', 'UPI', 'Credit Card', 'NEFT / RTGS'];
        foreach ($paymentTypes as $pt) {
            PaymentType::firstOrCreate(['name' => $pt, 'company_id' => $company->id]);
        }

        // Finance entry types
        $entryTypes = [
            ['name' => 'Service Revenue',    'direction' => 'credit'],
            ['name' => 'Material Cost',      'direction' => 'debit'],
            ['name' => 'Labour Cost',        'direction' => 'debit'],
            ['name' => 'Subcontractor',      'direction' => 'debit'],
            ['name' => 'Overheads',          'direction' => 'debit'],
            ['name' => 'Advance Received',   'direction' => 'credit'],
            ['name' => 'Refund',             'direction' => 'debit'],
            ['name' => 'Miscellaneous',      'direction' => 'debit'],
        ];
        foreach ($entryTypes as $et) {
            FinanceEntryType::firstOrCreate(
                ['name' => $et['name'], 'company_id' => $company->id],
                ['direction' => $et['direction'], 'is_active' => true]
            );
        }

        // Project types
        $projectTypes = [
            ['name' => 'Residential',   'color' => '#6366f1'],
            ['name' => 'Commercial',    'color' => '#10b981'],
            ['name' => 'Hospitality',   'color' => '#f59e0b'],
            ['name' => 'Retail',        'color' => '#ef4444'],
            ['name' => 'Office',        'color' => '#8b5cf6'],
        ];
        foreach ($projectTypes as $pt) {
            ProjectType::firstOrCreate(
                ['name' => $pt['name'], 'company_id' => $company->id],
                ['color' => $pt['color'], 'is_active' => true]
            );
        }

        // Default terms template
        TermsTemplate::firstOrCreate(
            ['name' => 'Standard Terms', 'company_id' => $company->id],
            [
                'content'               => "1. All prices are exclusive of GST unless stated.\n2. Payment is due within 30 days of invoice date.\n3. A late payment fee of 2% per month may be charged on overdue amounts.\n4. Work will commence only after advance payment as agreed.\n5. Any changes to the scope of work will be quoted separately.",
                'is_default_quotation'  => true,
                'is_default_invoice'    => true,
            ]
        );
    }
}
