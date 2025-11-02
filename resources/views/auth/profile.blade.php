@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container py-5">
    <div class="row">
        {{-- ===== Sidebar ===== --}}
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    {{-- User Avatar --}}
                    <div class="position-relative d-inline-block">
                        <img src="{{ $user->avatar ?? 'https://cdn-icons-png.flaticon.com/512/847/847969.png' }}" 
                             alt="User Avatar" class="rounded-circle mb-3" width="100" height="100">

                        {{-- Edit Icon --}}
                        <form action="{{ route('profile.avatar.update') }}" method="POST" enctype="multipart/form-data" class="position-absolute bottom-0 end-0">
                            @csrf
                            <label for="avatar" class="btn btn-sm btn-light border shadow-sm rounded-circle">
                                <i class="bi bi-pencil"></i>
                            </label>
                            <input type="file" name="avatar" id="avatar" class="d-none" onchange="this.form.submit()">
                        </form>
                    </div>

                    <h5 class="fw-bold mb-0">{{ $user->name }}</h5>
                    <small class="text-muted">{{ $user->email }}</small>
                </div>
            </div>

            <div class="list-group mt-4 shadow-sm">
                <a href="#profile-tab" class="list-group-item list-group-item-action active" data-bs-toggle="tab">
                    👤 My Profile
                </a>
                <a href="#orders-tab" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                    📦 My Orders
                </a>
                <a href="#wishlist-tab" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                    ❤️ Wishlist
                </a>
            </div>
        </div>

        {{-- ===== Main Content ===== --}}
        <div class="col-md-9">
            <div class="card shadow-sm border-0">
                <div class="card-body tab-content">

                    {{-- === Profile Info Tab === --}}
                    <div class="tab-pane fade show active" id="profile-tab">
                        <h4 class="mb-3">Profile Information</h4>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Full Name</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Email</label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">New Password</label>
                                    <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Confirm Password</label>
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm new password">
                                </div>
                            </div>

                            <div class="text-end">
                                <button class="btn btn-success px-4" type="submit">
                                    <i class="bi bi-save"></i> Update Profile
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- === Orders Tab === --}}
                    <div class="tab-pane fade" id="orders-tab">
                        <h4 class="mb-3">📦 My Orders</h4>

                        @if($orders->isEmpty())
                            <div class="alert alert-info">You haven't placed any orders yet.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Order #</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Total</th>
                                            <th>Items</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($orders as $order)
                                            <tr>
                                                <td>{{ $order->order_number }}</td>
                                                <td>{{ $order->created_at->format('d M Y') }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'pending' ? 'warning' : 'secondary') }}">
                                                        {{ ucfirst($order->status) }}
                                                    </span>
                                                </td>
                                                <td>₹{{ number_format($order->total, 2) }}</td>
                                                <td>
                                                    @foreach($order->items as $item)
                                                        <div class="d-flex align-items-center mb-2">
                                                            <img src="{{ asset($item->product->image ?? 'https://via.placeholder.com/50') }}" 
                                                                 class="rounded me-2" width="50" height="50">
                                                            <span>{{ $item->product->name ?? 'Unknown Product' }}</span>
                                                        </div>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    {{-- === Wishlist Tab === --}}
                    <div class="tab-pane fade" id="wishlist-tab">
                        <h4 class="mb-3">❤️ My Wishlist</h4>

                        @if($wishlist->isEmpty())
                            <div class="alert alert-info">You haven’t added anything to your wishlist yet.</div>
                        @else
                            <div class="row g-3">
                                @foreach($wishlist as $item)
                                    @php
                                        // Try featured_image first, then first image from images relationship, then fallback
                                        $imageUrl = null;
                                        if ($item->product) {
                                            if ($item->product->featured_image) {
                                                $imagePath = $item->product->featured_image;
                                                $imageUrl = (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://'))
                                                    ? $imagePath
                                                    : asset('storage/' . ltrim($imagePath, '/'));
                                            }
                                            // Fallback to first image in images relationship
                                            elseif ($item->product->images && $item->product->images->count() > 0) {
                                                $imagePath = $item->product->images->first()->path;
                                                $imageUrl = (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://'))
                                                    ? $imagePath
                                                    : asset('storage/' . ltrim($imagePath, '/'));
                                            }
                                        }
                                        // Final fallback
                                        $imageUrl = $imageUrl ?? asset('images/default-product.jpg');
                                        $productName = $item->product->title ?? $item->product->name ?? 'Product';
                                    @endphp
                                    <div class="col-md-4">
                                        <div class="card h-100 shadow-sm border-0">
                                            <img src="{{ $imageUrl }}" 
                                                 class="card-img-top" 
                                                 alt="{{ $productName }}"
                                                 onerror="this.src='{{ asset('images/default-product.jpg') }}'">
                                            <div class="card-body text-center">
                                                <h6 class="card-title">{{ $productName }}</h6>
                                                <p class="text-muted mb-2">₹{{ number_format($item->product->price, 2) }}</p>
                                                <a href="{{ route('products.show', $item->product->slug) }}" class="btn btn-sm btn-primary">
                                                    View Product
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- Bootstrap JS (Tabs) --}}
<script>
    // Auto switch tabs on sidebar click
    const tabTriggerList = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabTriggerList.forEach(tab => {
        tab.addEventListener('click', function (event) {
            event.preventDefault();
            new bootstrap.Tab(tab).show();
        });
    });
</script>
@endsection
