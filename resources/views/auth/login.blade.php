@extends('layouts.app')

@section('title')
	Login
@endsection

@section('content')
    <div class="row container">
        <div class="col-md-6 col-md-offset-3">
            <div id="login-wrapper">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="app-heading text-center">KNUST ACADEMIC TIMETABLE SCHEDULING SYSTEM</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <h1 class="text-center"><b>Log in</b></h1>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 col-lg-offset-4 col-md-offset-4">
                        <p id="undertext" class="align_center">Please enter your admin credentials</p>

                        <form method="POST" action="{{ URL::to('/login') }}">
                            {!! csrf_field() !!}
                            @include('errors.form_errors')

                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" class="form-control" placeholder="Password" name="password">
                            </div>

                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <input type="submit" name="submit" value="Log in" class="btn btn-md btn-block btn-success">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="text-center">
                                    <a href="/request_reset">Forgot Password?</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    @include('partials.scripts')
    @yield('scripts')
@endsection