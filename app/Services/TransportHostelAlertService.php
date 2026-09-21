<?php

namespace App\Services;

use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\StudentTransportFeePayment;
use App\Models\StudentHostelFeePayment;
use Carbon\Carbon;

/**
 * Computes expiry and overdue-fee alerts for the Transport & Hostel modules.
 *
 * These are computed on read (not stored/queued), so there's no need for a
 * scheduler or notifications table — every call reflects current data.
 * If you want scheduled digest emails later, wrap generate() in a
 * console command + Laravel's task scheduler.
 */
class TransportHostelAlertService
{
    /** Days ahead considered "expiring soon" (not yet expired, but close). */
    protected int $warningDays = 30;

    public function generate(): array
    {
        $today = Carbon::today();
        $warningDate = $today->copy()->addDays($this->warningDays);

        return [
            'driver_licenses' => $this->driverLicenseAlerts($today, $warningDate),
            'vehicle_documents' => $this->vehicleDocumentAlerts($today, $warningDate),
            'transport_fees_overdue' => $this->transportFeeAlerts($today),
            'hostel_fees_overdue' => $this->hostelFeeAlerts($today),
        ];
    }

    public function totalCount(): int
    {
        $alerts = $this->generate();
        return collect($alerts)->sum(fn ($group) => count($group));
    }

    protected function driverLicenseAlerts(Carbon $today, Carbon $warningDate): array
    {
        return Driver::where('is_active', true)
            ->whereNotNull('license_expiry')
            ->where('license_expiry', '<=', $warningDate)
            ->orderBy('license_expiry')
            ->get()
            ->map(function ($driver) use ($today) {
                return [
                    'type' => 'driver_license',
                    'severity' => $driver->license_expiry->lt($today) ? 'expired' : 'warning',
                    'title' => "{$driver->name}'s license " . ($driver->license_expiry->lt($today) ? 'has expired' : 'is expiring soon'),
                    'detail' => 'License #' . $driver->license_number . ' — ' . $driver->license_expiry->format('d M Y'),
                    'date' => $driver->license_expiry,
                    'url' => route('admin.drivers.show', $driver),
                ];
            })->toArray();
    }

    protected function vehicleDocumentAlerts(Carbon $today, Carbon $warningDate): array
    {
        $alerts = [];

        $vehicles = Vehicle::where('status', '!=', 'inactive')
            ->where(function ($q) use ($warningDate) {
                $q->where('insurance_expiry', '<=', $warningDate)
                  ->orWhere('fitness_expiry', '<=', $warningDate)
                  ->orWhere('registration_expiry', '<=', $warningDate);
            })
            ->get();

        foreach ($vehicles as $vehicle) {
            foreach (['insurance_expiry' => 'Insurance', 'fitness_expiry' => 'Fitness certificate', 'registration_expiry' => 'Registration'] as $field => $label) {
                if ($vehicle->$field && $vehicle->$field->lte($warningDate)) {
                    $alerts[] = [
                        'type' => 'vehicle_document',
                        'severity' => $vehicle->$field->lt($today) ? 'expired' : 'warning',
                        'title' => "{$vehicle->vehicle_number}: {$label} " . ($vehicle->$field->lt($today) ? 'expired' : 'expiring soon'),
                        'detail' => $vehicle->$field->format('d M Y'),
                        'date' => $vehicle->$field,
                        'url' => route('admin.vehicles.show', $vehicle),
                    ];
                }
            }
        }

        return collect($alerts)->sortBy('date')->values()->toArray();
    }

    protected function transportFeeAlerts(Carbon $today): array
    {
        return StudentTransportFeePayment::with('studentTransport.student')
            ->whereIn('status', ['pending', 'partial'])
            ->where('due_date', '<', $today)
            ->orderBy('due_date')
            ->get()
            ->map(function ($payment) {
                $student = $payment->studentTransport?->student;
                $name = $student ? "{$student->first_name} {$student->last_name}" : 'Unknown student';
                return [
                    'type' => 'transport_fee_overdue',
                    'severity' => 'expired',
                    'title' => "{$name}: transport fee overdue",
                    'detail' => "{$payment->month} — " . number_format($payment->amount - $payment->paid_amount, 2) . ' remaining, due ' . $payment->due_date->format('d M Y'),
                    'date' => $payment->due_date,
                    'url' => route('admin.transport-fee-payments.index'),
                ];
            })->toArray();
    }

    protected function hostelFeeAlerts(Carbon $today): array
    {
        return StudentHostelFeePayment::with('allocation.student')
            ->whereIn('status', ['pending', 'partial'])
            ->where('due_date', '<', $today)
            ->orderBy('due_date')
            ->get()
            ->map(function ($payment) {
                $student = $payment->allocation?->student;
                $name = $student ? "{$student->first_name} {$student->last_name}" : 'Unknown student';
                return [
                    'type' => 'hostel_fee_overdue',
                    'severity' => 'expired',
                    'title' => "{$name}: hostel fee overdue",
                    'detail' => "{$payment->month} — " . number_format($payment->amount - $payment->paid_amount, 2) . ' remaining, due ' . $payment->due_date->format('d M Y'),
                    'date' => $payment->due_date,
                    'url' => route('admin.hostel-fee-payments.index'),
                ];
            })->toArray();
    }
}
