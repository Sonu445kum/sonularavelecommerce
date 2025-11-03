<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f4f6f8;
            padding: 30px;
            color: #333;
        }
        .container {
            max-width: 650px;
            margin: auto;
            background: #ffffff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h2 {
            color: #007bff;
            text-align: center;
            margin-bottom: 10px;
        }
        .divider {
            height: 2px;
            background: #e0e0e0;
            margin: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        td {
            padding: 8px 0;
        }
        td:first-child {
            font-weight: 600;
            color: #555;
        }
        .footer {
            margin-top: 25px;
            text-align: center;
            color: #777;
            font-size: 14px;
        }
        .brand {
            color: #007bff;
            font-weight: bold;
        }
        .btn {
            display: inline-block;
            padding: 10px 18px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
            font-weight: 500;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .emoji {
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>✅ Order Confirmation</h2>
        <p>Hi <strong>{{ $user->name }}</strong>,</p>
        <p>Thank you for shopping with <span class="brand">MyShop</span>! <span class="emoji">🎉</span></p>

        <div class="divider"></div>

        <table>
            <tr>
                <td>Order ID:</td>
                <td>#{{ $order->id }}</td>
            </tr>
            <tr>
                <td>Total Amount:</td>
                <td>₹{{ number_format($total, 2) }}</td>
            </tr>
            <tr>
                <td>Status:</td>
                <td><strong>{{ ucfirst($status) }}</strong></td>
            </tr>
            <tr>
                <td>Payment Method:</td>
                <td>{{ strtoupper($order->payment_method ?? 'N/A') }}</td>
            </tr>
        </table>

        <div class="divider"></div>

        <p>We’re processing your order and will notify you once it’s shipped. You can track your order anytime from your account dashboard.</p>

        <p style="text-align:center;">
            <a href="{{ url('/orders/'.$order->id) }}" class="btn">View My Order</a>
        </p>

        <p>Thanks again for choosing <strong>MyShop E-Commerce</strong> ❤️</p>

        <div class="footer">
            <p>— The MyShop Team</p>
            <p>© {{ date('Y') }} MyShop E-Commerce. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
