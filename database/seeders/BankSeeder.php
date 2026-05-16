<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bank;

class BankSeeder extends Seeder
{
    public function run()
    {
        $banks = [
            [
                'name' => 'Habib Bank Limited',
                'branch_name' => 'Main Branch',
                'account_title' => 'School Management System',
                'account_number' => '1234-567890-01',
                'iban' => 'PK36HABB0012345678901234',
                'routing_number' => '123456789',
                'address' => '123 Main Street, City',
            ],
            [
                'name' => 'United Bank Limited',
                'branch_name' => 'City Branch',
                'account_title' => 'School Fee Account',
                'account_number' => '9876-543210-02',
                'iban' => 'PK36UNIL0012345678905678',
                'routing_number' => '987654321',
                'address' => '456 Secondary Road, City',
            ],
        ];

        foreach ($banks as $bank) {
            Bank::create(array_merge($bank, ['is_active' => true]));
        }
    }
}