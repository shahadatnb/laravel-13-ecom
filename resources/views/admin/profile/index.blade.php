@extends('admin.layouts.app')
@section('title', 'My Profile')

@section('content')
<div class="row">
    @include('admin.profile.leftSidebar')
    <div class="col-md-9">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Profile Info</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.profile.edit') }}">Profile Edit</a></li>
                </ul>
            </div>
            <div class="card-body">
                @include('admin.layouts._message')
                <div class="tab-content">
                    <div class="active tab-pane" id="activity">
                        <table class="table">
                            <tr>
                                <td>Name</td>
                                <td>{{ Auth::user()->name }}</td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td>{{ Auth::user()->email }}</td>
                            </tr>
                            <tr>
                                <td>Phone</td>
                                <td>{{ Auth::user()->phone ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Bio</td>
                                <td>{{ Auth::user()->bio ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Gender</td>
                                <td>{{ ucfirst(Auth::user()->gender ?? 'N/A') }}</td>
                            </tr>
                            <tr>
                                <td>Timezone</td>
                                <td>{{ Auth::user()->timezone ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Locale</td>
                                <td>{{ Auth::user()->locale ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
