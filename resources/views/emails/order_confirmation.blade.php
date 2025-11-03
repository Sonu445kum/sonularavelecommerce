<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f6fa;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 650px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 10px;
            padding: 25px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }
        .header h2 {
            color: #007bff;
            font-size: 22px;
        }
        .content p {
            line-height: 1.6;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background-color: #fafafa;
            border-radius: 5px;
        }
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #e0e0e0;
        }
        td:first-child {
            font-weight: 600;
            width: 40%;
        }
        .footer {
            text-align: center;
            font-size: 13px;
            color: #777;
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .btn {
            display: inline-block;
            background: #007bff;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 6px;
            margin-top: 20px;
            transition: background 0.3s ease;
        }
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header --}}
        <div class="header">
            <h2>✅ Order Confirmation</h2>
        </div>

        {{-- Greeting --}}
        <div class="content">
            <p>Hi <strong>{{ $user->name }}</strong>,</p>
            <p>Thank you for shopping with <strong>{{ config('app.name') }}</strong>! 🎉</p>
            <p>We’re excited to let you know that your order has been successfully placed. Below are your order details:</p>

            {{-- Order Details --}}
            <table>
                <tr>
                    <td>🆔 <strong>Order ID</strong></td>
                    <td>#{{ $order->id }}</td>
                </tr>
                <tr>
                    <td>💰 <strong>Total Amount</strong></td>
                    <td>₹{{ number_format($total ?? $order->total, 2) }}</td>
                </tr>
                <tr>
                    <td>📦 <strong>Status</strong></td>
                    <td>{{ ucfirst($status ?? $order->status) }}</td>
                </tr>
                <tr>
                    <td>💳 <strong>Payment Method</strong></td>
                    <td>{{ strtoupper($order->payment_method) }}</td>
                </tr>
            </table>

            {{-- CTA Button --}}
            <p style="text-align:center;">
                <a href="{{ url('/orders/' . $order->id) }}" class="btn">View My Order</a>
            </p>

            <p style="margin-top: 25px;">
                We’ll notify you as soon as your order is shipped.  
                If you have any questions, feel free to reply to this email.
            </p>

            <p>Thanks again for choosing <strong>{{ config('app.name') }}</strong> ❤️</p>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>This is an automated message — please do not reply directly.</p>
        </div>
    </div>
</body>
</html>
