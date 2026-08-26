<div class="col-md-3">
    <div class="card card-primary card-outline">
        <div class="card-body box-profile">
            <div class="text-center">
                 <img class="profile-user-img img-fluid img-circle"
                      src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'User') }}&background=0D8ABC&color=fff&size=200"
                      alt="{{ Auth::user()->name }}">
            </div>
            <h3 class="profile-username text-center">{{ Auth::user()->name }}</h3>
            <p class="text-muted text-center">{{ Auth::user()->email }}</p>
        </div>
    </div>

    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">About</h3>
        </div>
        <div class="card-body">
            <strong><i class="fas fa-phone mr-1"></i> Phone</strong>
            <p class="text-muted">{{ Auth::user()->phone ?? 'N/A' }}</p>
            <hr>
            <strong><i class="fas fa-map-marker-alt mr-1"></i> Address</strong>
            <p class="text-muted">{{ Auth::user()->address ?? 'N/A' }}</p>
            <hr>
            <strong><i class="fas fa-calendar mr-1"></i> Date of Birth</strong>
            <p class="text-muted">{{ Auth::user()->date_of_birth ? \Carbon\Carbon::parse(Auth::user()->date_of_birth)->format('d-m-Y') : 'N/A' }}</p>
            <hr>
            <strong><i class="fas fa-venus-mars mr-1"></i> Gender</strong>
            <p class="text-muted">{{ ucfirst(Auth::user()->gender ?? 'N/A') }}</p>
        </div>
    </div>
</div>
