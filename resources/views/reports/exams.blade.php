@extends('layouts.app')

@section('title')
Examinations Schedule Report
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-12">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 page-title">
                <h1><span class="fa fa-file"></span> Exams Report</h1>
            </div>
        </div>

        <div class="x_panel">
            <div class="x_title">
                <h4>View exams scheduled within a chosen date period</h4>
            </div>

            <div class="x_content">
                <input type="hidden" name="report_type" value="exams">
                @include('reports.config')

                <div class="page-body" id="resource-container">
                    @include('reports.exams_table')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{URL::asset('/js/reports/index.js')}}"></script>
@endsection