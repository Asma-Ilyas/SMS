@extends('layouts.app')
@section('title', 'Salary Management')

@section('content')
{{-- ============================================================
     SALARY INDEX  —  admin/salaries/index.blade.php
     Variables:
       $salaries    — paginated Salary models (with staff)
       $employees   — all Staff (for filter dropdown)
       $months      — distinct months (Y-m) in salaries table
       $summary     — object: total_count, total_basic, total_allowances,
                      total_deductions, total_net, total_paid, total_pending,
                      count_paid, count_pending
       $trend       — Collection [{month, total, paid, pending}] last 12 months
     ============================================================ --}}

<style>
  @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap');

  :root {
    --bg:       #f4f3f0;
    --surface:  #ffffff;
    --ink:      #131210;
    --ink2:     #44403c;
    --ink3:     #78716c;
    --line:     #e7e5e4;
    --line2:    #f5f5f4;
    --green:    #15803d;
    --green-bg: #dcfce7;
    --amber:    #b45309;
    --amber-bg: #fef3c7;
    --red:      #b91c1c;
    --red-bg:   #fee2e2;
    --blue:     #1d4ed8;
    --blue-bg:  #dbeafe;
    --slate:    #334155;
    --radius:   8px;
    --mono:     'JetBrains Mono', monospace;
    --display:  'Syne', sans-serif;
  }

  body { background: var(--bg); }

  .sm-page { font-family: var(--display); color: var(--ink); padding: 28px 24px; max-width: 1300px; margin: 0 auto; }

  /* ── header ── */
  .sm-header { display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 28px; }
  .sm-title  { font-size: 26px; font-weight: 800; letter-spacing: -.02em; }
  .sm-subtitle { font-size: 12px; color: var(--ink3); margin-top: 2px; font-weight: 400; }

  /* ── buttons ── */
  .sm-btn { display: inline-flex; align-items: center; gap: 7px; padding: 8px 16px; border-radius: var(--radius); font-family: var(--display); font-size: 12.5px; font-weight: 600; cursor: pointer; text-decoration: none; border: none; letter-spacing: .01em; transition: transform .1s, filter .15s; }
  .sm-btn:active { transform: scale(.97); }
  .sm-btn:hover  { filter: brightness(.9); }
  .sm-btn-primary { background: var(--ink); color: #fff; }
  .sm-btn-green   { background: var(--green); color: #fff; }
  .sm-btn-blue    { background: var(--blue); color: #fff; }
  .sm-btn-slate   { background: var(--slate); color: #fff; }
  .sm-btn-outline { background: var(--surface); color: var(--ink2); border: 1px solid var(--line); }

  /* ── alert ── */
  .sm-alert { padding: 12px 16px; border-radius: var(--radius); font-size: 13px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
  .sm-alert-success { background: var(--green-bg); color: var(--green); border: 1px solid #bbf7d0; }
  .sm-alert-error   { background: var(--red-bg);   color: var(--red);   border: 1px solid #fecaca; }

  /* ── summary cards ── */
  .sm-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 14px; margin-bottom: 24px; }
  .sm-card  { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); padding: 18px 20px; }
  .sm-card-label { font-size: 10.5px; font-weight: 700; color: var(--ink3); text-transform: uppercase; letter-spacing: .08em; margin-bottom: 6px; }
  .sm-card-val   { font-family: var(--mono); font-size: 22px; font-weight: 500; line-height: 1; }
  .sm-card-sub   { font-size: 11px; color: var(--ink3); margin-top: 5px; }
  .sm-card-bar   { height: 2px; background: var(--line); border-radius: 1px; margin-top: 10px; overflow: hidden; }
  .sm-card-fill  { height: 100%; border-radius: 1px; }

  /* ── chart ── */
  .sm-chart-wrap { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); padding: 20px; margin-bottom: 24px; }
  .sm-chart-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
  .sm-chart-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: var(--ink2); }
  .sm-chart-legend { display: flex; gap: 16px; font-size: 11px; color: var(--ink3); }
  .sm-chart-dot   { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 4px; }

  /* ── filter form ── */
  .sm-filter { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); padding: 16px 20px; margin-bottom: 20px; display: flex; flex-wrap: wrap; gap: 14px; align-items: flex-end; }
  .sm-filter label { font-size: 11px; font-weight: 700; color: var(--ink3); text-transform: uppercase; letter-spacing: .06em; display: block; margin-bottom: 5px; }
  .sm-filter select, .sm-filter input[type=text] {
    border: 1px solid var(--line); border-radius: 6px; padding: 7px 10px;
    font-family: var(--display); font-size: 12.5px; color: var(--ink);
    background: var(--line2); outline: none; min-width: 170px;
  }
  .sm-filter select:focus { border-color: var(--slate); }

  /* ── table ── */
  .sm-table-wrap { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); overflow: hidden; }
  .sm-table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
  .sm-table thead th { background: var(--line2); padding: 10px 14px; text-align: left; font-size: 10px; font-weight: 700; color: var(--ink3); text-transform: uppercase; letter-spacing: .07em; border-bottom: 1px solid var(--line); white-space: nowrap; }
  .sm-table thead th.r { text-align: right; }
  .sm-table tbody td { padding: 11px 14px; border-bottom: 1px solid var(--line2); vertical-align: middle; }
  .sm-table tbody tr:last-child td { border-bottom: none; }
  .sm-table tbody tr:hover td { background: var(--line2); }
  .sm-table td.r  { text-align: right; font-family: var(--mono); font-size: 12px; }
  .sm-table td.mono { font-family: var(--mono); font-size: 12px; }
  .sm-table tfoot td { padding: 10px 14px; background: #f0efec; font-weight: 600; font-family: var(--mono); font-size: 12px; border-top: 1px solid var(--line); }
  .sm-table tfoot .label { font-family: var(--display); font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: var(--ink2); }

  /* ── badges ── */
  .sm-badge { display: inline-block; font-size: 10px; font-weight: 700; padding: 2px 9px; border-radius: 20px; letter-spacing: .04em; }
  .sm-badge-paid    { background: var(--green-bg); color: var(--green); }
  .sm-badge-pending { background: var(--amber-bg); color: var(--amber); }
  .sm-badge-bank    { background: var(--blue-bg);  color: var(--blue);  }
  .sm-badge-cash    { background: var(--line2);     color: var(--ink2);  }
  .sm-badge-cheque  { background: #ede9fe;          color: #5b21b6;      }

  /* ── action icons ── */
  .sm-actions { display: flex; align-items: center; gap: 10px; }
  .sm-icon-btn { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; border: 1px solid var(--line); background: var(--surface); color: var(--ink2); cursor: pointer; text-decoration: none; font-size: 12px; transition: background .12s, color .12s; }
  .sm-icon-btn:hover { background: var(--ink); color: #fff; border-color: var(--ink); }
  .sm-icon-btn.danger:hover { background: var(--red); border-color: var(--red); color: #fff; }
  .sm-icon-btn.success:hover { background: var(--green); border-color: var(--green); color: #fff; }

  /* ── modal ── */
  .sm-modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.45); backdrop-filter: blur(2px); z-index: 50; align-items: center; justify-content: center; }
  .sm-modal-overlay.open { display: flex; }
  .sm-modal { background: var(--surface); border-radius: 12px; width: 460px; max-width: 95vw; border: 1px solid var(--line); box-shadow: 0 20px 60px rgba(0,0,0,.15); }
  .sm-modal-header { padding: 18px 24px; border-bottom: 1px solid var(--line); display: flex; align-items: center; justify-content: space-between; }
  .sm-modal-title  { font-size: 15px; font-weight: 700; }
  .sm-modal-body   { padding: 22px 24px; }
  .sm-modal-footer { padding: 16px 24px; border-top: 1px solid var(--line); display: flex; justify-content: flex-end; gap: 10px; }
  .sm-field { margin-bottom: 16px; }
  .sm-field label { font-size: 11px; font-weight: 700; color: var(--ink3); text-transform: uppercase; letter-spacing: .06em; display: block; margin-bottom: 5px; }
  .sm-field input, .sm-field select, .sm-field textarea {
    width: 100%; border: 1px solid var(--line); border-radius: 6px; padding: 8px 12px;
    font-family: var(--display); font-size: 13px; color: var(--ink);
    background: var(--line2); outline: none;
  }
  .sm-field input:focus, .sm-field select:focus { border-color: var(--slate); background: var(--surface); }
  .sm-field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
  .sm-error { font-size: 11px; color: var(--red); margin-top: 3px; }

  /* ── pagination ── */
  .sm-pagination { padding: 14px 20px; border-top: 1px solid var(--line); }

  /* ── empty ── */
  .sm-empty { padding: 56px 24px; text-align: center; color: var(--ink3); font-size: 13px; }
  .sm-empty-icon { font-size: 36px; margin-bottom: 10px; }

  @media (max-width: 768px) {
    .sm-cards { grid-template-columns: repeat(2, 1fr); }
    .sm-header { flex-direction: column; }
  }
  @media print {
    .sm-no-print { display: none !important; }
  }
</style>

<div class="sm-page">

  {{-- ── FLASH MESSAGES ─────────────────────────────────────────────────── --}}
  @if(session('success'))
  <div class="sm-alert sm-alert-success sm-no-print">
    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
    {{ session('success') }}
  </div>
  @endif
  @if($errors->any())
  <div class="sm-alert sm-alert-error sm-no-print">
    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    {{ $errors->first() }}
  </div>
  @endif

  {{-- ── PAGE HEADER ─────────────────────────────────────────────────────── --}}
  <div class="sm-header">
    <div>
      <div class="sm-title">Salary Management</div>
      <div class="sm-subtitle">
        {{ $summary->total_count ?? 0 }} records ·
        {{ $salaries->total() }} total matching
      </div>
    </div>
    <div class="sm-no-print" style="display:flex; gap:8px; flex-wrap:wrap;">
      <a href="{{ route('admin.salaries.create') }}" class="sm-btn sm-btn-primary">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Salary
      </a>
      <button onclick="openModal('payrollModal')" class="sm-btn sm-btn-green">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
        Generate Payroll
      </button>
      <a href="{{ route('admin.salaries.export', request()->query()) }}" class="sm-btn sm-btn-slate">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
        Export CSV
      </a>
      <button onclick="window.print()" class="sm-btn sm-btn-outline">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        Print
      </button>
    </div>
  </div>

  {{-- ── SUMMARY CARDS ───────────────────────────────────────────────────── --}}
  @php
    $totalNet     = $summary->total_net     ?? 0;
    $totalPaid    = $summary->total_paid    ?? 0;
    $totalPending = $summary->total_pending ?? 0;
    $paidPct      = $totalNet > 0 ? round($totalPaid / $totalNet * 100, 1) : 0;
    $pendingPct   = $totalNet > 0 ? round($totalPending / $totalNet * 100, 1) : 0;
  @endphp

  <div class="sm-cards">

    <div class="sm-card">
      <div class="sm-card-label">Total Payroll</div>
      <div class="sm-card-val" style="color:var(--ink)">₹{{ number_format($totalNet, 2) }}</div>
      <div class="sm-card-sub">{{ $summary->total_count ?? 0 }} salary records</div>
      <div class="sm-card-bar"><div class="sm-card-fill" style="width:100%; background:var(--line);"></div></div>
    </div>

    <div class="sm-card">
      <div class="sm-card-label">Total Basic</div>
      <div class="sm-card-val" style="color:var(--slate)">₹{{ number_format($summary->total_basic ?? 0, 2) }}</div>
      <div class="sm-card-sub">Before allowances</div>
      <div class="sm-card-bar"><div class="sm-card-fill" style="width:100%; background:#cbd5e1;"></div></div>
    </div>

    <div class="sm-card">
      <div class="sm-card-label">Total Allowances</div>
      <div class="sm-card-val" style="color:var(--blue)">₹{{ number_format($summary->total_allowances ?? 0, 2) }}</div>
      <div class="sm-card-sub">Added to basic</div>
      <div class="sm-card-bar"><div class="sm-card-fill" style="width:100%; background:var(--blue-bg);"></div></div>
    </div>

    <div class="sm-card">
      <div class="sm-card-label">Total Deductions</div>
      <div class="sm-card-val" style="color:var(--red)">₹{{ number_format($summary->total_deductions ?? 0, 2) }}</div>
      <div class="sm-card-sub">Subtracted from gross</div>
      <div class="sm-card-bar"><div class="sm-card-fill" style="width:100%; background:var(--red-bg);"></div></div>
    </div>

    <div class="sm-card">
      <div class="sm-card-label">Paid</div>
      <div class="sm-card-val" style="color:var(--green)">₹{{ number_format($totalPaid, 2) }}</div>
      <div class="sm-card-sub">{{ $summary->count_paid ?? 0 }} records · {{ $paidPct }}%</div>
      <div class="sm-card-bar"><div class="sm-card-fill" style="width:{{ $paidPct }}%; background:var(--green);"></div></div>
    </div>

    <div class="sm-card">
      <div class="sm-card-label">Pending</div>
      <div class="sm-card-val" style="color:var(--amber)">₹{{ number_format($totalPending, 2) }}</div>
      <div class="sm-card-sub">{{ $summary->count_pending ?? 0 }} records · {{ $pendingPct }}%</div>
      <div class="sm-card-bar"><div class="sm-card-fill" style="width:{{ $pendingPct }}%; background:var(--amber);"></div></div>
    </div>

  </div>

  {{-- ── MONTHLY TREND CHART ─────────────────────────────────────────────── --}}
  @if($trend->isNotEmpty())
  <div class="sm-chart-wrap sm-no-print">
    <div class="sm-chart-head">
      <div class="sm-chart-title">Monthly Payroll — Last 12 Months</div>
      <div class="sm-chart-legend">
        <span><span class="sm-chart-dot" style="background:var(--green)"></span>Paid</span>
        <span><span class="sm-chart-dot" style="background:var(--amber)"></span>Pending</span>
      </div>
    </div>
    <canvas id="trendChart" height="80"></canvas>
  </div>
  @endif

  {{-- ── FILTER FORM ─────────────────────────────────────────────────────── --}}
  <form method="GET" action="{{ route('admin.salaries.index') }}" class="sm-filter sm-no-print">
    <div>
      <label>Employee</label>
      <select name="employee_id">
        <option value="">All Employees</option>
        @foreach($employees as $emp)
          <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
            {{ $emp->first_name }} {{ $emp->last_name }}
          </option>
        @endforeach
      </select>
    </div>
    <div>
      <label>Month</label>
      <select name="month">
        <option value="">All Months</option>
        @foreach($months as $m)
          <option value="{{ $m->month }}" {{ request('month') == $m->month ? 'selected' : '' }}>
            {{ \Carbon\Carbon::createFromFormat('Y-m', $m->month)->format('F Y') }}
          </option>
        @endforeach
      </select>
    </div>
    <div>
      <label>Status</label>
      <select name="status">
        <option value="">All Statuses</option>
        <option value="paid"    {{ request('status') == 'paid'    ? 'selected' : '' }}>Paid</option>
        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
      </select>
    </div>
    <div style="display:flex; gap:8px;">
      <button type="submit" class="sm-btn sm-btn-primary">Apply</button>
      <a href="{{ route('admin.salaries.index') }}" class="sm-btn sm-btn-outline">Reset</a>
    </div>
  </form>

  {{-- ── SALARY TABLE ─────────────────────────────────────────────────────── --}}
  <div class="sm-table-wrap">
    <div class="sm-table-wrap" style="overflow-x:auto;">
      <table class="sm-table">
        <thead>
          <tr>
            <th>Employee</th>
            <th>Month</th>
            <th class="r">Basic (₹)</th>
            <th class="r">Allowances (₹)</th>
            <th class="r">Gross (₹)</th>
            <th class="r">Deductions (₹)</th>
            <th class="r">Net Salary (₹)</th>
            <th>Payment Date</th>
            <th>Method</th>
            <th>Status</th>
            <th class="sm-no-print" style="text-align:center">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($salaries as $salary)
          @php
            $gross = $salary->basic_salary + $salary->allowances;
          @endphp
          <tr>
            <td>
              <div style="font-weight:600; font-size:13px;">
                {{ $salary->staff->first_name }} {{ $salary->staff->last_name }}
              </div>
              <div style="font-size:11px; color:var(--ink3)">
                {{ $salary->staff->designation ?? '' }}
              </div>
            </td>
            <td class="mono">
              {{ \Carbon\Carbon::createFromFormat('Y-m', $salary->month)->format('M Y') }}
            </td>
            <td class="r">{{ number_format($salary->basic_salary, 2) }}</td>
            <td class="r" style="color:var(--blue)">{{ number_format($salary->allowances, 2) }}</td>
            <td class="r" style="font-weight:500">{{ number_format($gross, 2) }}</td>
            <td class="r" style="color:var(--red)">{{ number_format($salary->deductions, 2) }}</td>
            <td class="r" style="font-weight:700; font-size:13px;">
              {{ number_format($salary->net_salary, 2) }}
            </td>
            <td class="mono" style="color:var(--ink3)">
              {{ \Carbon\Carbon::parse($salary->payment_date)->format('d M Y') }}
            </td>
            <td>
              <span class="sm-badge sm-badge-{{ $salary->payment_method }}">
                {{ ucfirst($salary->payment_method) }}
              </span>
            </td>
            <td>
              <span class="sm-badge sm-badge-{{ $salary->payment_status }}">
                {{ ucfirst($salary->payment_status) }}
              </span>
            </td>
            <td class="sm-no-print" style="text-align:center;">
              <div class="sm-actions" style="justify-content:center;">
                <a href="{{ route('admin.salaries.show', $salary) }}" class="sm-icon-btn" title="View">
                  <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </a>
                <a href="{{ route('admin.salaries.edit', $salary) }}" class="sm-icon-btn" title="Edit">
                  <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </a>
                @if($salary->payment_status === 'pending')
                <form action="{{ route('admin.salaries.mark-paid', $salary) }}" method="POST" class="inline">
                  @csrf @method('PATCH')
                  <button type="submit" class="sm-icon-btn success" title="Mark as Paid"
                    onclick="return confirm('Mark this salary as paid?')">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
                  </button>
                </form>
                @endif
                <form action="{{ route('admin.salaries.destroy', $salary) }}" method="POST" class="inline"
                  onsubmit="return confirm('Delete this salary record?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="sm-icon-btn danger" title="Delete">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="11">
              <div class="sm-empty">
                <div class="sm-empty-icon">💼</div>
                <div style="font-weight:600; margin-bottom:4px;">No salary records found</div>
                <div>Generate payroll or add a record manually.</div>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>

        {{-- ── TOTALS FOOTER ── --}}
        @if($salaries->count() > 0)
        @php
          $pgGross = $salaries->sum(fn($s) => $s->basic_salary + $s->allowances);
        @endphp
        <tfoot>
          <tr>
            <td class="label" colspan="2">PAGE SUBTOTAL</td>
            <td class="r">{{ number_format($salaries->sum('basic_salary'), 2) }}</td>
            <td class="r" style="color:var(--blue)">{{ number_format($salaries->sum('allowances'), 2) }}</td>
            <td class="r">{{ number_format($pgGross, 2) }}</td>
            <td class="r" style="color:var(--red)">{{ number_format($salaries->sum('deductions'), 2) }}</td>
            <td class="r" style="font-weight:700">{{ number_format($salaries->sum('net_salary'), 2) }}</td>
            <td colspan="4"></td>
          </tr>
        </tfoot>
        @endif
      </table>
    </div>

    {{-- Pagination --}}
    <div class="sm-pagination">
      {{ $salaries->withQueryString()->links() }}
    </div>
  </div>

</div>

{{-- ══════════════════════════════════════════════════════════════════════════
     GENERATE PAYROLL MODAL
     ══════════════════════════════════════════════════════════════════════════ --}}
<div id="payrollModal" class="sm-modal-overlay sm-no-print">
  <div class="sm-modal">
    <div class="sm-modal-header">
      <div class="sm-modal-title">Generate Payroll</div>
      <button onclick="closeModal('payrollModal')" style="background:none;border:none;cursor:pointer;color:var(--ink3);">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <form action="{{ route('admin.salaries.generate-payroll') }}" method="POST">
      @csrf
      <div class="sm-modal-body">
        <p style="font-size:12.5px; color:var(--ink3); margin-bottom:18px;">
          Creates salary records for <strong>all staff</strong> using their stored basic salary. Skips staff who already have a record for the selected month.
        </p>
        <div class="sm-field-row">
          <div class="sm-field">
            <label>Month</label>
            <input type="month" name="month" required value="{{ now()->format('Y-m') }}">
          </div>
          <div class="sm-field">
            <label>Payment Date</label>
            <input type="date" name="payment_date" required value="{{ now()->format('Y-m-d') }}">
          </div>
        </div>
        <div class="sm-field-row">
          <div class="sm-field">
            <label>Payment Method</label>
            <select name="payment_method" required>
              <option value="bank">Bank Transfer</option>
              <option value="cash">Cash</option>
              <option value="cheque">Cheque</option>
            </select>
          </div>
          <div class="sm-field">
            <label>Initial Status</label>
            <select name="payment_status" required>
              <option value="pending">Pending</option>
              <option value="paid">Paid</option>
            </select>
          </div>
        </div>
      </div>
      <div class="sm-modal-footer">
        <button type="button" onclick="closeModal('payrollModal')" class="sm-btn sm-btn-outline">Cancel</button>
        <button type="submit" class="sm-btn sm-btn-green">Generate Payroll</button>
      </div>
    </form>
  </div>
</div>

{{-- ── Chart.js + modal JS ──────────────────────────────────────────────── --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script>
  // Modal helpers
  function openModal(id)  { document.getElementById(id).classList.add('open'); }
  function closeModal(id) { document.getElementById(id).classList.remove('open'); }
  document.querySelectorAll('.sm-modal-overlay').forEach(el => {
    el.addEventListener('click', e => { if (e.target === el) el.classList.remove('open'); });
  });

  // Monthly trend chart
  @if($trend->isNotEmpty())
  (function() {
    const labels  = {!! $trend->pluck('month')->toJson() !!};
    const paid    = {!! $trend->pluck('paid')->toJson() !!};
    const pending = {!! $trend->pluck('pending')->toJson() !!};

    new Chart(document.getElementById('trendChart'), {
      type: 'bar',
      data: {
        labels,
        datasets: [
          {
            label: 'Paid',
            data: paid,
            backgroundColor: '#16a34a',
            borderRadius: 4,
            stack: 'a',
          },
          {
            label: 'Pending',
            data: pending,
            backgroundColor: '#fcd34d',
            borderRadius: 4,
            stack: 'a',
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: ctx => ' ₹' + ctx.raw.toLocaleString('en-IN', {minimumFractionDigits: 0})
            }
          }
        },
        scales: {
          x: { grid: { display: false }, ticks: { font: { size: 11, family: 'JetBrains Mono' } } },
          y: {
            grid: { color: '#f0efec' },
            ticks: {
              font: { size: 11, family: 'JetBrains Mono' },
              callback: v => '₹' + (v >= 1000 ? (v/1000).toFixed(0) + 'k' : v)
            }
          }
        }
      }
    });
  })();
  @endif
</script>
@endsection