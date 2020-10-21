@extends('layouts.app')

@section('title')
Timetables
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-12">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 page-title">
                <h1><span class="fa fa-calendar"></span> Timetables</h1>
            </div>
        </div>

        <div class="x_panel">
            <div class="x_title">
                <h4>View list of timetables generated</h4>
            </div>

            <div class="x_content">
                <div class="row menu-bar">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="input-group">
                            <input type="text" class="form-control input-custom-height" placeholder="Enter a keyword to search" name="search_term">

                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-success input-custom-height" id="search-button"><i class="fa fa-search" title="Search"></i></button>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="page-body" id="resource-container">
                    @include('timetables.table')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{URL::asset('/js/timetables/index.js')}}"></script>
@endsection