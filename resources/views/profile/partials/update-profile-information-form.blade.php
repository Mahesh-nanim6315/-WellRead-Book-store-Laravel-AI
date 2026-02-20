 @vite(['resources/css/app.css'])
<section class="profile-section">
    <header class="profile-header">
        <h2 class="profile-title">
            {{ __('Profile Information') }}
        </h2>

        <p class="profile-subtitle">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="profile-form">
        @csrf
        @method('patch')

        <div class="form-group">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="form-input"
                :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="form-error" :messages="$errors->get('name')" />
        </div>

        <div class="form-group">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="form-input"
                :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="form-error" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="verification-box">
                    <p class="verification-text">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="verification-btn">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="verification-success">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="form-actions">
            <x-primary-button class="save-btn">{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                   x-init="setTimeout(() => show = false, 2000)"
                   class="save-message">
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>
