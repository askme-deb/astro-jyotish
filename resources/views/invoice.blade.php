<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice BKNG{{ $booking['id'] ?? '' }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1f2937;
            line-height: 1.35;
        }

        .container {
            width: 100%;
        }

        .header {
            width: 100%;
            margin-bottom: 14px;
        }

        .company {
            float: left;
            width: 56%;
        }

        .invoice {
            float: right;
            text-align: right;
            width: 40%;
        }

        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            color: #f97316;
            margin-bottom: 6px;
        }

        .clear {
            clear: both;
        }

        .section {
            margin-top: 14px;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 6px;
            color: #111827;
        }

        .box {
            padding: 10px 12px;
            /* border: 1px solid #e5e7eb; */
            background: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            background: #f3f4f6;
            padding: 7px 8px;
            border: 1px solid #e5e7eb;
            text-align: left;
            font-size: 10px;
        }

        table td {
            padding: 7px 8px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }

        .meta-table td {
            border: none;
            padding: 2px 0;
        }

        .total-box {
            width: 270px;
            float: right;
            margin-top: 12px;
        }

        .total-box table td {
            border: none;
            padding: 4px 0;
        }

        .grand-total {
            font-size: 16px;
            font-weight: bold;
            color: #15803d;
        }

        .note-box {
            margin-top: 10px;
            padding: 8px 10px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            font-size: 11px;
            color: #4b5563;
        }

        .footer {
            margin-top: 30px;
            font-size: 11px;
            color: #6b7280;
            text-align: center;
            clear: both;
            display: block;
            width: 100%;
        }

        .declaration-section {
            margin-top: 18px;
            width: 100%;
        }

        .declaration-box {
            width: 62%;
            float: left;
            font-size: 11px;
            color: #4b5563;
            line-height: 1.45;
        }

        .signatory-box {
            width: 30%;
            float: right;
            text-align: center;
            font-size: 11px;
            color: #374151;
        }

        .signatory-space {
            height: 38px;
        }

        .compact-text {
            margin: 0;
        }
    </style>
</head>

