<?php
// app/Console/Commands/ProcessMonthlyPayroll.php
namespace App\Console\Commands;

use App\Models\Employee;
use App\Services\SalaryCalculator;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessMonthlyPayroll extends Command
{
    protected $signature = 'payroll:process {month?}';
    protected $description = 'Generate salaries for all employees for given month';
    
    public function handle()
    {
        $month = $this->argument('month') ?: Carbon::now()->subMonth()->format('Y-m');
        list($year, $monthNum) = explode('-', $month);
        $employees = Employee::all();
        $paymentDate = Carbon::now()->toDateString();
        
        foreach ($employees as $employee) {
            $calc = new SalaryCalculator($employee, $year, $monthNum);
            $calc->saveSalaryRecord($paymentDate, 'bank', 'Auto payroll');
        }
        $this->info("Payroll processed for $month");
    }
}