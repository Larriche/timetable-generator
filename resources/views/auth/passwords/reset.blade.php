@extends('layouts.app')

@section('content')
    <div class="row container">
        <div class="col-md-2 col-md-offset-5">
            <div id="login-wrapper">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 col-lg-offset-3 col-md-offset-3 col-sm-offset-3 col-xs-offset-2">
                        <h1 class="align_center"><b>timetable</b></h1>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <p id="undertext" class="align_center">Reset Password</p>

                        <form method="POST" action="{{ URL::to('/request_reset') }}">
                            {!! csrf_field() !!}
                            @include('errors.form_errors')

                            <div class="form-group">
                                <label>Enter your email</label>
                                <input type="text" class="form-control" placeholder="Email" name="email">
                            </div>

                            <div class="form-group">
                                <p>{{ $user->security_question->question }}</p>
                            </div>

                            <div class="form-group">
                                <label>Your Answer</label>
                                <input type="text" class="form-control" name="security_question_answer">
                            </div>

                            <div class="form-group">
                                <input type="submit" name="submit" value="SUBMIT" class="btn btn-lg btn-block btn-primary">
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
