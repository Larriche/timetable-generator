@extends('layouts.app')

@section('title')
Manage courses for {{ $class->name }}
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-12">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 page-title">
                <h1><span class="fa fa-book"></span> Manage courses for {{ $class->name }}</h1>
            </div>
        </div>

        <div class="x_panel">
            <div class="x_title">
                <h4>Manage the courses offered by this class</h4>
            </div>

            <div class="x_content">
                @include('partials.menu_bar', ['buttonTitle' => 'Add Course'])

                <div class="page-body" id="resource-container">
                    @include('courses_manager.table')
                </div>
            </div>
        </div>
    </div>
</div>

@include('courses_manager.modals')
@endsection

@section('scripts')
<script src="{{URL::asset('/js/courses_manager/index.js')}}"></script>
@endsection