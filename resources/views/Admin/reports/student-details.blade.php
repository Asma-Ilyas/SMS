@extends('layouts.app')

@section('content')

{{-- ============================================================
     STUDENT FEE DETAIL — student-details.blade.php
     Variables from controller:
       $student          — Eloquent model (class, section, roll_number, admission_number)
       $installments     — Collection of arrays (fee_type, period, due_date, amount,
                           paid_amount, remaining, status, payment_date,
                           receipt_number, is_overdue)
       $invoices         — Collection of arrays (invoice_number, amount, due_date,
                           status, paid_at, late_fee, discount,
                           other_charge_desc, other_charge_amount, bank)
       $totalDue         — float  (sum of all installment amounts)
       $totalPaid        — float  (sum of all paid_amount)
       $totalRemaining   — float  (sum of remaining per installment)
       $collectionPct    — float  (totalPaid / totalDue × 100)
     ============================================================ --}}

<style>
  @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap');

  :root {
    --ink:       #0d1117;
    --ink2:      #3d4451;
    --ink3:      #6b7280;
    --line:      #e5e7eb;
    --line2:     #f3f4f6;
    --surface:   #ffffff;
    --surface2:  #f9fafb;
    --green:     #16a34a;
    --green-bg:  #f0fdf4;
    --red:       #dc2626;
    --red-bg:    #fef2f2;
    --amber:     #d97706;
    --amber-bg:  #fffbeb;
    --blue:      #2563eb;
    --blue-bg:   #eff6ff;
    --indigo:    #4f46e5;
    --radius:    10px;
    --mono:      'DM Mono', monospace;
    --sans:      'DM Sans', sans-serif;
  }

  .sd-wrap { font-family: var(--sans); color: var(--ink); }

  /* ── cards ── */
  .sd-card { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); margin-bottom: 22px; }
  .sd-card-header { padding: 13px 20px; border-bottom: 1px solid var(--line); font-size: 12px; font-weight: 600; color: var(--ink2); letter-spacing: .04em; text-transform: uppercase; display: flex; align-items: center; gap: 8px; }

  /* ── summary stats ── */
  .sd-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(175px, 1fr)); gap: 14px; margin-bottom: 22px; }
  .sd-stat { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); padding: 16px 18px; }
  .sd-stat-label { font-size: 11px; font-weight: 600; color: var(--ink3); text-transform: uppercase; letter-spacing: .04em; margin-bottom: 5px; }
  .sd-stat-val { font-family: var(--mono); font-size: 21px; font-weight: 500; line-height: 1.1; }
  .sd-stat-bar { height: 3px; border-radius: 2px; background: var(--line); margin-top: 8px; overflow: hidden; }
  .sd-stat-bar-fill { height: 100%; border-radius: 2px; transition: width .6s ease; }

  /* ── info grid ── */
  .sd-info { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 0; }
  .sd-info-cell { padding: 14px 20px; border-right: 1px solid var(--line); }
  .sd-info-cell:last-child { border-right: none; }
  .sd-info-label { font-size: 10.5px; font-weight: 600; color: var(--ink3); text-transform: uppercase; letter-spacing: .04em; margin-bottom: 3px; }
  .sd-info-val { font-size: 13.5px; font-weight: 500; color: var(--ink); }

  /* ── tables ── */
  .sd-table-wrap { overflow-x: auto; }
  .sd-table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
  .sd-table thead th { background: var(--surface2); padding: 9px 14px; text-align: left; font-size: 10.5px; font-weight: 600; color: var(--ink3); letter-spacing: .05em; text-transform: uppercase; border-bottom: 1px solid var(--line); white-space: nowrap; }
  .sd-table thead th.r { text-align: right; }
  .sd-table tbody td { padding: 10px 14px; border-bottom: 1px solid var(--line2); vertical-align: middle; }
  .sd-table tbody tr:last-child td { border-bottom: none; }
  .sd-table tbody tr:hover td { background: var(--surface2); }
  .sd-table td.r { text-align: right; font-family: var(--mono); font-size: 12px; }
  .sd-table td.mono { font-family: var(--mono); font-size: 12px; }
  .sd-table tfoot td { padding: 10px 14px; background: var(--surface2); font-weight: 600; font-size: 12px; font-family: var(--mono); border-top: 1px solid var(--line); }
  .sd-table tfoot td.label { font-family: var(--sans); font-size: 11px; font-weight: 700; color: var(--ink2); text-transform: uppercase; letter-spacing: .04em; }

  /* ── badges ── */
  .sd-badge { display: inline-block; font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 20px; letter-spacing: .03em; white-space: nowrap; }
  .sd-badge-paid    { background: #dcfce7; color: #15803d; }
  .sd-badge-partial { background: #fef9c3; color: #854d0e; }
  .sd-badge-pending { background: #fee2e2; color: #b91c1c; }
  .sd-badge-overdue { background: #ffedd5; color: #9a3412; }
  .sd-badge-inv-paid { background: #dbeafe; color: #1d4ed8; }
  .sd-badge-inv-unpaid { background: #fee2e2; color: #b91c1c; }

  /* ── progress ring ── */
  .sd-ring-wrap { display: flex; align-items: center; gap: 16px; padding: 16px 20px; border-top: 1px solid var(--line); }
  .sd-ring-label { font-size: 12px; color: var(--ink3); }
  .sd-ring-pct { font-family: var(--mono); font-size: 18px; font-weight: 500; color: var(--green); }
  .sd-ring-bar { flex: 1; height: 8px; background: var(--line); border-radius: 4px; overflow: hidden; }
  .sd-ring-fill { height: 100%; border-radius: 4px; background: var(--green); transition: width .7s ease; }

  /* ── buttons ── */
  .sd-btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 7px; font-family: var(--sans); font-size: 12.5px; font-weight: 500; cursor: pointer; text-decoration: none; border: none; transition: filter .15s; }
  .sd-btn:hover { filter: brightness(.92); }
  .sd-btn-gray   { background: var(--line); color: var(--ink2); }
  .sd-btn-green  { background: var(--green); color: #fff; }
  .sd-btn-slate  { background: #334155; color: #fff; }

  /* ── overdue row highlight ── */
  tr.row-overdue td { background: #fff8f0 !important; }

  /* ── extra charges chip ── */
  .sd-chip { display: inline-block; font-size: 10.5px; padding: 1px 7px; border-radius: 12px; margin-right: 4px; margin-top: 2px; }
  .sd-chip-late    { background: #ffedd5; color: #9a3412; }
  .sd-chip-disc    { background: #dcfce7; color: #15803d; }
  .sd-chip-other   { background: #e0e7ff; color: #3730a3; }

  @media print {
    .sd-no-print { display: none !important; }
    .sd-card { break-inside: avoid; }
  }
</style>

<div class="sd-wrap" style="max-width: 1100px; margin: 0 auto; padding: 24px 20px;">

  {{-- ── HEADER ──────────────────────────────────────────────────────── --}}
  <div style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:10px; margin-bottom:20px;">
    <div>
      <div style="font-size:20px; font-weight:600; color:var(--ink)">
        {{ $student->full_name }}
      </div>
      <div style="font-size:12px; color:var(--ink3); margin-top:2px;">
        Fee Detail Report · all figures from installment records
      </div>
    </div>
    <div class="sd-no-print" style="display:flex; gap:8px; flex-wrap:wrap;">
      <button onclick="window.print()" class="sd-btn sd-btn-slate">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        Print
      </button>
      <a href="{{ route('admin.fee-reports.index', request()->query()) }}" class="sd-btn sd-btn-gray">
        ← Back to Reports
      </a>
    </div>
  </div>

  {{-- ── STUDENT INFO CARD ───────────────────────────────────────────── --}}
  <div class="sd-card" style="margin-bottom:22px;">
    <div class="sd-info">
      <div class="sd-info-cell">
        <div class="sd-info-label">Class</div>
        <div class="sd-info-val">{{ $student->class?->full_name ?? 'N/A' }}</div>
      </div>
      <div class="sd-info-cell">
        <div class="sd-info-label">Section</div>
        <div class="sd-info-val">{{ $student->section ?? '—' }}</div>
      </div>
      <div class="sd-info-cell">
        <div class="sd-info-label">Roll No.</div>
        <div class="sd-info-val" style="font-family:var(--mono)">{{ $student->roll_number ?? '—' }}</div>
      </div>
      <div class="sd-info-cell">
        <div class="sd-info-label">Admission No.</div>
        <div class="sd-info-val" style="font-family:var(--mono)">{{ $student->admission_number ?? '—' }}</div>
      </div>
    </div>
  </div>

  {{-- ── SUMMARY CARDS ────────────────────────────────────────────────── --}}
  <div class="sd-stats">
    <div class="sd-stat">
      <div class="sd-stat-label">Total Fee Liability</div>
      <div class="sd-stat-val" style="color:var(--ink)">₹{{ number_format($totalDue, 2) }}</div>
      <div class="sd-stat-bar"><div class="sd-stat-bar-fill" style="width:100%; background:var(--line);"></div></div>
    </div>
    <div class="sd-stat">
      <div class="sd-stat-label">Collected</div>
      <div class="sd-stat-val" style="color:var(--green)">₹{{ number_format($totalPaid, 2) }}</div>
      <div class="sd-stat-bar">
        <div class="sd-stat-bar-fill" style="width:{{ $collectionPct }}%; background:var(--green);"></div>
      </div>
    </div>
    <div class="sd-stat">
      <div class="sd-stat-label">Outstanding</div>
      <div class="sd-stat-val" style="color:var(--red)">₹{{ number_format($totalRemaining, 2) }}</div>
      @php $outPct = $totalDue > 0 ? round($totalRemaining / $totalDue * 100, 1) : 0; @endphp
      <div class="sd-stat-bar">
        <div class="sd-stat-bar-fill" style="width:{{ $outPct }}%; background:var(--red);"></div>
      </div>
    </div>
    <div class="sd-stat">
      <div class="sd-stat-label">Collection Rate</div>
      <div class="sd-stat-val" style="color:var(--blue)">{{ $collectionPct }}%</div>
      <div class="sd-stat-bar">
        <div class="sd-stat-bar-fill" style="width:{{ $collectionPct }}%; background:var(--blue);"></div>
      </div>
    </div>
  </div>

  {{-- ── INSTALLMENT HISTORY ──────────────────────────────────────────── --}}
  <div class="sd-card">
    <div class="sd-card-header">
      <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      Installment History
      <span style="font-weight:400; text-transform:none; letter-spacing:0; font-size:11px; color:var(--ink3)">
        ({{ $installments->count() }} records)
      </span>
    </div>

    <div class="sd-table-wrap">
      <table class="sd-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Fee Type</th>
            <th>Period</th>
            <th>Due Date</th>
            <th class="r">Amount (₹)</th>
            <th class="r">Paid (₹)</th>
            <th class="r">Remaining (₹)</th>
            <th>Status</th>
            <th>Payment Date</th>
            <th>Receipt No.</th>
          </tr>
        </thead>
        <tbody>
          @forelse($installments as $i => $inst)
          <tr class="{{ $inst['is_overdue'] ? 'row-overdue' : '' }}">
            <td class="mono" style="color:var(--ink3)">{{ $i + 1 }}</td>
            <td style="font-weight:500">{{ $inst['fee_type'] }}</td>
            <td style="color:var(--ink3)">{{ $inst['period'] ?: '—' }}</td>
            <td class="mono">{{ $inst['due_date'] ?? '—' }}</td>
            <td class="r">{{ number_format($inst['amount'], 2) }}</td>
            <td class="r" style="color:var(--green)">{{ number_format($inst['paid_amount'], 2) }}</td>
            <td class="r" style="color:{{ $inst['remaining'] > 0 ? 'var(--red)' : 'var(--ink3)' }}">
              {{ number_format($inst['remaining'], 2) }}
            </td>
            <td>
              @if($inst['is_overdue'])
                <span class="sd-badge sd-badge-overdue">Overdue</span>
              @elseif($inst['status'] === 'Paid')
                <span class="sd-badge sd-badge-paid">Paid</span>
              @elseif($inst['status'] === 'Partial')
                <span class="sd-badge sd-badge-partial">Partial</span>
              @else
                <span class="sd-badge sd-badge-pending">Pending</span>
              @endif
            </td>
            <td class="mono" style="color:var(--ink3)">{{ $inst['payment_date'] ?? '—' }}</td>
            <td class="mono" style="color:var(--ink3)">{{ $inst['receipt_number'] ?? '—' }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="10" style="padding:32px; text-align:center; color:var(--ink3);">
              No installment records found.
            </td>
          </tr>
          @endforelse
        </tbody>
        @if($installments->isNotEmpty())
        <tfoot>
          <tr>
            <td class="label" colspan="4">TOTAL</td>
            <td class="r">{{ number_format($totalDue, 2) }}</td>
            <td class="r" style="color:var(--green)">{{ number_format($totalPaid, 2) }}</td>
            <td class="r" style="color:var(--red)">{{ number_format($totalRemaining, 2) }}</td>
            <td colspan="3"></td>
          </tr>
        </tfoot>
        @endif
      </table>
    </div>

    {{-- Collection progress bar inside the card --}}
    @if($installments->isNotEmpty())
    <div class="sd-ring-wrap">
      <div style="display:flex; flex-direction:column;">
        <div class="sd-ring-pct">{{ $collectionPct }}%</div>
        <div class="sd-ring-label">collected</div>
      </div>
      <div class="sd-ring-bar">
        <div class="sd-ring-fill" style="width:{{ $collectionPct }}%"></div>
      </div>
      <div style="font-size:11.5px; color:var(--ink3); white-space:nowrap;">
        ₹{{ number_format($totalPaid, 2) }} of ₹{{ number_format($totalDue, 2) }}
      </div>
    </div>
    @endif
  </div>

  {{-- ── INVOICE / CHALLAN HISTORY ───────────────────────────────────── --}}
  <div class="sd-card">
    <div class="sd-card-header">
      <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
      Invoice / Challan History
      <span style="font-weight:400; text-transform:none; letter-spacing:0; font-size:11px; color:var(--ink3)">
        ({{ $invoices->count() }} records)
      </span>
    </div>

    <div class="sd-table-wrap">
      <table class="sd-table">
        <thead>
          <tr>
            <th>Invoice #</th>
            <th>Due Date</th>
            <th class="r">Amount (₹)</th>
            <th>Status</th>
            <th>Payment Date</th>
            <th>Bank / Method</th>
            <th>Adjustments</th>
          </tr>
        </thead>
        <tbody>
          @forelse($invoices as $inv)
          <tr>
            <td class="mono" style="font-weight:500">{{ $inv['invoice_number'] }}</td>
            <td class="mono" style="color:var(--ink3)">{{ $inv['due_date'] ?? '—' }}</td>
            <td class="r">{{ number_format($inv['amount'], 2) }}</td>
            <td>
              @if($inv['status'] === 'Paid')
                <span class="sd-badge sd-badge-inv-paid">Paid</span>
              @else
                <span class="sd-badge sd-badge-inv-unpaid">{{ $inv['status'] }}</span>
              @endif
            </td>
            <td class="mono" style="color:var(--ink3)">{{ $inv['paid_at'] ?? '—' }}</td>
            <td style="color:var(--ink3); font-size:12px;">{{ $inv['bank'] }}</td>
            <td>
              {{-- Extra charges / discounts as chips --}}
              @if($inv['late_fee'] > 0)
                <span class="sd-chip sd-chip-late">Late fee ₹{{ number_format($inv['late_fee'], 2) }}</span>
              @endif
              @if($inv['discount'] > 0)
                <span class="sd-chip sd-chip-disc">Discount –₹{{ number_format($inv['discount'], 2) }}</span>
              @endif
              @if($inv['other_charge_desc'] && $inv['other_charge_amount'] > 0)
                <span class="sd-chip sd-chip-other">{{ $inv['other_charge_desc'] }} ₹{{ number_format($inv['other_charge_amount'], 2) }}</span>
              @endif
              @if(!$inv['late_fee'] && !$inv['discount'] && !$inv['other_charge_desc'])
                <span style="color:var(--ink3); font-size:12px;">—</span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" style="padding:32px; text-align:center; color:var(--ink3);">
              No invoice records found.
            </td>
          </tr>
          @endforelse
        </tbody>
        @if($invoices->isNotEmpty())
        <tfoot>
          <tr>
            <td class="label" colspan="2">TOTAL</td>
            <td class="r">{{ number_format($invoices->sum('amount'), 2) }}</td>
            <td colspan="4"></td>
          </tr>
        </tfoot>
        @endif
      </table>
    </div>
  </div>

  {{-- ── CALCULATION LEGEND ───────────────────────────────────────────── --}}
  <div class="sd-no-print" style="margin-top:8px; padding:14px 18px; background:var(--surface2); border:1px solid var(--line); border-radius:var(--radius); font-size:11.5px; color:var(--ink3); line-height:1.8;">
    <strong style="color:var(--ink2);">How figures are calculated</strong> &nbsp;·&nbsp;
    <strong>Total Due</strong> = Σ installment amounts &nbsp;·&nbsp;
    <strong>Collected</strong> = Σ paid_amount across all installments &nbsp;·&nbsp;
    <strong>Outstanding</strong> = Σ remaining (pending → full amount · partial → amount − paid · paid → 0) &nbsp;·&nbsp;
    <strong>Collected + Outstanding = Total Due</strong> always ✓
  </div>

</div>
@endsection
