<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice BKNG{{ $booking['id'] ?? '' }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #333;
        }

        .container {
            width: 100%;
        }

        .header {
            width: 100%;
            margin-bottom: 25px;
        }

        .company {
            float: left;
        }

        .invoice {
            float: right;
            text-align: right;
        }

        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #f97316;
        }

        .clear {
            clear: both;
        }

        .section {
            margin-top: 25px;
        }

        .box {
            /* border: 1px solid #e5e7eb; */
            padding: 15px;
            /* border-radius: 6px; */
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            background: #f3f4f6;
            padding: 10px;
            border: 1px solid #e5e7eb;
            text-align: left;
        }

        table td {
            padding: 10px;
            border: 1px solid #e5e7eb;
        }

        .meta-table td {
            border: none;
            padding: 4px 0;
        }

        .total-box {
            width: 300px;
            float: right;
            margin-top: 20px;
        }

        .total-box table td {
            border: none;
            padding: 6px;
        }

        .grand-total {
            font-size: 20px;
            font-weight: bold;
            color: #15803d;
        }

        .footer {
            margin-top: 80px;
            font-size: 11px;
            color: #6b7280;
            text-align: center;
            clear: both;
            display: block;
            width: 100%;
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
        $business = is_array($business ?? null) ? $business : [];
    @endphp


    <div class="container">

        <!-- HEADER -->
        <div class="header">

            <div class="company">
                <h2>{{ $business['name'] ?? 'Astrologer Raju Maharaj' }}</h2>

                {{ $business['address'] ?? '-' }} <br>
                GSTIN : {{ $business['gstin'] ?? '-' }} <br>
                Email : {{ $business['email'] ?? '-' }} <br>
                Phone : {{ $business['phone'] ?? '-' }}

            </div>

            <div class="invoice">
                <div class="invoice-title">INVOICE</div>

                Invoice No : {{ $invoiceNumber ?? '-' }} <br>
                Booking ID : BKNG{{ $booking['id'] ?? '-' }} <br>
                Date : {{ $generatedDate->format('d M Y') }}

            </div>

            <div class="clear"></div>

        </div>



        <!-- BILLING INFO -->
        <div class="section">

            <table>
                <tr>

                    <td width="50%">
                        <div class="box">
                            <b>Bill To</b><br><br>
                            {{ $booking['name'] ?? '-' }} <br>
                            Email : {{ $booking['email'] ?? '-' }} <br>
                            Phone : {{ $booking['phone'] ?? '-' }}
                        </div>
                    </td>

                    <td width="50%">
                        <div class="box">

                            <b>Payment Details</b>

                            <table class="meta-table">

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
                        <th>Astrologer</th>
                        <th>Consultation</th>
                        <th>Date</th>
                        <th>Time Slot</th>
                        <th>Duration</th>
                        <th>Amount</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <td>{{ $booking['astrologer']['name'] ?? '-' }}</td>

                        <td>{{ ucfirst($booking['consultation_type'] ?? '-') }}</td>

                        <td>{{ $scheduledAt ? $scheduledAt->format('d M Y') : '-' }}</td>

                        <td>{{ $slot }}</td>

                        <td>{{ $booking['duration'] ?? '-' }} min</td>

                        <td>₹{{ number_format($amount, 2) }}</td>

                    </tr>

                </tbody>

            </table>

        </div>


        <!-- TOTAL -->
        <div class="total-box">

            <table>

                <tr>
                    <td>Subtotal</td>
                    <td align="right">₹{{ number_format($amount, 2) }}</td>
                </tr>

                <tr>
                    <td>Tax</td>
                    <td align="right">₹0.00</td>
                </tr>

                <tr class="grand-total">
                    <td>Total</td>
                    <td align="right">₹{{ number_format($amount, 2) }}</td>
                </tr>

            </table>

        </div>

        <div class="clear"></div>


        <!-- FOOTER -->
        <div class="footer">

            Thank you for booking with {{ $business['name'] ?? 'Astrologer Raju Maharaj' }}.<br>
            This invoice was automatically generated.

        </div>


    </div>

</body>

</html>
