<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Fee Voucher - {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: #eef2f5;
            font-family: 'Segoe UI', 'Inter', 'DejaVu Sans', sans-serif;
            padding: 30px 20px;
        }
        /* Print & PDF friendly */
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            .copy-container {
                box-shadow: none;
                border: 1px solid #ccc;
                page-break-after: always;
                border-radius: 0;
            }
            .copy-container:last-child {
                page-break-after: auto;
            }
            .fee-table th {
                background: #f1f4f7 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
        .copy-container {
            max-width: 1000px;
            margin: 0 auto 40px auto;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 12px 28px rgba(0,0,0,0.08);
            overflow: hidden;
            page-break-after: always;
        }
        .copy-container:last-child {
            page-break-after: auto;
        }
        .copy-label {
            display: inline-block;
            background: #1e2a3e;
            color: white;
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 1px;
            padding: 6px 28px;
            border-radius: 0 0 16px 16px;
            text-transform: uppercase;
        }
        .voucher-inner {
            padding: 16px 28px 32px 28px;
        }
        /* Header without school name */
        .voucher-header {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            flex-wrap: wrap;
            border-bottom: 2px solid #eef2f8;
            padding-bottom: 18px;
            margin-bottom: 24px;
        }
        .title h2 {
            font-size: 28px;
            font-weight: 800;
            color: #1e2f3e;
            letter-spacing: -0.3px;
        }
        .title p {
            font-size: 13px;
            color: #5d7184;
        }
        .challan-box {
            background: #f8fafd;
            padding: 6px 18px;
            border-radius: 40px;
            text-align: right;
        }
        .challan-box .label {
            font-size: 12px;
            font-weight: 600;
            color: #566b82;
        }
        .challan-box .number {
            font-size: 18px;
            font-weight: 800;
            font-family: monospace;
            color: #0a2e42;
        }
        /* Info grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px,1fr));
            gap: 18px;
            background: #fafcff;
            border: 1px solid #e9edf2;
            border-radius: 20px;
            padding: 16px 22px;
            margin-bottom: 28px;
        }
        .info-item .label {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            color: #6f88a2;
            letter-spacing: 0.5px;
        }
        .info-item .value {
            font-size: 16px;
            font-weight: 600;
            color: #1f3e54;
            margin-top: 4px;
        }
        .section-title {
            font-size: 18px;
            font-weight: 700;
            margin: 24px 0 12px 0;
            border-left: 4px solid #2c7da0;
            padding-left: 14px;
            color: #1f3e54;
        }
        .fee-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }
        .fee-table th, .fee-table td {
            padding: 12px 18px;
            border-bottom: 1px solid #e9edf2;
            text-align: left;
        }
        .fee-table th {
            background: #f5f8fe;
            font-weight: 700;
            color: #1f4662;
        }
        .fee-table td:last-child, .fee-table th:last-child {
            text-align: right;
        }
        .total-row td {
            background: #fff7e8;
            font-weight: 800;
            border-top: 1px solid #ffe0a3;
        }
        .payment-row {
            display: flex;
            flex-wrap: wrap;
            gap: 24px;
            margin: 20px 0 24px;
        }
        .payment-col {
            flex: 1;
            min-width: 240px;
            background: #ffffff;
            border: 1px solid #eef3fb;
            border-radius: 20px;
            overflow: hidden;
        }
        .payment-col h4 {
            background: #f8fbfe;
            padding: 12px 18px;
            font-size: 15px;
            border-bottom: 1px solid #eaf0f6;
            color: #205072;
        }
        .bank-item, .wallet-item {
            padding: 12px 18px;
            border-bottom: 1px solid #f0f4fa;
        }
        .bank-name, .wallet-name {
            font-weight: 700;
            font-size: 14px;
        }
        .account-number {
            font-family: monospace;
            background: #f1f6fc;
            display: inline-block;
            padding: 4px 10px;
            border-radius: 30px;
            font-size: 12px;
            margin-top: 6px;
        }
        .signature-row {
            display: flex;
            justify-content: space-between;
            margin: 30px 0 20px;
            flex-wrap: wrap;
            gap: 20px;
        }
        .sign {
            width: 200px;
            text-align: center;
            border-top: 2px dashed #bfd6e8;
            padding-top: 12px;
            font-size: 13px;
            font-weight: 500;
            color: #4a6f8c;
        }
        .footer-note {
            text-align: center;
            font-size: 10px;
            color: #8196ab;
            border-top: 1px solid #ecf3f9;
            padding-top: 20px;
            margin-top: 10px;
        }
        .empty-message {
            color: #869fb3;
            padding: 12px 18px;
            font-style: italic;
            font-size: 13px;
        }
    </style>
</head>
<body>

@php
    // ---- NO SCHOOL NAME ANYWHERE ----
    $originalFee = ($installment->amount ?? $invoice->amount) + ($invoice->discount_amount ?? 0);
    $discount = $invoice->discount_amount ?? 0;
    $totalPayable = $invoice->amount;
    $feeDesc = ($installment->feeType->name ?? 'Tuition Fee') . ' (' . ucfirst($installment->feeType->period ?? 'Monthly') . ')';
    
    $studentName = $invoice->student->full_name ?? $invoice->student->name ?? 'Student Name';
    $className = $invoice->student->class->full_name ?? ($invoice->student->class->name ?? '—');
    $rollNo = $invoice->student->roll_number ?? '—';
    
    $issueDate = now()->format('d M, Y');
    $dueDate = $invoice->due_date ? $invoice->due_date->format('d M, Y') : '—';
    
    $copies = [
        'student' => 'STUDENT COPY',
        'bank'    => 'BANK COPY',
        'school'  => 'SCHOOL COPY'
    ];
@endphp

@foreach($copies as $copyKey => $copyLabel)
<div class="copy-container">
    <div class="voucher-inner">
        <div class="copy-label">{{ $copyLabel }}</div>

        <!-- HEADER (NO SCHOOL NAME) -->
        <div class="voucher-header">
            <div class="title">
                <h2>FEE VOUCHER</h2>
                <p>payment challan • electronic invoice</p>
            </div>
            <div class="challan-box">
                <div class="label">CHALLAN NO</div>
                <div class="number">{{ $invoice->invoice_number }}</div>
            </div>
        </div>

        <!-- INFO CARDS -->
        <div class="info-grid">
            <div class="info-item"><div class="label">📅 ISSUE DATE</div><div class="value">{{ $issueDate }}</div></div>
            <div class="info-item"><div class="label">⏰ DUE DATE</div><div class="value">{{ $dueDate }}</div></div>
            <div class="info-item"><div class="label">👤 STUDENT</div><div class="value">{{ $studentName }}</div></div>
            <div class="info-item"><div class="label">📌 CLASS / ROLL</div><div class="value">{{ $className }} @if($rollNo !== '—') | Roll: {{ $rollNo }} @endif</div></div>
        </div>

        <!-- FEE TABLE -->
        <div class="section-title">💰 FEE BREAKDOWN</div>
        <table class="fee-table">
            <thead><tr><th>Description</th><th>Amount (PKR)</th></tr></thead>
            <tbody>
                <tr><td>{{ $feeDesc }}</td><td>{{ number_format($originalFee, 2) }}</td></tr>
                @if($discount > 0)
                <tr><td>Discount Applied</td><td style="color:#2b6e3b;">- {{ number_format($discount, 2) }}</td></tr>
                @endif
                <tr class="total-row"><td><strong>TOTAL PAYABLE</strong></td><td><strong>PKR {{ number_format($totalPayable, 2) }}/-</strong></td></tr>
            </tbody>
        </table>

        <!-- BANKS + WALLETS -->
        <div class="payment-row">
            <div class="payment-col">
                <h4>🏛️ AUTHORIZED BANKS</h4>
                @forelse($allBanks as $bank)
                <div class="bank-item">
                    <div class="bank-name">{{ $bank->name }}</div>
                    @if($bank->account_number)<div class="account-number">Account: {{ $bank->account_number }}</div>@endif
                </div>
                @empty
                <div class="empty-message">No banks configured</div>
                @endforelse
            </div>
            <div class="payment-col">
                <h4>📱 MOBILE WALLETS</h4>
                @forelse($mobileWallets as $wallet)
                <div class="wallet-item">
                    <div class="wallet-name">{{ $wallet->name }}</div>
                    @if($wallet->account_number)<div class="account-number">{{ $wallet->account_number }}</div>@endif
                </div>
                @empty
                <div class="empty-message">No wallets configured</div>
                @endforelse
            </div>
        </div>

        <!-- SIGNATURES -->
        <div class="signature-row">
            <div class="sign">CASHIER</div>
            <div class="sign">AUTHORIZED SIGNATURE</div>
            <div class="sign">STUDENT / PARENT</div>
        </div>

        <div class="footer-note">
            This is a computer generated voucher – no physical signature required. | Challan #{{ $invoice->invoice_number }}
        </div>
    </div>
</div>
@endforeach

</body>
</html>