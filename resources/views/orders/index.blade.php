@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="container mx-auto px-4 py-10">

    <h1 class="text-3xl font-bold text-gray-800 mb-8">My Orders</h1>

    {{-- If no orders --}}
    @if($orders->isEmpty())
        <div class="text-center py-10 bg-white rounded-xl shadow-sm">
            <h2 class="text-xl text-gray-600 mb-2">You haven’t placed any orders yet.</h2>
            <a href="{{ route('home') }}" class="text-indigo-600 font-semibold hover:underline">Start Shopping →</a>
        </div>
    @else
        {{-- Orders Table --}}
        <div class="overflow-x-auto bg-white rounded-xl shadow-md">
            <table class="w-full table-auto border-collapse">
                <thead class="bg-gray-100 text-gray-700 text-sm uppercase font-semibold">
                    <tr>
                        <th class="px-6 py-3 text-left">Order ID</th>
                        <th class="px-6 py-3 text-left">Date</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Total</th>
                        <th class="px-6 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @foreach($orders as $order)
                        <tr class="border-t hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold text-indigo-600">
                                #{{ $order->id }}
                            </td>
                            <td class="px-6 py-4">{{ $order->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'Pending' => 'bg-yellow-100 text-yellow-800',
                                        'Shipped' => 'bg-blue-100 text-blue-800',
                                        'Delivered' => 'bg-green-100 text-green-800',
                                        'Cancelled' => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-semibold">₹{{ number_format($order->total, 2) }}</td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('orders.show', $order->id) }}" 
                                   class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition text-sm">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
