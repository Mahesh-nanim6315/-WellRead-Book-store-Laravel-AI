 @vite(['resources/css/app.css'])
<section class="delete-section" style="margin-top: 100px;">
    <header class="delete-header">
        <h2 class="delete-title">
            {{ __('Delete Account') }}
        </h2>

        <p class="delete-subtitle">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="delete-btn"
    >{{ __('Delete Account') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="delete-form">
            @csrf
            @method('delete')

            <h2 class="delete-title">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="delete-subtitle">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="form-groups">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />
                <x-text-input id="password" name="password" type="password" class="form-input"
                    placeholder="{{ __('Password') }}" />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="form-error" />
            </div>

            <div class="form-actions">
                <x-secondary-button x-on:click="$dispatch('close')" class="cancel-btn">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="confirm-btn">
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
