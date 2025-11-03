@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">

        {{-- ===== HEADER ===== --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
                🔔 Notifications
            </h1>

            @if($notifications->where('is_read', false)->count() > 0)
                <form action="{{ route('notifications.readAll') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition font-medium">
                        Mark All as Read
                    </button>
                </form>
            @endif
        </div>

        {{-- ===== NOTIFICATIONS LIST ===== --}}
        @if($notifications->count() > 0)
            <div class="space-y-4">
                @foreach($notifications as $notification)
                    <div
                        class="bg-white rounded-lg shadow-sm p-4 border-l-4 transition
                        {{ $notification->is_read ? 'border-gray-300 opacity-75' : 'border-blue-600 hover:shadow-md' }}">
                        
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ $notification->title ?? 'Notification' }}
                                </h3>

                                <p class="text-gray-600 mt-1">
                                    {{ $notification->message ?? 'No message content available.' }}
                                </p>

                                <p class="text-sm text-gray-400 mt-2 flex items-center gap-1">
                                    <i class="far fa-clock"></i>
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>

                            {{-- Mark as Read Button --}}
                            @if(!$notification->is_read)
                                <button
                                    onclick="markAsRead({{ $notification->id }})"
                                    class="ml-4 text-blue-600 hover:text-blue-800 text-sm font-medium transition">
                                    Mark as Read
                                </button>
                            @else
                                <span class="ml-4 text-gray-400 text-sm flex items-center gap-1">
                                    <i class="fas fa-check-circle"></i> Read
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ===== PAGINATION ===== --}}
            <div class="mt-6">
                {{ $notifications->links() }}
            </div>

        @else
            {{-- ===== NO NOTIFICATIONS ===== --}}
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <i class="fas fa-bell-slash text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">No notifications yet</p>
            </div>
        @endif
    </div>
</div>

{{-- ===== JAVASCRIPT FOR AJAX ===== --}}
<script>
    function markAsRead(id) {
        fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        })
        .catch(err => console.error('Error:', err));
    }
</script>
@endsection
