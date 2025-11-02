@extends('layouts.admin')

@section('title', 'User Wishlists')

@section('content')
<div class="container-fluid py-4">

    {{-- 🧭 Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">
            <i class="fas fa-heart text-danger me-2"></i> All User Wishlists
        </h3>
        <form class="d-flex" method="GET" action="{{ route('admin.wishlist.index') }}">
            <input type="text" name="search" class="form-control me-2" placeholder="Search by user or product..." value="{{ request('search') }}">
            <button class="btn btn-primary"><i class="fas fa-search"></i></button>
        </form>
    </div>

    {{-- 📋 Wishlist Table --}}
    <div class="card shadow border-0 rounded-4">
        <div class="card-body">
            @if($wishlists->count() > 0)
                <div class="table-responsive">
                    <table class="table align-middle table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Date Added</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($wishlists as $wishlist)
                                <tr id="wishlist-row-{{ $wishlist->id }}">
                                    <td>{{ $loop->iteration + ($wishlists->currentPage() - 1) * $wishlists->perPage() }}</td>
                                    <td>
                                        <div>
                                            <strong>{{ $wishlist->user->name }}</strong><br>
                                            <small class="text-muted">{{ $wishlist->user->email }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @php
                                                $imageUrl = $wishlist->product->featured_image 
                                                    ? (filter_var($wishlist->product->featured_image, FILTER_VALIDATE_URL) 
                                                        ? $wishlist->product->featured_image 
                                                        : asset('storage/' . $wishlist->product->featured_image))
                                                    : ($wishlist->product->images->first() 
                                                        ? asset('storage/' . $wishlist->product->images->first()->path)
                                                        : asset('images/default-product.jpg'));
                                            @endphp
                                            <img src="{{ $imageUrl }}" 
                                                 alt="{{ $wishlist->product->title ?? 'Product' }}"
                                                 class="rounded me-2" 
                                                 width="50" 
                                                 height="50" 
                                                 style="object-fit: cover;"
                                                 onerror="this.src='{{ asset('images/default-product.jpg') }}'">
                                            <div>
                                                <strong>{{ $wishlist->product->title ?? $wishlist->product->name ?? 'N/A' }}</strong><br>
                                                <small class="text-muted">{{ Str::limit($wishlist->product->description ?? '', 40) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        ₹{{ number_format($wishlist->product->price, 2) }}
                                    </td>
                                    <td>
                                        {{ $wishlist->created_at->format('d M Y, h:i A') }}
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-danger remove-wishlist"
                                                data-id="{{ $wishlist->id }}">
                                            <i class="fas fa-trash-alt"></i> Remove
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- 🔄 Pagination --}}
                <div class="mt-3 d-flex justify-content-center">
                    {{ $wishlists->links('pagination::bootstrap-5') }}
                </div>
                
                {{-- Wishlist Count Info --}}
                <div class="mt-2 text-center text-muted">
                    <small>Showing {{ $wishlists->firstItem() ?? 0 }} to {{ $wishlists->lastItem() ?? 0 }} of {{ $wishlists->total() }} wishlist items</small>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-heart-broken text-danger fs-1 mb-3"></i>
                    <h5 class="fw-bold">No wishlist records found</h5>
                    <p class="text-muted">Users haven’t added any products to their wishlist yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- 💅 Styles --}}
<style>
.table thead th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 13px;
}
.table td {
    vertical-align: middle;
}
.remove-wishlist {
    border-radius: 20px;
    transition: 0.2s ease-in-out;
}
.remove-wishlist:hover {
    transform: scale(1.05);
}
</style>

{{-- ⚡ AJAX Delete Script --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const removeButtons = document.querySelectorAll('.remove-wishlist');

    removeButtons.forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;

            // Confirm first
            const confirm = await Swal.fire({
                title: 'Are you sure?',
                text: "You want to remove this wishlist item?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            });

            if (!confirm.isConfirmed) return;

            try {
                const res = await fetch(`/admin/wishlist/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await res.json();

                if (data.status) {
                    document.getElementById(`wishlist-row-${id}`).remove();
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message || 'Wishlist item removed',
                        showConfirmButton: false,
                        timer: 2000
                    });
                } else {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: data.message || 'Error deleting wishlist item',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }

            } catch (err) {
                console.error(err);
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'Something went wrong!',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        });
    });
});
</script>
@endsection
