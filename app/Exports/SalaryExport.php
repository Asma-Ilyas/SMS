<?php

namespace App\Exports;

use App\Models\Salary;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalaryExport implements FromQuery, WithHeadings, WithMapping
{
    protected $month;

    public function __construct($month = null)
    {
        $this->month = $month;
    }

    public function query()
    {
        $query = Salary::with('staff');
        if ($this->month) {
            $query->where('month', $this->month);
        }
        return $query;
    }

    public function headings(): array
    {
        return [
            'ID', 'Staff Name', 'Month', 'Basic Salary', 'Allowances',
            'Deductions', 'Net Salary', 'Payment Date', 'Payment Method'
        ];
    }

    public function map($salary): array
    {
        return [
            $salary->id,
            $salary->staff->first_name . ' ' . $salary->staff->last_name,
            $salary->month,
            $salary->basic_salary,
            $salary->allowances,
            $salary->deductions,
            $salary->net_salary,
            $salary->payment_date,
            $salary->payment_method,
        ];
    }
}