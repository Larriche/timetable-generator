@extends('layouts.app')

@section('title')
Periods
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-12">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 page-title">
                <h1><span class="fa fa-clock-o"></span> Periods</h1>
            </div>
        </div>

        <div class="x_panel">
            <div class="x_title">
                <h4>Manage the various time periods</h4>
            </div>

            <div class="x_content">
                @include('partials.menu_bar', ['buttonTitle' => 'Add New Period'])

                <div class="page-body" id="resource-container">
                    @include('timeslots.table')
                </div>
            </div>
        </div>
    </div>
</div>

@include('timeslots.modals')
@endsection

@section('scripts')
<script src="{{URL::asset('/js/timeslots/index.js')}}"></script>
@endsection