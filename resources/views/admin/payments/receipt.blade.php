<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="utf-8">
    <title>Payment Receipt - {{ $payment->payment_number }}</title>
    <style>
        @media print {
            .page {
                margin: 0;
                box-shadow: none;
            }
        }
        @page {
            margin: 0;
            size: A4;
        }
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', 'Arial', 'Helvetica', sans-serif;
            font-size: 12px;
            color: #000;
            background: #fff;
        }
        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            position: relative;
            overflow: hidden;
        }
        /* Header with diagonal design */
        .header {
            position: relative;
            height: 130px;
            margin-bottom: 25px;
            overflow: visible;
        }
        .header-red {
            position: absolute;
            top: 0;
            left: 0;
            width: 200px;
            height: 100%;
            background: #dc3545;
            z-index: 1;
        }
        .header-black {
            position: absolute;
            top: 0;
            left: 200px;
            right: 0;
            height: 100%;
            background: #212529;
            z-index: 1;
        }
        .header-content {
            position: relative;
            z-index: 3;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 100%;
        }
        .company-info {
            color: #fff;
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 4;
        }
        .company-logo-box {
            width: 60px;
            height: 60px;
            background: transparent;
            border-radius: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .company-logo-box img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .receipt-title {
            color: #fff;
            font-size: 58px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 6px;
            position: absolute;
            right: 40px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 4;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        /* Main content */
        .content {
            padding: 0 30px;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            gap: 20px;
        }
        .info-box {
            width: 48%;
            flex: 1;
        }
        .info-label {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 8px;
            color: #212529;
        }
        .customer-name {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 5px;
        }
        .customer-details {
            font-size: 11px;
            color: #495057;
            line-height: 1.6;
        }
        .payment-details {
            text-align: right;
        }
        .payment-number-box {
            background: #212529;
            color: #fff;
            padding: 12px 18px;
            display: inline-block;
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 12px;
            border-radius: 2px;
        }
        .detail-row {
            font-size: 11px;
            margin-bottom: 5px;
            color: #495057;
        }
        /* Payment details table */
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .payment-table thead tr th {
            padding: 14px 12px;
            text-align: left;
            font-weight: bold;
            font-size: 12px;
            color: #fff;
        }
        .payment-table thead tr th:nth-child(1) {
            background: #dc3545;
            width: 50%;
        }
        .payment-table thead tr th:nth-child(2) {
            background: #212529;
            width: 50%;
        }
        .payment-table tbody tr td {
            padding: 14px 12px;
            border-bottom: 1px solid #e9ecef;
            font-size: 11px;
        }
        .payment-table tbody tr:last-child td {
            border-bottom: none;
        }
        .payment-table tbody tr td:first-child {
            font-weight: 500;
        }
        .text-right {
            text-align: right;
        }
        /* Amount section */
        .amount-section {
            margin-bottom: 30px;
        }
        .amount-box {
            background: #28a745;
            color: #fff;
            padding: 25px 30px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            border-radius: 2px;
        }
        .amount-label {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 8px;
        }
        .amount-value {
            font-weight: bold;
            font-size: 32px;
        }
        /* Payment summary */
        .summary-section {
            margin-bottom: 20px;
        }
        .summary-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 4px;
            border-left: 4px solid #007bff;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 12px;
        }
        .summary-row:last-child {
            margin-bottom: 0;
            padding-top: 10px;
            border-top: 1px solid #dee2e6;
            font-weight: bold;
        }
        /* Footer */
        .footer {
            position: relative;
            margin-top: 30px;
            padding: 20px 30px;
            border-top: 1px solid #e9ecef;
            min-height: 100px;
        }
        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .contact-info {
            display: flex;
            gap: 12px;
            font-size: 10px;
            color: #6c757d;
            align-items: center;
            flex-wrap: wrap;
        }
        .contact-info span {
            white-space: nowrap;
        }
        .signature-section {
            text-align: right;
        }
        .signature-name {
            font-weight: bold;
            font-size: 12px;
            margin-top: 5px;
        }
        .signature-title {
            font-size: 10px;
            color: #6c757d;
        }
        .thanks-message {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 15px;
            margin-top: 10px;
            color: #212529;
            text-align: center;
        }
        /* Bottom diagonal design */
        .footer-design {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 180px;
            height: 90px;
            background: #dc3545;
            clip-path: polygon(0 0, 100% 0, 100% 100%, 25% 100%);
            z-index: 1;
        }
        .footer-design-black {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 140px;
            height: 70px;
            background: #212529;
            clip-path: polygon(0 0, 100% 0, 100% 100%, 35% 100%);
            z-index: 2;
        }
        .footer-content {
            position: relative;
            z-index: 3;
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header with diagonal design -->
        <div class="header">
            <div class="header-red"></div>
            <div class="header-black"></div>
            <div class="header-content">
                <div class="company-info">
                    <div class="company-logo-box">
                        @php
                            $logoPath = public_path('assets/images/logo.png');
                            if (file_exists($logoPath)) {
                                $logoData = base64_encode(file_get_contents($logoPath));
                                $logoMime = mime_content_type($logoPath);
                                $logoBase64 = 'data:' . $logoMime . ';base64,' . $logoData;
                            } else {
                                $logoBase64 = null;
                            }
                        @endphp
                        @if($logoBase64)
                            <img src="{{ $logoBase64 }}" alt="SoftSpire Logo" style="max-width: 100%; max-height: 100%;">
                        @else
                            <span style="font-size: 32px; font-weight: bold; color: #dc3545;">P</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="receipt-title">RECEIPT</div>
        </div>

        <div class="content">
            @php
                $quotation = $payment->quotation;
                $totalPaid = $quotation->payments->sum('amount');
                $pendingAmount = $quotation->total_amount - $totalPaid;
            @endphp

            <!-- Customer and Payment Info -->
            <div class="info-section">
                <div class="info-box">
                    <div class="info-label">PAID BY:</div>
                    <div class="customer-name">{{ $quotation->customer->name }}</div>
                    <div class="customer-details">
                        @if($quotation->customer->email)
                        E: {{ $quotation->customer->email }}<br>
                        @endif
                        P: {{ $quotation->customer->country_code }} {{ $quotation->customer->phone }}<br>
                        @if($quotation->customer->country)
                        {{ $quotation->customer->country->name }}
                        @endif
                    </div>
                </div>
                <div class="info-box payment-details">
                    <div class="payment-number-box">RECEIPT NO: {{ $payment->payment_number }}</div>
                    <div class="detail-row">Payment Date: {{ $payment->payment_date->format('d M, Y') }}</div>
                    <div class="detail-row">Quotation No: {{ $quotation->quotation_number }}</div>
                    <div class="detail-row">Payment Type: {{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</div>
                    @if($payment->transaction_id)
                    <div class="detail-row">Transaction ID: {{ $payment->transaction_id }}</div>
                    @endif
                </div>
            </div>

            <!-- Payment Details Table -->
            <table class="payment-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Quotation Number</td>
                        <td class="text-right">{{ $quotation->quotation_number }}</td>
                    </tr>
                    <tr>
                        <td>Quotation Date</td>
                        <td class="text-right">{{ $quotation->quotation_date->format('d M, Y') }}</td>
                    </tr>
                    <tr>
                        <td>Quotation Total Amount</td>
                        <td class="text-right">&#8377;{{ number_format($quotation->total_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Total Paid Amount</td>
                        <td class="text-right">&#8377;{{ number_format($totalPaid, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Pending Amount</td>
                        <td class="text-right">&#8377;{{ number_format($pendingAmount, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Payment Amount -->
            <div class="amount-section">
                <div class="amount-box">
                    <div class="amount-label">Payment Received</div>
                    <div class="amount-value">&#8377;{{ number_format($payment->amount, 2) }}</div>
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="summary-section">
                <div class="summary-box">
                    <div class="summary-row">
                        <span>Quotation Total:</span>
                        <span>&#8377;{{ number_format($quotation->total_amount, 2) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Previously Paid:</span>
                        <span>&#8377;{{ number_format($totalPaid - $payment->amount, 2) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>This Payment:</span>
                        <span>&#8377;{{ number_format($payment->amount, 2) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Total Paid:</span>
                        <span>&#8377;{{ number_format($totalPaid, 2) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Remaining Balance:</span>
                        <span>&#8377;{{ number_format($pendingAmount, 2) }}</span>
                    </div>
                </div>
            </div>

            @if($payment->notes)
            <div class="summary-section">
                <div class="summary-box">
                    <div class="info-label mb-2">Notes:</div>
                    <div class="detail-row">{{ $payment->notes }}</div>
                </div>
            </div>
            @endif

            <div class="thanks-message">Thank you for your payment!</div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-design"></div>
            <div class="footer-design-black"></div>
            <div class="footer-content">
                <div class="contact-info">
                    <span>7 +000 123456789</span>
                    <span>7 softspire.com</span>
                    <span>7 Street Address write Here, 100</span>
                </div>
                <div class="signature-section">
                    <div class="signature-name">SOFTSPIRE TEAM</div>
                    <div class="signature-title">Chief Director</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
