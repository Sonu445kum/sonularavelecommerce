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
                $cards = [
                    ['icon'=>'bi-box-seam','title'=>'Total Products','value'=>$totalProducts ?? 0,'bg'=>'linear-gradient(135deg,#6a11cb,#2575fc)'],
                    ['icon'=>'bi-bag-check','title'=>'Total Orders','value'=>$totalOrders ?? 0,'bg'=>'linear-gradient(135deg,#ff416c,#ff4b2b)'],
                    ['icon'=>'bi-tags','title'=>'Total Categories','value'=>$totalCategories ?? 0,'bg'=>'linear-gradient(135deg,#00c6ff,#0072ff)'],
                    ['icon'=>'bi-people','title'=>'Total Users','value'=>$totalUsers ?? 0,'bg'=>'linear-gradient(135deg,#f7971e,#ffd200)'],
                    ['icon'=>'bi-currency-rupee','title'=>'Total Revenue','value'=>'₹'.number_format($totalRevenue ?? 0,2),'bg'=>'linear-gradient(135deg,#43e97b,#38f9d7)'],
                    ['icon'=>'bi-wallet2','title'=>'Successful Payments','value'=>$successfulPayments ?? 0,'bg'=>'linear-gradient(135deg,#ffb75e,#ed8f03)'],
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
                <h5 class="fw-bold mb-3">Growth Rate</h5>
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

{{-- ==================== Revenue Pie Chart ==================== --}}
@php
    $revenueLabels = $revenueDistributionLabels ?? ['Product A','Product B','Product C','Product D','Product E'];
    $revenueData   = $revenueDistributionData ?? [120,90,70,50,30];
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

{{-- ============================ CHARTS ============================= --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    // Animated Counters
    document.querySelectorAll('.counter').forEach(el=>{
        let value = parseFloat(el.textContent.replace(/[^0-9.]/g,''));
        let start = 0; 
        let duration = 1500;
        let increment = value / (duration / 16);
        function updateCounter(){
            start += increment;
            if(start >= value) el.textContent = el.textContent.includes('₹') ? '₹'+value.toFixed(2) : Math.floor(value);
            else { 
                el.textContent = el.textContent.includes('₹') ? '₹'+Math.floor(start) : Math.floor(start); 
                requestAnimationFrame(updateCounter);
            }
        }
        updateCounter();
    });

    // Charts...
    const dashboardChart = new Chart(document.getElementById('dashboardChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: @json($dashboardLabels ?? []),
            datasets: [
                { label: 'Orders', data: @json($ordersChartData ?? []), borderColor: '#007bff', backgroundColor:'rgba(0,123,255,0.2)', fill:true, tension:0.3 },
                { label: 'Revenue', data: @json($revenueChartData ?? []), borderColor: '#17a2b8', backgroundColor:'rgba(23,162,184,0.2)', fill:true, tension:0.3 },
                { label: 'Products', data: @json($productsChartData ?? []), borderColor: '#28a745', backgroundColor:'rgba(40,167,69,0.2)', fill:true, tension:0.3 },
                { label: 'Payments', data: @json($paymentsChartData ?? []), borderColor: '#ffc107', backgroundColor:'rgba(255,193,7,0.2)', fill:true, tension:0.3 }
            ]
        },
        options:{ responsive:true, interaction:{mode:'index',intersect:false}, stacked:false, plugins:{ legend:{ position:'top' } }, scales:{ y:{ beginAtZero:true }, x:{} } }
    });
    // Revenue Pie Chart
        const revenueCtx = document.getElementById('revenuePieChart').getContext('2d');

        // Agar chart already exist karta hai toh destroy karo
        if (window.revenueChart) window.revenueChart.destroy();

        const revenueColors = ['#007bff','#28a745','#ffc107','#dc3545','#17a2b8'];

        window.revenueChart = new Chart(revenueCtx, {
            type: 'pie',
            data: {
                labels: @json($revenueLabels),
                datasets: [{
                    data: @json($revenueData),
                    backgroundColor: revenueColors,
                    borderColor: '#fff',
                    borderWidth: 2,
                    hoverOffset: 20
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 15,
                            font: {
                                size: 14,
                                weight: '600'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                let total = context.dataset.data.reduce((a,b) => a + b, 0);
                                let percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    new Chart(document.getElementById('growthPieChart').getContext('2d'), {
        type:'doughnut',
        data:{ labels:['New Users','New Orders','Revenue Growth'], datasets:[{ data:@json($growthRateData ?? [30,20,50]), backgroundColor:['#28a745','#007bff','#ffc107'] }] },
        options:{ responsive:true, cutout:'60%', plugins:{ legend:{ position:'bottom' } } }
    });
});
</script>

@endsection
