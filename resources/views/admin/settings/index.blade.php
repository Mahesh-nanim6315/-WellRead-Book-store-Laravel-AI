@vite(['resources/css/settings.css', 'resources/js/app.js'])
@extends('admin.layouts.app')

@section('content')

<div class="settings-container" id="main-settings-container">
    <div class="settings-header">
        <h2 class="settings-title">Platform Settings</h2>
        <p class="settings-subtitle">Configure and manage your platform preferences</p>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" id="settings-form">
        @csrf

        <div class="settings-tabs-wrapper" id="settings-tabs-container">

            <!-- General Settings Card -->
            <div class="settings-card" id="general-settings-card">
                <div class="settings-card-header">
                    <h4 class="settings-card-title">General Settings</h4>
                    <span class="settings-card-icon">⚙️</span>
                </div>
                
                <div class="settings-card-body">
                    <div class="settings-form-group">
                        <label for="site_name" class="settings-label">Site Name</label>
                        <input type="text" 
                               id="site_name" 
                               name="site_name"
                               class="settings-input"
                               value="{{ \App\Models\Setting::get('site_name') }}"
                               placeholder="Enter site name">
                    </div>

                    <div class="settings-form-group">
                        <label for="support_email" class="settings-label">Support Email</label>
                        <input type="email" 
                               id="support_email" 
                               name="support_email"
                               class="settings-input"
                               value="{{ \App\Models\Setting::get('support_email') }}"
                               placeholder="support@example.com">
                    </div>

                    <div class="settings-form-group">
                        <label for="maintenance_mode" class="settings-label">Maintenance Mode</label>
                        <select id="maintenance_mode" 
                                name="maintenance_mode" 
                                class="settings-select">
                            <option value="0">Disabled</option>
                            <option value="1"
                                {{ \App\Models\Setting::get('maintenance_mode') == 1 ? 'selected' : '' }}>
                                Enabled
                            </option>
                        </select>
                        <small class="settings-help-text">When enabled, only admins can access the site</small>
                    </div>
                </div>
            </div>

            <!-- Subscription Settings Card -->
            <div class="settings-card" id="subscription-settings-card">
                <div class="settings-card-header">
                    <h4 class="settings-card-title">Subscription Settings</h4>
                    <span class="settings-card-icon">💳</span>
                </div>
                
                <div class="settings-card-body">
                    <div class="settings-form-group">
                        <label for="subscriptions_enabled" class="settings-label">Enable Subscriptions</label>
                        <select id="subscriptions_enabled" 
                                name="subscriptions_enabled" 
                                class="settings-select">
                            <option value="1">Yes</option>
                            <option value="0"
                                {{ \App\Models\Setting::get('subscriptions_enabled') == 0 ? 'selected' : '' }}>
                                No
                            </option>
                        </select>
                    </div>

                    <div class="settings-form-group">
                        <label for="free_trial_days" class="settings-label">Free Trial Days</label>
                        <input type="number" 
                               id="free_trial_days" 
                               name="free_trial_days"
                               class="settings-input"
                               value="{{ \App\Models\Setting::get('free_trial_days', 7) }}"
                               min="0"
                               max="365">
                    </div>
                </div>
            </div>

            <!-- Content Settings Card -->
            <div class="settings-card" id="content-settings-card">
                <div class="settings-card-header">
                    <h4 class="settings-card-title">Content Settings</h4>
                    <span class="settings-card-icon">📚</span>
                </div>
                
                <div class="settings-card-body">
                    <div class="settings-form-group">
                        <label for="auto_approve_reviews" class="settings-label">Auto Approve Reviews</label>
                        <select id="auto_approve_reviews" 
                                name="auto_approve_reviews" 
                                class="settings-select">
                            <option value="1">Yes</option>
                            <option value="0"
                                {{ \App\Models\Setting::get('auto_approve_reviews') == 0 ? 'selected' : '' }}>
                                No
                            </option>
                        </select>
                    </div>

                    <div class="settings-form-group">
                        <label for="books_per_page" class="settings-label">Books Per Page</label>
                        <input type="number" 
                               id="books_per_page" 
                               name="books_per_page"
                               class="settings-input"
                               value="{{ \App\Models\Setting::get('books_per_page', 12) }}"
                               min="5"
                               max="100">
                    </div>
                </div>
            </div>

        </div>

        <div class="settings-actions" id="settings-form-actions">
            <button type="submit" class="settings-btn settings-btn-primary" id="save-settings-btn">
                <span class="btn-icon">💾</span>
                Save Settings
            </button>
            <button type="reset" class="settings-btn settings-btn-secondary" id="reset-settings-btn">
                <span class="btn-icon">↩️</span>
                Reset
            </button>
        </div>
    </form>
</div>

@endsection