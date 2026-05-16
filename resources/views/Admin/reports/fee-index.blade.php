@extends('layouts.app')

@section('content')

{{-- ============================================================
     FEE REPORTS — fee-index.blade.php
     Variables from controller (all read-only, no queries here):
       $classes, $sections, $selectedClassId, $selectedSection
       $totalDue, $totalPaid, $totalOutstanding, $totalOverdue, $collectionRate
       $reportData  (array of class breakdown rows)
       $studentRows (Collection — only present when class is selected)
     ============================================================ --}}

<style>
  @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap');

  :root {
    --ink:      #0d1117;
    --ink2:     #3d4451;
    --ink3:     #6b7280;
    --line:     #e5e7eb;
    --line2:    #f3f4f6;
    --surface:  #ffffff;
    --surface2: #f9fafb;
    --green:    #16a34a;
    --green-bg: #f0fdf4;
    --red:      #dc2626;
    --red-bg:   #fef2f2;
    --amber:    #d97706;
    --amber-bg: #fffbeb;
    --blue:     #2563eb;
    --blue-bg:  #eff6ff;
    --indigo:   #4f46e5;
    --radius:   10px;
    --mono:     'DM Mono', monospace;
    --sans:     'DM Sans', sans-serif;
  }

  .fr-wrap  { font-family: var(--sans); color: var(--ink); }

  /* ── cards ── */
  .fr-card  { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); }
  .fr-card-header { padding: 14px 20px; border-bottom: 1px solid var(--line); font-size: 13px; font-weight: 600; color: var(--ink2); letter-spacing: .03em; text-transform: uppercase; display: flex; align-items: center; gap: 8px; }

  /* ── summary grid ── */
  .fr-summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 14px; margin-bottom: 28px; }
  .fr-stat { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); padding: 18px 20px; display: flex; flex-direction: column; gap: 6px; }
  .fr-stat-label { font-size: 11.5px; font-weight: 500; color: var(--ink3); letter-spacing: .04em; text-transform: uppercase; }
  .fr-stat-val { font-family: var(--mono); font-size: 22px; font-weight: 500; line-height: 1; }
  .fr-stat-sub { font-size: 11.5px; color: var(--ink3); }
  .fr-stat-bar { height: 3px; border-radius: 2px; background: var(--line); margin-top: 6px; overflow: hidden; }
  .fr-stat-bar-fill { height: 100%; border-radius: 2px; transition: width .6s ease; }

  /* ── tables ── */
  .fr-table-wrap { overflow-x: auto; }
  .fr-table { width: 100%; border-collapse: collapse; font-size: 13px; }
  .fr-table thead th { background: var(--surface2); padding: 10px 16px; text-align: left; font-size: 11px; font-weight: 600; color: var(--ink3); letter-spacing: .05em; text-transform: uppercase; border-bottom: 1px solid var(--line); white-space: nowrap; }
  .fr-table thead th.right { text-align: right; }
  .fr-table tbody td { padding: 11px 16px; border-bottom: 1px solid var(--line2); vertical-align: middle; }
  .fr-table tbody tr:last-child td { border-bottom: none; }
  .fr-table tbody tr:hover td { background: var(--surface2); }
  .fr-table td.right { text-align: right; font-family: var(--mono); font-size: 12.5px; }
  .fr-table td.mono  { font-family: var(--mono); font-size: 12.5px; }

  /* ── progress bar in table ── */
  .fr-prog { display: inline-flex; align-items: center; gap: 7px; }
  .fr-prog-track { width: 70px; height: 4px; background: var(--line); border-radius: 2px; overflow: hidden; flex-shrink: 0; }
  .fr-prog-fill  { height: 100%; border-radius: 2px; background: var(--green); }

  /* ── badges ── */
  .fr-badge { display: inline-block; font-size: 10.5px; font-weight: 600; padding: 2px 8px; border-radius: 20px; letter-spacing: .02em; white-space: nowrap; }
  .fr-badge-paid    { background: #dcfce7; color: #15803d; }
  .fr-badge-partial { background: #fef9c3; color: #854d0e; }
  .fr-badge-pending { background: #fee2e2; color: #b91c1c; }
  .fr-badge-overdue { background: #fde8d8; color: #9a3412; }

  /* ── action link ── */
  .fr-link { font-size: 12px; color: var(--blue); text-decoration: none; font-weight: 500; }
  .fr-link:hover { text-decoration: underline; }

  /* ── filter form ── */
  .fr-form { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); padding: 18px 20px; margin-bottom: 24px; display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end; }
  .fr-form label { display: block; font-size: 11.5px; font-weight: 600; color: var(--ink3); text-transform: uppercase; letter-spacing: .04em; margin-bottom: 5px; }
  .fr-form select, .fr-form input[type=text] {
    border: 1px solid var(--line); border-radius: 7px; padding: 7px 12px;
    font-family: var(--sans); font-size: 13px; color: var(--ink);
    background: var(--surface2); outline: none; min-width: 160px;
  }
  .fr-form select:focus, .fr-form input:focus { border-color: var(--indigo); box-shadow: 0 0 0 3px rgba(79,70,229,.08); }

  /* ── buttons ── */
  .fr-btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 7px; font-family: var(--sans); font-size: 12.5px; font-weight: 500; cursor: pointer; text-decoration: none; border: none; transition: filter .15s; }
  .fr-btn:hover { filter: brightness(.93); }
  .fr-btn-primary  { background: var(--indigo); color: #fff; }
  .fr-btn-gray     { background: var(--line); color: var(--ink2); }
  .fr-btn-green    { background: var(--green); color: #fff; }
  .fr-btn-slate    { background: #334155; color: #fff; }

  /* ── page header ── */
  .fr-header { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 22px; }
  .fr-title  { font-size: 22px; font-weight: 600; color: var(--ink); }
  .fr-subtitle { font-size: 12px; color: var(--ink3); margin-top: 2px; }

  /* ── section heading ── */
  .fr-section-head { font-size: 13px; font-weight: 600; color: var(--ink2); text-transform: uppercase; letter-spacing: .05em; margin-bottom: 10px; display: flex; align-items: center; gap: 8px; }

  /* ── search ── */
  #studentSearch { min-width: 220px; }

  /* ── empty state ── */
  .fr-empty { padding: 36px; text-align: center; color: var(--ink3); font-size: 13px; }

  /* ── print styles ── */
  @media print {
    .fr-no-print { display: none !important; }
    .fr-card { break-inside: avoid; }
  }
</style>

<div class="fr-wrap" style="max-width: 1200px; margin: 0 auto; padding: 24px 20px;">

  {{-- ── PAGE HEADER ──────────────────────────────────────────────────── --}}
  <div class="fr-header">
    <div>
      <div class="fr-title">Fee Reports</div>
      <div class="fr-subtitle">All figures sourced from installment records · amounts in ₹</div>
    </div>
    <div class="fr-no-print" style="display:flex; gap:8px; flex-wrap:wrap;">
      <button onclick="exportCSV()" class="fr-btn fr-btn-green">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
        Export CSV
      </button>
      <button onclick="window.print()" class="fr-btn fr-btn-slate">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        Print
      </button>
    </div>
  </div>

  {{-- ── FILTER FORM ──────────────────────────────────────────────────── --}}
  <form method="GET" action="{{ route('admin.fee-reports.index') }}" id="filterForm" class="fr-form fr-no-print">
    <div>
      <label for="classSelect">Class</label>
      <select name="class_id" id="classSelect">
        <option value="">All Classes</option>
        @foreach($classes as $class)
          <option value="{{ $class->id }}" {{ $selectedClassId == $class->id ? 'selected' : '' }}>
            {{ $class->full_name }}
          </option>
        @endforeach
      </select>
    </div>
    <div>
      <label for="sectionSelect">Section</label>
      <select name="section" id="sectionSelect" {{ !$selectedClassId ? 'disabled' : '' }}>
        <option value="">All Sections</option>
        @foreach($sections as $sec)
          <option value="{{ $sec }}" {{ $selectedSection == $sec ? 'selected' : '' }}>{{ $sec }}</option>
        @endforeach
      </select>
    </div>
    @if($selectedClassId && $studentRows->isNotEmpty())
    <div class="fr-no-print">
      <label for="studentSearch">Search student</label>
      <input type="text" id="studentSearch" placeholder="Name or roll no…">
    </div>
    @endif
    <div style="display:flex; gap:8px;">
      <button type="submit" class="fr-btn fr-btn-primary">Apply</button>
      <a href="{{ route('admin.fee-reports.index') }}" class="fr-btn fr-btn-gray">Reset</a>
    </div>
  </form>

  {{-- ── SUMMARY CARDS ─────────────────────────────────────────────────── --}}
  <div class="fr-summary">
    {{-- Total Due --}}
    <div class="fr-stat">
      <div class="fr-stat-label">Total Fee Liability</div>
      <div class="fr-stat-val" style="color:var(--ink)">₹{{ number_format($totalDue, 2) }}</div>
      <div class="fr-stat-sub">Full amount assigned to students</div>
      <div class="fr-stat-bar"><div class="fr-stat-bar-fill" style="width:100%; background:#d1d5db;"></div></div>
    </div>

    {{-- Collected --}}
    <div class="fr-stat">
      <div class="fr-stat-label">Collected</div>
      <div class="fr-stat-val" style="color:var(--green)">₹{{ number_format($totalPaid, 2) }}</div>
      <div class="fr-stat-sub">{{ $collectionRate }}% collection rate</div>
      <div class="fr-stat-bar"><div class="fr-stat-bar-fill" style="width:{{ $collectionRate }}%; background:var(--green);"></div></div>
    </div>

    {{-- Outstanding --}}
    <div class="fr-stat">
      <div class="fr-stat-label">Outstanding</div>
      <div class="fr-stat-val" style="color:var(--red)">₹{{ number_format($totalOutstanding, 2) }}</div>
      <div class="fr-stat-sub">Pending + partial balance</div>
      @php $outPct = $totalDue > 0 ? round($totalOutstanding/$totalDue*100,1) : 0; @endphp
      <div class="fr-stat-bar"><div class="fr-stat-bar-fill" style="width:{{ $outPct }}%; background:var(--red);"></div></div>
    </div>

    {{-- Overdue --}}
    <div class="fr-stat">
      <div class="fr-stat-label">Overdue</div>
      <div class="fr-stat-val" style="color:var(--amber)">₹{{ number_format($totalOverdue, 2) }}</div>
      <div class="fr-stat-sub">Due date passed, still unpaid</div>
      @php $odPct = $totalDue > 0 ? round($totalOverdue/$totalDue*100,1) : 0; @endphp
      <div class="fr-stat-bar"><div class="fr-stat-bar-fill" style="width:{{ $odPct }}%; background:var(--amber);"></div></div>
    </div>
  </div>

  {{-- ── CLASS / SECTION BREAKDOWN TABLE ─────────────────────────────── --}}
  <div class="fr-card" style="margin-bottom:28px;">
    <div class="fr-card-header">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
      Class / Section Breakdown
    </div>
    <div class="fr-table-wrap">
      <table class="fr-table">
        <thead>
          <tr>
            <th>Class</th>
            <th>Section</th>
            <th class="right">Students</th>
            <th class="right">Total Due (₹)</th>
            <th class="right">Collected (₹)</th>
            <th class="right">Outstanding (₹)</th>
            <th>Collection %</th>
            <th class="fr-no-print"></th>
          </tr>
        </thead>
        <tbody>
          @forelse($reportData as $row)
          <tr>
            <td style="font-weight:500;">{{ $row['class'] }}</td>
            <td>{{ $row['section'] }}</td>
            <td class="right mono">{{ $row['students'] }}</td>
            <td class="right">₹{{ number_format($row['total_due'], 2) }}</td>
            <td class="right" style="color:var(--green)">₹{{ number_format($row['paid'], 2) }}</td>
            <td class="right" style="color:var(--red)">₹{{ number_format($row['outstanding'], 2) }}</td>
            <td>
              <div class="fr-prog">
                <div class="fr-prog-track">
                  <div class="fr-prog-fill" style="width:{{ $row['collection_pct'] }}%"></div>
                </div>
                <span class="mono" style="font-size:11.5px; color:var(--ink3)">{{ $row['collection_pct'] }}%</span>
              </div>
            </td>
            <td class="fr-no-print">
              <a class="fr-link" href="{{ route('admin.fee-reports.index', [
                  'class_id' => $row['class_id'],
                  'section'  => $row['section'] !== 'All Sections' ? $row['section'] : '',
                ]) }}">
                View Students →
              </a>
            </td>
          </tr>
          @empty
          <tr><td colspan="8"><div class="fr-empty">No class data found.</div></td></tr>
          @endforelse
        </tbody>
        {{-- Totals footer --}}
        @if(count($reportData) > 1)
        @php
          $ftDue  = collect($reportData)->sum('total_due');
          $ftPaid = collect($reportData)->sum('paid');
          $ftOut  = collect($reportData)->sum('outstanding');
          $ftPct  = $ftDue > 0 ? round($ftPaid/$ftDue*100,1) : 0;
        @endphp
        <tfoot>
          <tr style="background:var(--surface2); font-weight:600; font-size:12px;">
            <td colspan="3" style="padding:10px 16px; color:var(--ink2);">TOTAL (filtered)</td>
            <td class="right">₹{{ number_format($ftDue, 2) }}</td>
            <td class="right" style="color:var(--green)">₹{{ number_format($ftPaid, 2) }}</td>
            <td class="right" style="color:var(--red)">₹{{ number_format($ftOut, 2) }}</td>
            <td colspan="2" style="padding:10px 16px; font-family:var(--mono); font-size:11.5px; color:var(--ink3)">{{ $ftPct }}%</td>
          </tr>
        </tfoot>
        @endif
      </table>
    </div>
  </div>

  {{-- ── STUDENT LIST ──────────────────────────────────────────────────── --}}
  @if($selectedClassId)

    @if($studentRows->isNotEmpty())
    <div class="fr-card">
      <div class="fr-card-header">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
        Students —
        {{ $reportData[0]['class'] ?? '' }}
        {{ $selectedSection ? ' · Section '.$selectedSection : ' · All Sections' }}
        <span style="font-weight:400; color:var(--ink3); font-size:12px; text-transform:none; letter-spacing:0">
          ({{ $studentRows->count() }} students)
        </span>
      </div>
      <div class="fr-table-wrap">
        <table class="fr-table" id="studentTable">
          <thead>
            <tr>
              <th>Name</th>
              <th>Sec</th>
              <th>Roll No</th>
              <th class="right">Total Due (₹)</th>
              <th class="right">Collected (₹)</th>
              <th class="right">Outstanding (₹)</th>
              <th>Status</th>
              <th>Coll %</th>
              <th>Last Payment</th>
              <th class="fr-no-print"></th>
            </tr>
          </thead>
          <tbody id="studentTbody">
            @foreach($studentRows as $row)
            <tr class="student-row">
              <td style="font-weight:500;">{{ $row['full_name'] }}</td>
              <td>{{ $row['section'] }}</td>
              <td class="mono">{{ $row['roll_number'] }}</td>
              <td class="right">₹{{ number_format($row['total_due'], 2) }}</td>
              <td class="right" style="color:var(--green)">₹{{ number_format($row['total_paid'], 2) }}</td>
              <td class="right" style="color:{{ $row['outstanding'] > 0 ? 'var(--red)' : 'var(--ink3)' }}">
                ₹{{ number_format($row['outstanding'], 2) }}
              </td>
              <td>
                @if($row['pay_status'] === 'paid')
                  <span class="fr-badge fr-badge-paid">Paid</span>
                @elseif($row['pay_status'] === 'partial')
                  <span class="fr-badge fr-badge-partial">Partial</span>
                @else
                  <span class="fr-badge fr-badge-pending">Pending</span>
                @endif
              </td>
              <td>
                <div class="fr-prog">
                  <div class="fr-prog-track">
                    <div class="fr-prog-fill" style="width:{{ $row['collection_pct'] }}%"></div>
                  </div>
                  <span class="mono" style="font-size:11px; color:var(--ink3)">{{ $row['collection_pct'] }}%</span>
                </div>
              </td>
              <td style="font-size:12px; color:var(--ink3)">{{ $row['last_payment'] ?? '—' }}</td>
              <td class="fr-no-print">
                <a class="fr-link" href="{{ route('admin.fee-reports.student', $row['id']) }}">Details →</a>
              </td>
            </tr>
            @endforeach
          </tbody>
          {{-- Student subtotal footer --}}
          @php
            $stDue  = $studentRows->sum('total_due');
            $stPaid = $studentRows->sum('total_paid');
            $stOut  = $studentRows->sum('outstanding');
            $stPct  = $stDue > 0 ? round($stPaid/$stDue*100,1) : 0;
          @endphp
          <tfoot>
            <tr style="background:var(--surface2); font-weight:600; font-size:12px;">
              <td colspan="3" style="padding:10px 16px; color:var(--ink2);">SUBTOTAL</td>
              <td class="right">₹{{ number_format($stDue, 2) }}</td>
              <td class="right" style="color:var(--green)">₹{{ number_format($stPaid, 2) }}</td>
              <td class="right" style="color:var(--red)">₹{{ number_format($stOut, 2) }}</td>
              <td></td>
              <td style="padding:10px 16px; font-family:var(--mono); font-size:11.5px; color:var(--ink3)">{{ $stPct }}%</td>
              <td colspan="2"></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    @else
    <div class="fr-card" style="padding:28px; text-align:center; color:var(--ink3); font-size:13px;">
      No students found for the selected class / section.
    </div>
    @endif

  @endif

</div>

{{-- ── JS ── --}}
<script>
  // Auto-submit on class change; enable/disable section
  const cs = document.getElementById('classSelect');
  const ss = document.getElementById('sectionSelect');
  if (cs) cs.addEventListener('change', () => {
    ss && (ss.disabled = !cs.value);
    document.getElementById('filterForm').submit();
  });
  if (ss) ss.addEventListener('change', () => document.getElementById('filterForm').submit());

  // Live search on student table
  const search = document.getElementById('studentSearch');
  if (search) {
    search.addEventListener('input', function () {
      const term = this.value.trim().toLowerCase();
      document.querySelectorAll('.student-row').forEach(tr => {
        const name = tr.cells[0].innerText.toLowerCase();
        const roll = tr.cells[2].innerText.toLowerCase();
        tr.style.display = (name.includes(term) || roll.includes(term)) ? '' : 'none';
      });
    });
  }

  // CSV export
  function exportCSV() {
    const rows = document.querySelectorAll('#studentTable tbody tr.student-row');
    if (!rows.length) {
      // Fall back to class breakdown
      const tbl = document.querySelector('.fr-table');
      let csv = 'Class,Section,Students,Total Due,Collected,Outstanding,Collection %\n';
      tbl && tbl.querySelectorAll('tbody tr').forEach(tr => {
        const c = [...tr.cells];
        if (c.length < 7) return;
        csv += [0,1,2,3,4,5,6].map(i => `"${c[i]?.innerText.replace(/[₹,]/g,'').trim()}"`).join(',') + '\n';
      });
      return dl(csv, 'fee_class_report.csv');
    }
    let csv = 'Name,Section,Roll No,Total Due,Collected,Outstanding,Status,Collection %,Last Payment\n';
    rows.forEach(tr => {
      const c = [...tr.cells];
      csv += [0,1,2,3,4,5,6,7,8].map(i => `"${c[i]?.innerText.replace(/[₹,\n]/g,'').trim()}"`).join(',') + '\n';
    });
    dl(csv, 'fee_student_report.csv');
  }
  function dl(csv, name) {
    const a = document.createElement('a');
    a.href = URL.createObjectURL(new Blob([csv], {type:'text/csv'}));
    a.download = name;
    a.click();
    URL.revokeObjectURL(a.href);
  }
</script>
@endsection