@extends('layouts.app')

@section('title')
Lecturers
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-12">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 page-title">
                <h1><span class="fa fa-graduation-cap"></span> Lecturers</h1>
            </div>
        </div>

        <div class="x_panel">
            <div class="x_title">
                <h4>Manage information about lecturers</h4>
            </div>

            <div class="x_content">
                @include('partials.menu_bar', ['buttonTitle' => 'Add New Lecturer'])

                <div class="page-body" id="resource-container">
                    @include('professors.table')
                </div>
            </div>
        </div>
    </div>
</div>

@include('professors.modals')
@endsection

@section('scripts')
<script src="{{URL::asset('/js/professors/index.js')}}"></script>
@endsection