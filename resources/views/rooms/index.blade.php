@extends('layouts.app')

@section('title')
Rooms
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-12">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 page-title">
                <h1><span class="fa fa-home"></span> Rooms</h1>
            </div>
        </div>

        <div class="x_panel">
            <div class="x_title">
                <h4>Manage the rooms available in the college</h4>
            </div>

            <div class="x_content">
                @include('partials.menu_bar', ['buttonTitle' => 'Add New Room'])

                <div class="page-body" id="resource-container">
                    @include('rooms.table')
                </div>
            </div>
        </div>
    </div>
</div>

@include('rooms.modals')
@endsection

@section('scripts')
<script src="{{URL::asset('/js/rooms/index.js')}}"></script>
@endsection