<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation - {{ $quotation->quotation_number }}</title>
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
            font-family: 'Arial', 'Helvetica', sans-serif;
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
        .quotation-title {
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
        .quotation-details {
            text-align: right;
        }
        .quotation-number-box {
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
        /* Items table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table thead tr th {
            padding: 14px 12px;
            text-align: left;
            font-weight: bold;
            font-size: 12px;
            color: #fff;
        }
        .items-table thead tr th:nth-child(1) {
            background: #dc3545;
            width: 70%;
        }
        .items-table thead tr th:nth-child(2) {
            background: #212529;
            text-align: right;
            width: 30%;
        }
        .items-table tbody tr td {
            padding: 14px 12px;
            border-bottom: 1px solid #e9ecef;
            font-size: 11px;
            vertical-align: top;
        }
        .items-table tbody tr:last-child td {
            border-bottom: none;
        }
        .item-description {
            font-size: 10px;
            color: #6c757d;
            margin-top: 5px;
            line-height: 1.4;
        }
        .items-table tbody tr td:first-child {
            font-weight: 500;
        }
        .text-right {
            text-align: right;
        }
        /* Payment and totals section */
        .bottom-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .payment-section, .totals-section {
            width: 48%;
        }
        .section-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 10px;
            color: #212529;
        }
        .payment-details,         .total-row {
            font-size: 11px;
            margin-bottom: 8px;
            color: #495057;
            text-align: right;
        }
        .grand-total-box {
            background: #dc3545;
            color: #fff;
            padding: 18px 20px;
            margin-top: 12px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            border-radius: 2px;
        }
        .grand-total-label {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 5px;
        }
        .grand-total-amount {
            font-weight: bold;
            font-size: 22px;
        }
        /* Terms and conditions */
        .terms-section {
            margin-bottom: 15px;
        }
        .terms-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 10px;
            color: #212529;
        }
        .terms-content {
            font-size: 10px;
            color: #495057;
            line-height: 1.8;
        }
        .terms-content ul {
            margin-left: 20px;
            margin-top: 5px;
        }
        .terms-content li {
            margin-bottom: 5px;
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
            <div class="quotation-title">QUOTATION</div>
        </div>

        <div class="content">
            <!-- Customer and Quotation Info -->
            <div class="info-section">
                <div class="info-box">
                    <div class="info-label">QUOTATION TO:</div>
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
                <div class="info-box quotation-details">
                    <div class="quotation-number-box">QUOTATION NO: {{ $quotation->quotation_number }}</div>
                    <div class="detail-row">Quotation Date: {{ $quotation->quotation_date->format('d M, Y') }}</div>
                    @if($quotation->duration_months)
                    <div class="detail-row">Duration: {{ $quotation->duration_months }}@if(is_numeric($quotation->duration_months)) months @endif</div>
                    @endif
                    @if($quotation->technologies)
                    <div class="detail-row">Technologies: {{ $quotation->technologies }}</div>
                    @endif
                </div>
            </div>

            <!-- Items Table -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Item description</th>
                        <th>Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quotation->items as $item)
                    <tr>
                        <td>
                            {{ $item->item_name }}
                            @if($item->description)
                            <div class="item-description">{{ $item->description }}</div>
                            @endif
                        </td>
                        <td class="text-right">₹{{ number_format($item->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Payment and Totals -->
            <div class="bottom-section">
                <div class="payment-section">
                    <div class="section-title">Payment method</div>
                    <div class="payment-details">
                        Account: 0123456789012090<br>
                        Swift: 01234560<br>
                        Payoneer: payoneer@softspire.com
                    </div>
                </div>
                <div class="totals-section">
                    <div class="total-row text-right">Sub Total: ₹{{ number_format($quotation->total_amount, 2) }}</div>
                    <div class="grand-total-box">
                        <div class="grand-total-label">Grand Total</div>
                        <div class="grand-total-amount">₹{{ number_format($quotation->total_amount, 2) }}</div>
                    </div>
                </div>
            </div>

            <!-- Terms and Conditions -->
            <div class="terms-section">
                <div class="terms-title">Terms & Conditions:</div>
                <div class="terms-content">
                    <ul>
                        <li>The customer should pay 35% of the total invoice value before the project starts.</li>
                        <li>The balance due must be paid in full before project completion and delivery.</li>
                        <li>Any delays caused by the client, such as late payment or approvals, may result in delay to the project timeline.</li>
                        <li>Support will be provided after the warranty period at an additional cost.</li>
                        @if($quotation->annual_amount)
                        <li>Annual project maintenance will be charged at ₹{{ number_format($quotation->annual_amount, 2) }}/- per year.</li>
                        @else
                        <li>Annual project maintenance will be charged at an additional cost (to be discussed and finalized).</li>
                        @endif
                        <li>If the source code is required by the client, it will be provided at an additional charge (to be discussed and finalized).</li>
                        <li>Any third-party accounts, services, or tools (such as WhatsApp Business API, AI models, payment gateways, SMS/Email services, etc.) must be purchased and maintained by the client at their own cost.</li>
                    </ul>
                </div>
            </div>

            <div class="thanks-message">Thanks for your business!</div>
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

