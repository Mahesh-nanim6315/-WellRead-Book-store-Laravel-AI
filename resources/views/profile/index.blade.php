@vite(['resources/css/app.css', 'resources/js/app.js'])

<div class="profile-header">

<div class="profile-cover">

    <form action="{{ route('profile.cover') }}" 
          method="POST" 
          enctype="multipart/form-data">
        @csrf

        <input type="file" 
               name="cover" 
               id="coverInput" 
               hidden>

        <img src="{{ $user->cover 
                ? asset('storage/'.$user->cover) 
                : asset('images/default-cover.jpg') }}"
             class="cover-img">

        <button type="button" 
                class="change-cover-btn"
                onclick="document.getElementById('coverInput').click()">
            Change Cover
        </button>
    </form>

</div>


<form action="{{ route('profile.avatar') }}" 
      method="POST" 
      enctype="multipart/form-data">
    @csrf

    <div class="avatar-upload">
        <input type="file" name="avatar" id="avatarInput" hidden>
        
        <img src="{{ $user->avatar 
            ? asset('storage/' . $user->avatar) 
            : asset('images/default-avatar.png') }}" 
            id="avatarPreview"
            class="avatar-img">

        <button type="button" onclick="document.getElementById('avatarInput').click()">
            Change Avatar
        </button> 
    </div>
            <h2 style="position: absolute; top: 330px; left: 655px; bottom: 30px;">{{ $user->name }}</h2>
</form>


   <div style="text-align: start; position: absolute; bottom: 20px; left: 40px; top: 300px;">
        <div style="font-weight: 600; font-size: 16px;">
            <p>{{ $user->email }}</p>
        </div>

        <div class="badges-container" style="display: flex; gap: 10px;">
            <span class="role-badges">
                {{ ucfirst($user->role) }}
            </span>

            @if($user->hasActiveSubscription())
                <span class="plan-badges">
                    {{ ucfirst($user->plan) }} Plan
                </span>
            @else
                <span class="free-badges">
                    Free Plan
                </span>
            @endif
        </div>
    
    </div>


    <div style="position: absolute; top: 310px; right: 180px; text-align: right;">
        <h4>Profile Completion</h4>

            <div class="progress-bars">
                <div class="progress-fills" 
                    style="width: {{ $completion }}%;">
                </div>
            </div> 

        <span>{{ $completion }}% Completed</span>
        
    </div>
    <div style="position: absolute; top: 310px; right: 40px; text-align: right;">
        <button class="edit-btns" onclick="openModal()">
            Edit Profile
        </button>
    </div>

</div>





<div class="profile-stats">

    <div class="stat-card">
        <h3>{{ $totalOrders }}</h3>
        <p>Books Purchased</p>
    </div>

    <div class="stat-card">
        <h3>{{ $wishlistCount }}</h3>
        <p>Wishlist Items</p>
    </div>

    <div class="stat-card">
        <h3>{{ $reviewCount }}</h3>
        <p>Reviews Written</p>
    </div>

    <div class="stat-card">
        <h3>
            {{ $user->hasActiveSubscription() ? ucfirst($user->plan) : 'Free' }}
        </h3>
        <p>Current Plan</p>
    </div>

</div>


<div class="recent-purchases">
    <h3>Recent Purchases</h3>

    @if($recentBooks->count())

        <div class="books-grid">
            @foreach($recentBooks as $item)
                <div class="book-card">
                    <img src="{{ $item->book->image }}" alt="Book Cover">

                    <div class="book-info">
                        <h4>{{ $item->book->name }}</h4>
                        <p>{{ $item->book->author->name }}</p>

                        <a href="{{ route('books.show', $item->book->id) }}" 
                           class="view-btn">
                            View Book
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

    @else
        <p class="no-books">
            You haven't purchased any books yet.
        </p>
    @endif
</div>



<div class="subscription-section">

    <h3>Subscription Management</h3>

    @if($user->subscribed('default'))
        <p>You are subscribed to {{ ucfirst($user->plan) }} plan.</p>

        @if(auth()->user()->subscribed('default'))

        @if(auth()->user()->subscription('default')->onGracePeriod())

            <form method="POST" action="{{ route('subscription.resume') }}">
                @csrf
                <button class="resume-btn">
                    Resume Subscription
                </button>
            </form>

            <p>
                Your subscription will end on 
                {{ auth()->user()->subscription('default')->ends_at->format('M d, Y') }}
            </p>

        @else

        <form method="POST" action="{{ route('subscription.cancel') }}">
            @csrf
            <button class="cancel-btn">
                Cancel Subscription
            </button>
        </form>

    @endif

@endif


        <form method="POST" action="{{ route('subscription.cancel') }}">
            @csrf
            <button class="cancel-btn">Cancel Subscription</button>
        </form>

    @else
        <a href="{{ route('plans.index') }}" class="upgrade-btn">
            Upgrade to Premium
        </a>
    @endif

</div>

<div id="editProfileModal" class="modal">

    <div class="modal-content">

        <div class="modal-header">
            <h3>Edit Profile</h3>
            <span class="close-btn" onclick="closeModal()">&times;</span>
        </div>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" 
                       value="{{ $user->name }}" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" 
                       value="{{ $user->email }}" required>
            </div>

            <hr>

            <h4>Change Password (Optional)</h4>

            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="password">
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation">
            </div>

            <button type="submit" class="save-btn">
                Save Changes
            </button>

        </form>
    </div>
</div>

<script>
document.getElementById('coverInput').addEventListener('change', function() {
    this.form.submit();
});
</script>


<script>
function openModal() {
    document.getElementById('editProfileModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('editProfileModal').style.display = 'none';
}

// Close when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('editProfileModal');
    if (event.target === modal) {
        modal.style.display = "none";
    }
}
</script>


<script>
document.getElementById('avatarInput').addEventListener('change', function() {
    this.form.submit();
});
</script>
