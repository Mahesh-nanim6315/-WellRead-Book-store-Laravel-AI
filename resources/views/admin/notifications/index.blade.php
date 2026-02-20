@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h3>Notifications</h3>

    @forelse($notifications as $notification)
        <div class="card mb-2 {{ $notification->read_at ? '' : 'border-primary' }}">
            <div class="card-body">
                <a href="{{ route('admin.notifications.read', $notification->id) }}">
                    {{ $notification->data['message'] ?? 'Notification' }}
                </a>
                <br>
                <small class="text-muted">
                    {{ $notification->created_at->diffForHumans() }}
                </small>
            </div>
        </div>
    @empty
        <p>No notifications</p>
    @endforelse
</div>
@endsection
