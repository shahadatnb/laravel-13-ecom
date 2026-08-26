@extends('admin.layouts.app')
@section('title', 'Edit Profile')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/admin/plugins/jquery-ui/jquery-ui.css') }}">
@endpush

@section('content')
<div class="row">
    @include('admin.profile.leftSidebar')
    <div class="col-md-9">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Edit Profile</a></li>
                </ul>
            </div>
            <div class="card-body">
                @include('admin.layouts._message')
                <div class="tab-content">
                    <div class="active tab-pane" id="activity">
                        <form method="POST" action="{{ route('admin.profile.update') }}" class="form-horizontal">
                            @csrf
                            @method('PATCH')

                            <div class="form-group row">
                                <label for="name" class="col-sm-2 col-form-label">Name</label>
                                <div class="col-sm-10">
                                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="form-control" />
                                    @error('name')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="email" class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="form-control" />
                                    @error('email')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="phone" class="col-sm-2 col-form-label">Phone</label>
                                <div class="col-sm-10">
                                    <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}" class="form-control" />
                                    @error('phone')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="address" class="col-sm-2 col-form-label">Address</label>
                                <div class="col-sm-10">
                                    <textarea id="address" name="address" class="form-control">{{ old('address', $user->address) }}</textarea>
                                    @error('address')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="bio" class="col-sm-2 col-form-label">Bio</label>
                                <div class="col-sm-10">
                                    <textarea id="bio" name="bio" class="form-control">{{ old('bio', $user->bio) }}</textarea>
                                    @error('bio')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="date_of_birth" class="col-sm-2 col-form-label">Date of Birth</label>
                                <div class="col-sm-10">
                                    <input id="date_of_birth" name="date_of_birth" type="text" value="{{ old('date_of_birth', $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('d-m-Y') : '') }}" class="form-control datepicker" placeholder="dd-mm-yyyy" autocomplete="off" />
                                    @error('date_of_birth')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="gender" class="col-sm-2 col-form-label">Gender</label>
                                <div class="col-sm-10">
                                    <select id="gender" name="gender" class="form-control">
                                        <option value="">Select gender</option>
                                        <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="timezone" class="col-sm-2 col-form-label">Timezone</label>
                                <div class="col-sm-10">
                                    <input id="timezone" name="timezone" type="text" value="{{ old('timezone', $user->timezone) }}" class="form-control" />
                                    @error('timezone')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="locale" class="col-sm-2 col-form-label">Locale</label>
                                <div class="col-sm-10">
                                    <input id="locale" name="locale" type="text" value="{{ old('locale', $user->locale) }}" class="form-control" />
                                    @error('locale')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="offset-sm-2 col-sm-10">
                                    <button type="submit" class="btn btn-success">Update</button>
                                </div>
                            </div>
                        </form>

                        <hr>

                        <form method="POST" action="{{ route('admin.profile.password') }}" class="form-horizontal">
                            @csrf
                            <div class="form-group row">
                                <label for="password" class="col-sm-2 col-form-label">New Password</label>
                                <div class="col-sm-10">
                                    <input id="password" name="password" type="password" class="form-control" placeholder="New Password" />
                                    @error('password')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="password_confirmation" class="col-sm-2 col-form-label">Confirm Password</label>
                                <div class="col-sm-10">
                                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" placeholder="Confirm Password" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="offset-sm-2 col-sm-10">
                                    <button type="submit" class="btn btn-success">Update Password</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/admin/plugins/jquery-ui/jquery-ui.js') }}"></script>
<script>
$(function () {
    $('.datepicker').datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '-100:+0',
        maxDate: new Date(),
    });
});
</script>
@endpush