@extends('admin.layouts.app')

@section('content')
<div class="page-header">
    <h2>Roles & Permissions</h2>
</div>

@if (session('success'))
    <div style="margin-bottom:15px;padding:10px;border:1px solid #86efac;background:#f0fdf4;color:#166534;border-radius:6px;">
        {{ session('success') }}
    </div>
@endif

<form action="{{ route('admin.roles_permissions.update') }}" method="POST">
    @csrf
    @method('PUT')

    @foreach($roles as $role)
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:16px;margin-bottom:16px;">
            <h3 style="margin-top:0;margin-bottom:12px;">{{ ucfirst($role) }}</h3>

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:10px;">
                @foreach($permissionLabels as $permissionKey => $permissionLabel)
                    <label style="display:flex;align-items:center;gap:8px;padding:8px;border:1px solid #e5e7eb;border-radius:6px;">
                        <input
                            type="checkbox"
                            name="permissions[{{ $role }}][]"
                            value="{{ $permissionKey }}"
                            {{ in_array($permissionKey, $rolePermissions[$role] ?? []) ? 'checked' : '' }}
                        >
                        <span>{{ $permissionLabel }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    @endforeach

    <button type="submit" class="btn-primary">Save Permissions</button>
</form>
@endsection

