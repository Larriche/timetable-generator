@extends('layouts.app')

@section('title')
Classes
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-12">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 page-title">
                <h1><span class="fa fa-users"></span> Classes</h1>
            </div>
        </div>

        <div class="x_panel">
            <div class="x_title">
                <h4>Manage the various classes in the college</h4>
            </div>

            <div class="x_content">
                @include('partials.menu_bar', ['buttonTitle' => 'Add New Class'])

                <div class="page-body" id="resource-container">
                    @include('classes.table')
                </div>
            </div>
        </div>
    </div>
</div>

@include('classes.modals')
@endsection

@section('scripts')
<script src="{{URL::asset('/js/classes/index.js')}}"></script>
@endsection