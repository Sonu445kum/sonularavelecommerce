@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container py-4">

    {{-- ============================ HEADER ============================= --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold text-dark">
            <i class="bi bi-speedometer2 me-2 text-primary"></i> Admin Dashboard
        </h1>

        {{-- 🔔 Notifications Dropdown --}}
        <div class="dropdown">
            <button class="btn btn-outline-primary position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-bell fs-5"></i>
                @if(isset($notifications) && $notifications->where('is_read', false)->count() > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ $notifications->where('is_read', false)->count() }}
                    </span>
                @endif
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" aria-labelledby="notificationDropdown" style="width: 350px;">
                <li class="dropdown-header fw-bold bg-light py-2 px-3">Notifications</li>
                @forelse($notifications ?? [] as $notification)
                    <li class="px-3 py-2 border-bottom {{ $notification->is_read ? 'bg-light' : 'bg-white' }}">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-bag-check text-primary fs-5 me-2"></i>
                            <div>
                                <div class="fw-semibold">{{ $notification->title }}</div>
                                <small class="text-muted d-block">{{ $notification->message }}</small>
                                <small class="text-secondary">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="text-center text-muted py-3">No notifications found</li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- ============================ DYNAMIC SUMMARY CARD ============================= --}}
    <div class="summary-card mb-5 p-4 shadow-lg rounded-4">
        <h4 class="fw-bold mb-4 text-dark">Dashboard Summary</h4>
        <div class="summary-metrics d-flex flex-wrap gap-3 justify-content-center">

            @php
                // ensure variable exists (safe fallback)
                $successfulPayments = $successfulPayments ?? 0;
                $totalProducts = $totalProducts ?? 0;
                $totalOrders = $totalOrders ?? 0;
                $totalCategories = $totalCategories ?? 0;
                $totalUsers = $totalUsers ?? 0;
                $totalRevenue = $totalRevenue ?? 0.0;

                $cards = [
                    ['icon'=>'bi-box-seam','title'=>'Total Products','value'=>$totalProducts,'bg'=>'linear-gradient(135deg,#6a11cb,#2575fc)'],
                    ['icon'=>'bi-bag-check','title'=>'Total Orders','value'=>$totalOrders,'bg'=>'linear-gradient(135deg,#ff416c,#ff4b2b)'],
                    ['icon'=>'bi-tags','title'=>'Total Categories','value'=>$totalCategories,'bg'=>'linear-gradient(135deg,#00c6ff,#0072ff)'],
                    ['icon'=>'bi-people','title'=>'Total Users','value'=>$totalUsers,'bg'=>'linear-gradient(135deg,#f7971e,#ffd200)'],
                    ['icon'=>'bi-currency-rupee','title'=>'Total Revenue','value'=>'₹'.number_format($totalRevenue,2),'bg'=>'linear-gradient(135deg,#43e97b,#38f9d7)'],
                    ['icon'=>'bi-wallet2','title'=>'Successful Payments','value'=>$successfulPayments,'bg'=>'linear-gradient(135deg,#ffb75e,#ed8f03)'],
                ];
            @endphp

            @foreach($cards as $card)
                <div class="metric-card" style="background: {{ $card['bg'] }};">
                    <div class="metric-icon">
                        <i class="bi {{ $card['icon'] }}"></i>
                    </div>
                    <div class="metric-title">{{ $card['title'] }}</div>
                    <div class="metric-value counter">{{ $card['value'] }}</div>
                </div>
            @endforeach

        </div>
    </div>

    {{-- ============================ CHARTS & RECENT TABLES ============================= --}}
    <div class="row g-4 mt-5">
        <div class="col-md-12">
            <div class="card shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-3">Overall Dashboard Metrics</h5>
                <canvas id="dashboardChart" height="150"></canvas>
            </div>
        </div>

        <div class="col-md-6 mt-4">
            <div class="card shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-3">Revenue Distribution</h5>
                <canvas id="revenuePieChart" height="200"></canvas>
            </div>
        </div>

        <div class="col-md-6 mt-4">
            <div class="card shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-3">Growth / Payment Methods</h5>
                <canvas id="growthPieChart" height="200"></canvas>
            </div>
        </div>
    </div>

    {{-- ============================ RECENT ORDERS ============================= --}}
    <div class="mt-5">
        <h4 class="fw-bold mb-3 text-dark">
            <i class="bi bi-receipt-cutoff me-2 text-primary"></i> Recent Orders
        </h4>
        <div class="card shadow-sm rounded-4">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-primary text-dark">
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders ?? [] as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->user->name ?? 'Guest' }}</td>
                                <td>₹{{ number_format($order->total, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ in_array(strtolower($order->status), ['completed','delivered']) ? 'success' : 'warning' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">No recent orders found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ============================ RECENT USERS ============================= --}}
    <div class="mt-5">
        <h4 class="fw-bold mb-3 text-dark">
            <i class="bi bi-person-lines-fill me-2 text-success"></i> Recent Users
        </h4>
        <div class="card shadow-sm rounded-4">
            <div class="card-body p-0">
                <table class="table table-striped align-middle mb-0">
                    <thead class="table-success text-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentUsers ?? [] as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">No new users found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ============================ RECENT PAYMENTS ============================= --}}
    <div class="mt-5 mb-5">
        <h4 class="fw-bold mb-3 text-dark">
            <i class="bi bi-credit-card-2-front me-2 text-info"></i> Recent Payments
        </h4>
        <div class="card shadow-sm rounded-4">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-info text-dark">
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayments ?? [] as $payment)
                            <tr>
                                <td>{{ $payment->id }}</td>
                                <td>{{ $payment->order && $payment->order->user ? $payment->order->user->name : 'N/A' }}</td>
                                <td>₹{{ number_format($payment->amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $payment->status === 'success' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td>{{ $payment->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">No recent payments found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- ==================== Revenue Pie Chart fallbacks ==================== --}}
@php
    // backward compatible variable names used earlier in your project
    $revenueLabels = $revenueDistributionLabels ?? ($revenueLabels ?? ['Product A','Product B','Product C','Product D','Product E']);
    $revenueData   = $revenueDistributionData ?? ($revenueData ?? [120,90,70,50,30]);

    // payment method fallback (if controller didn't pass method distribution)
    $paymentMethodLabels = $paymentMethodLabels ?? ($paymentMethodLabels ?? []);
    $paymentMethodData = $paymentMethodData ?? ($paymentMethodData ?? []);
@endphp

{{-- ============================ STYLES ============================= --}}
<style>
.summary-card {
    background: #fff;
    border-radius: 20px;
    padding: 25px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.summary-metrics {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
}

.metric-card {
    border-radius: 15px;
    padding: 25px 20px;
    min-width: 160px;
    text-align: center;
    color: #0f0e0eff;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    background-size: 200% 200%;
    animation: gradientShift 5s ease infinite;
}

.metric-card:hover {
    transform: translateY(-8px) scale(1.05);
    box-shadow: 0 12px 30px rgba(0,0,0,0.2);
}

.metric-icon i {
    font-size: 2.5rem;
    margin-bottom: 12px;
}

.metric-title {
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.metric-title i {
    margin-right: 6px;
}

.metric-value {
    font-size: 1.8rem;
    font-weight: 700;
}

@keyframes gradientShift {
    0%{background-position:0% 50%}
    50%{background-position:100% 50%}
    100%{background-position:0% 50%}
}

.counter {
    transition: all 0.5s ease;
}
</style>

{{-- ============================ CHARTS (single robust script) ============================= --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function(){
    // ---------------------------
    // Animated Counters
    // ---------------------------
    document.querySelectorAll('.counter').forEach(el=>{
        let raw = el.textContent || '';
        let value = parseFloat(raw.toString().replace(/[^0-9.]/g,'')) || 0;
        let start = 0;
        let duration = 800;
        let steps = Math.max(1, Math.floor(duration / 16));
        let increment = value / steps;
        function updateCounter(){
            start += increment;
            if (start >= value) {
                if (raw.includes('₹')) el.textContent = '₹' + Number(value).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                else el.textContent = Math.floor(value).toLocaleString();
            } else {
                if (raw.includes('₹')) el.textContent = '₹' + Math.floor(start).toLocaleString();
                else el.textContent = Math.floor(start).toLocaleString();
                requestAnimationFrame(updateCounter);
            }
        }
        updateCounter();
    });

    // ---------------------------
    // Prepare data (support old & new variable names)
    // ---------------------------
    const months = @json($months ?? null);
    const dashboardLabels = @json($dashboardLabels ?? null);
    const labels = (Array.isArray(months) && months.length) ? months : (Array.isArray(dashboardLabels) ? dashboardLabels : []);

    const ordersMonthly = @json($ordersMonthly ?? null);
    const ordersChartData = @json($ordersChartData ?? null);
    const ordersData = (Array.isArray(ordersMonthly) && ordersMonthly.length) ? ordersMonthly : (Array.isArray(ordersChartData) ? ordersChartData : []);

    const revenueMonthly = @json($revenueMonthly ?? null);
    const revenueChartData = @json($revenueChartData ?? null);
    const revenueData = (Array.isArray(revenueMonthly) && revenueMonthly.length) ? revenueMonthly : (Array.isArray(revenueChartData) ? revenueChartData : []);

    const usersMonthly = @json($usersMonthly ?? null);
    const productsChartData = @json($productsChartData ?? null);
    const usersData = (Array.isArray(usersMonthly) && usersMonthly.length) ? usersMonthly : (Array.isArray(productsChartData) ? productsChartData : []);

    const paymentsMonthly = @json($paymentsMonthly ?? null);
    const paymentsChartData = @json($paymentsChartData ?? null);
    const paymentsData = (Array.isArray(paymentsMonthly) && paymentsMonthly.length) ? paymentsMonthly : (Array.isArray(paymentsChartData) ? paymentsChartData : []);

    const revenueDistributionLabels = @json($revenueDistributionLabels ?? null);
    const revenueLabelsOld = @json($revenueLabels ?? null);
    const revLabels = (Array.isArray(revenueDistributionLabels) && revenueDistributionLabels.length) ? revenueDistributionLabels : (Array.isArray(revenueLabelsOld) ? revenueLabelsOld : @json($revenueLabels ?? ['Product A','Product B']));

    const revenueDistributionData = @json($revenueDistributionData ?? null);
    const revenueDataPieOld = @json($revenueData ?? null);
    const revData = (Array.isArray(revenueDistributionData) && revenueDistributionData.length) ? revenueDistributionData : (Array.isArray(revenueDataPieOld) ? revenueDataPieOld : @json($revenueData ?? [100,50]));

    const paymentMethodLabels = @json($paymentMethodLabels ?? null);
    const paymentMethodData = @json($paymentMethodData ?? null);
    const growthRateData = @json($growthRateData ?? null);

    let pmLabels = [];
    let pmData = [];

    if (Array.isArray(paymentMethodLabels) && paymentMethodLabels.length && Array.isArray(paymentMethodData) && paymentMethodData.length) {
        pmLabels = paymentMethodLabels;
        pmData = paymentMethodData;
    } else if (Array.isArray(growthRateData) && growthRateData.length >= 3) {
        pmLabels = ['New Users','New Orders','Revenue Growth'];
        pmData = growthRateData;
    } else {
        // fallback safe defaults
        pmLabels = @json($paymentMethodLabels ?? ['Stripe','COD','Razorpay']);
        pmData = @json($paymentMethodData ?? [0,0,0]);
    }

    // ---------------------------
    // Destroy existing charts if present
    // ---------------------------
    try {
        if (window.overallChart) { window.overallChart.destroy(); window.overallChart = null; }
        if (window.revenueChart) { window.revenueChart.destroy(); window.revenueChart = null; }
        if (window.methodChart) { window.methodChart.destroy(); window.methodChart = null; }
    } catch(e) {
        console.warn('Chart destroy warning:', e);
    }

    // ---------------------------
    // 1) Overall multi-line chart
    // ---------------------------
    const overallCtx = document.getElementById('dashboardChart');
    if (overallCtx) {
        window.overallChart = new Chart(overallCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    { label: 'Orders',      data: ordersData,  borderColor: '#007bff', backgroundColor:'rgba(0,123,255,0.08)', fill:true, tension:0.3 },
                    { label: 'Revenue (₹)', data: revenueData, borderColor: '#28a745', backgroundColor:'rgba(40,167,69,0.08)', fill:true, tension:0.3 },
                    { label: 'Payments',    data: paymentsData, borderColor:'#ffc107', backgroundColor:'rgba(255,193,7,0.08)', fill:true, tension:0.3 },
                    { label: 'Users',       data: usersData, borderColor:'#17a2b8', backgroundColor:'rgba(23,162,184,0.08)', fill:true, tension:0.3 }
                ]
            },
            options: {
                responsive: true,
                interaction: { mode: 'index', intersect: false },
                stacked: false,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                let lab = ctx.dataset.label || '';
                                let val = ctx.parsed && typeof ctx.parsed.y !== 'undefined' ? ctx.parsed.y : ctx.raw;
                                if (lab.includes('Revenue')) return lab + ': ₹' + Number(val).toLocaleString();
                                return lab + ': ' + Number(val).toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: true },
                    x: {}
                }
            }
        });
    }

    // ---------------------------
    // 2) Revenue distribution pie
    // ---------------------------
    const revCtx = document.getElementById('revenuePieChart');
    if (revCtx) {
        window.revenueChart = new Chart(revCtx, {
            type: 'pie',
            data: {
                labels: revLabels,
                datasets: [{
                    data: revData,
                    backgroundColor: (function(){
                        const palette = ['#007bff','#28a745','#ffc107','#dc3545','#17a2b8','#6f42c1','#fd7e14','#20c997','#d63384','#0d6efd'];
                        return palette.slice(0, Math.max(1, revLabels.length));
                    })()
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const val = context.raw || 0;
                                const total = context.dataset.data.reduce((a,b)=>a+(parseFloat(b)||0),0);
                                const pct = total ? ((val/total)*100).toFixed(1) : 0;
                                return `${label}: ₹${Number(val).toLocaleString()} (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // ---------------------------
    // 3) Payment method / growth doughnut
    // ---------------------------
    const methodCtx = document.getElementById('growthPieChart');
    if (methodCtx) {
        window.methodChart = new Chart(methodCtx, {
            type: 'doughnut',
            data: {
                labels: pmLabels,
                datasets: [{
                    data: pmData,
                    backgroundColor: ['#28a745','#007bff','#ffc107','#6f42c1','#6f42c1']
                }]
            },
            options: {
                responsive: true,
                cutout: '60%',
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(ctx){
                                const label = ctx.label || '';
                                const val = ctx.raw || 0;
                                if (typeof val === 'number' && val > 100) {
                                    return `${label}: ₹${Number(val).toLocaleString()}`;
                                }
                                return `${label}: ${val}`;
                            }
                        }
                    }
                }
            }
        });
    }

    // console.debug({ labels, ordersData, revenueData, paymentsData, usersData, revLabels, revData, pmLabels, pmData });
});
</script>

@endsection
