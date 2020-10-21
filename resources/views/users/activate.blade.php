@extends('layouts.app')

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
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 col-lg-offset-4 col-md-offset-4">
                        <p class="align_center">Activate Account</p>

                        <form method="POST" action="{{ URL::to('/users/activate') }}">
                            {!! csrf_field() !!}
                            @include('errors.form_errors')

                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" class="form-control" placeholder="Name" name="name" value="{{ $user->name }}">
                            </div>

                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" class="form-control" placeholder="Password" name="password">
                            </div>

                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input type="password" class="form-control" placeholder="Password" name="password_confirmation">
                            </div>

                            <div class="form-group">
                                <label>Security Question</label>

                                <div class="select2-wrapper">
                                    <select name="security_question_id" class="form-control select2">
                                        @foreach ($questions as $question)
                                        <option value="{{ $question->id }}">{{ $question->question }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Your Answer</label>
                                <input type="text" class="form-control" name="security_question_answer">
                            </div>

                            <div class="form-group">
                                <input type="submit" name="submit" value="ACTIVATE ACCOUNT" class="btn btn-md btn-block btn-success">
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
