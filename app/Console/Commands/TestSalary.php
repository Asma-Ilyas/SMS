<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Staff;
use App\Services\SalaryCalculator;

class TestSalary extends Command
{
    protected $signature = 'test:salary';
    protected $description = 'Test salary creation for first staff member';

    public function handle()
    {
        $staff = Staff::first();
        if (!$staff) {
            $this->error('No staff found');
            return 1;
        }
        $this->info("Testing for staff: {$staff->first_name} {$staff->last_name} (ID: {$staff->id})");

        try {
            $calculator = new SalaryCalculator($staff, 2026, 5);
            $result = $calculator->saveSalaryRecord('2026-05-15', 'bank');
            if ($result) {
                $this->info("✅ Salary record saved with ID: {$result->id}");
            } else {
                $this->error('❌ Failed to save salary record');
            }
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->error('File: ' . $e->getFile() . ':' . $e->getLine());
            return 1;
        }
        return 0;
    }
}