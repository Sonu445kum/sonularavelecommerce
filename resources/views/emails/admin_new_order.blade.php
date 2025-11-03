<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>🛒 New Order Notification</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f3f6f9;
            margin: 0;
            padding: 30px;
        }
        .email-container {
            max-width: 650px;
            background: #ffffff;
            margin: auto;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .header {
            background: #007BFF;
            color: white;
            padding: 18px 30px;
            text-align: center;
        }
        .content {
            padding: 25px 30px;
            color: #333;
            line-height: 1.6;
        }
        .content h3 {
            color: #007BFF;
            margin-top: 0;
        }
        .order-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .order-details p {
            margin: 6px 0;
            font-size: 15px;
        }
        .footer {
            text-align: center;
            padding: 15px;
            font-size: 13px;
            color: #888;
            background: #f3f6f9;
        }
        a.button {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background: #007BFF;
            color: #fff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
        }
        a.button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h2>📦 New Order Received!</h2>
        </div>

        <div class="content">
            <p>Dear <strong>Admin</strong>,</p>
            <p>A new order has just been placed on your <strong>Sonnu E-Commerce</strong> store.</p>

            <div class="order-details">
                <h3>🧾 Order Details</h3>
                <p><strong>Order ID:</strong> #{{ $order->id }}</p>
                <p><strong>Customer:</strong> {{ $user->name }} ({{ $user->email }})</p>
                <p><strong>Total Amount:</strong> ₹{{ number_format($order->total, 2) }}</p>
                <p><strong>Payment Method:</strong> {{ strtoupper($order->payment_method) }}</p>
                <p><strong>Status:</strong> {{ ucfirst($order->status ?? 'Pending') }}</p>
            </div>

            @if(isset($orderUrl))
                <p>You can manage this order directly from your admin dashboard.</p>
                <a href="{{ $orderUrl }}" class="button">🔍 View Order</a>
            @else
                <p>Please log in to your Admin Panel to manage this order.</p>
            @endif
        </div>

        <div class="footer">
            <p>— Sonnu E-Commerce System © {{ date('Y') }}</p>
            <p>This is an automated message. Please do not reply.</p>
        </div>
    </div>
</body>
</html>