<body>

    @php
        $scheduledAt = !empty($booking['scheduled_at']) ? \Carbon\Carbon::parse($booking['scheduled_at']) : null;
        $endTime = !empty($booking['end_time']) ? \Carbon\Carbon::parse($booking['end_time']) : null;

        $slot = $scheduledAt ? $scheduledAt->format('h:i A') : '-';
        $slot .= $endTime ? ' - ' . $endTime->format('h:i A') : '';

        $amount = (float) ($booking['rate'] ?? 0);
        $gstRate = 18;
        $cgstRate = 9;
        $sgstRate = 9;
        $igstRate = 18;
        $taxableAmount = $amount > 0 ? round($amount / (1 + ($gstRate / 100)), 2) : 0;
        $gstAmount = round($amount - $taxableAmount, 2);
        $business = is_array($business ?? null) ? $business : [];
        $businessState = trim((string) ($business['state'] ?? 'West Bengal'));
        $businessAuthorizedSignatory = trim((string) ($business['authorized_signatory'] ?? $business['name'] ?? 'Authorized Signatory'));
        $customerState = trim((string) ($booking['state'] ?? $booking['customer_state'] ?? ''));
        $customerAddress = trim((string) ($booking['address'] ?? $booking['customer_address'] ?? '-'));
        $customerGstin = trim((string) ($booking['gstin'] ?? $booking['customer_gstin'] ?? '-'));
        $normalizedCustomerState = strtolower(preg_replace('/\s+/', ' ', $customerState));
        $westBengalAliases = ['west bengal', 'wb'];
        $isWestBengalCustomer = $normalizedCustomerState === '' || in_array($normalizedCustomerState, $westBengalAliases, true);
        $placeOfSupply = $customerState !== '' ? $customerState : 'West Bengal';
        $isInterstateSupply = ! $isWestBengalCustomer;
        $cgstAmount = $isInterstateSupply ? 0 : round($gstAmount / 2, 2);
        $sgstAmount = $isInterstateSupply ? 0 : round($gstAmount / 2, 2);
        $igstAmount = $isInterstateSupply ? $gstAmount : 0;
        $sacCode = $business['sac_code'] ?? $booking['sac_code'] ?? '-';
        $gstLabel = $isInterstateSupply ? 'IGST' : 'CGST/SGST';
        $invoiceDate = isset($generatedDate) ? $generatedDate->copy() : now();
        $dueDate = $scheduledAt ? $scheduledAt->copy() : $invoiceDate->copy();
    @endphp


    <div class="container">

        <!-- HEADER -->
        <div class="header">

            <div class="company">
                <h2>{{ $business['name'] ?? 'Astrologer Raju Maharaj' }}</h2>

                <div class="compact-text">{{ $business['address'] ?? '-' }}</div>
                <div class="compact-text">GSTIN : {{ $business['gstin'] ?? '-' }}</div>
                <div class="compact-text">Email : {{ $business['email'] ?? '-' }}</div>
                <div class="compact-text">Phone : {{ $business['phone'] ?? '-' }}</div>

            </div>

            <div class="invoice">
                <div class="invoice-title">INVOICE</div>

                Invoice No : {{ $invoiceNumber ?? '-' }} <br>
                Booking ID : BKNG{{ $booking['id'] ?? '-' }} <br>
                Invoice Date : {{ $invoiceDate->format('d M Y') }} <br>
                Due Date : {{ $dueDate->format('d M Y') }}

            </div>

            <div class="clear"></div>

        </div>



        <!-- BILLING INFO -->
        <div class="section">

            <table>
                <tr>

                    <td width="50%">
                        <div class="box">
                            <div class="section-title">Bill To</div>
                            <div class="compact-text">{{ $booking['name'] ?? '-' }}</div>
                            <div class="compact-text">Address : {{ $customerAddress }}</div>
                            <div class="compact-text">Email : {{ $booking['email'] ?? '-' }}</div>
                            <div class="compact-text">Phone : {{ $booking['phone'] ?? '-' }}</div>
                            <div class="compact-text">GSTIN : {{ $customerGstin !== '' ? $customerGstin : '-' }}</div>
                            <div class="compact-text">State : {{ $customerState !== '' ? $customerState : 'West Bengal' }}</div>
                        </div>
                    </td>

                    <td width="50%">
                        <div class="box">

                            <div class="section-title">Invoice Details</div>

                            <table class="meta-table">

                                <tr>
                                    <td>Place of Supply</td>
                                    <td>{{ $placeOfSupply }}</td>
                                </tr>

                                <tr>
                                    <td>SAC / HSN</td>
                                    <td>{{ $sacCode }}</td>
                                </tr>

                                <tr>
                                    <td>GST Type</td>
                                    <td>{{ $gstLabel }}</td>
                                </tr>

                                <tr>
                                    <td>Method</td>
                                    <td>{{ ucfirst($booking['payment_method'] ?? '-') }}</td>
                                </tr>

                                <tr>
                                    <td>Transaction ID</td>
                                    <td>{{ $booking['razorpay_payment_id'] ?? $booking['transaction_id'] ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <td>Status</td>
                                    <td>Paid</td>
                                </tr>

                                <tr>
                                    <td>GST</td>
                                    <td>{{ $gstRate }}% Inclusive</td>
                                </tr>

                            </table>

                        </div>
                    </td>

                </tr>
            </table>

        </div>


        <!-- ITEM TABLE -->
        <div class="section">

            <table>

                <thead>
                    <tr>
                        <th>SAC / HSN</th>
                        <th>Consultation</th>
                        <th>Date</th>
                        <th>Time Slot</th>
                        <th>Duration</th>
                        <th>GST Type</th>
                        <th>Amount</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <td>{{ $sacCode }}</td>

                        <td>{{ ucfirst($booking['consultation_type'] ?? '-') }}</td>

                        <td>{{ $scheduledAt ? $scheduledAt->format('d M Y') : '-' }}</td>

                        <td>{{ $slot }}</td>

                        <td>{{ $booking['duration'] ?? '-' }} min</td>

                        <td>{{ $isInterstateSupply ? 'IGST 18%' : 'CGST 9% + SGST 9%' }}</td>

                        <td>₹{{ number_format($amount, 2) }}</td>

                    </tr>

                </tbody>

            </table>

        </div>


        <!-- TOTAL -->
        <div class="total-box">

            <table>

                <tr>
                    <td>Taxable Value</td>
                    <td align="right">₹{{ number_format($taxableAmount, 2) }}</td>
                </tr>

                @if($isInterstateSupply)
                    <tr>
                        <td>IGST ({{ $igstRate }}% Inclusive)</td>
                        <td align="right">₹{{ number_format($igstAmount, 2) }}</td>
                    </tr>
                @else
                    <tr>
                        <td>CGST ({{ $cgstRate }}% Inclusive)</td>
                        <td align="right">₹{{ number_format($cgstAmount, 2) }}</td>
                    </tr>

                    <tr>
                        <td>SGST ({{ $sgstRate }}% Inclusive)</td>
                        <td align="right">₹{{ number_format($sgstAmount, 2) }}</td>
                    </tr>
                @endif

                <tr class="grand-total">
                    <td>Total</td>
                    <td align="right">₹{{ number_format($amount, 2) }}</td>
                </tr>

            </table>

            <div class="note-box">
                GST has been calculated on an inclusive basis. Customers in West Bengal are billed with CGST and SGST. Customers outside West Bengal are billed with IGST only.
            </div>

        </div>

        <div class="clear"></div>

        <div class="declaration-section">
            <div class="declaration-box">
                <div class="section-title">Declaration</div>
                We declare that this invoice shows the actual price of the services described and that all particulars are true and correct. This is a computer-generated invoice and does not require a physical signature.
            </div>

            <div class="signatory-box">
                <div>For {{ $business['name'] ?? 'Astrologer Raju Maharaj' }}</div>
                <div class="signatory-space"></div>
                <div><strong>{{ $businessAuthorizedSignatory }}</strong></div>
                <div>Authorized Signatory</div>
            </div>

            <div class="clear"></div>
        </div>


        <!-- FOOTER -->
        <div class="footer">

            Thank you for booking with {{ $business['name'] ?? 'Astrologer Raju Maharaj' }}.<br>
            This invoice was automatically generated. Total amount is inclusive of {{ $gstRate }}% GST.

        </div>


    </div>

</body>

</html>
