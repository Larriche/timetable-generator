@extends('layouts.app')

@section('title')
Invigilators Report
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-12">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 page-title">
                <h1><span class="fa fa-file"></span> Invigilators Report</h1>
            </div>
        </div>

        <div class="x_panel">
            <div class="x_title">
                <h4>View exams scheduled within a chosen date period to be invigilated by selected invigilator</h4>
            </div>

            <div class="x_content">
                <input type="hidden" name="report_type" value="invigilators">

                <form id="reports-form">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-3">
                            <label>From</label>

                            <div class="form-group">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control datepicker" name="start_date">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label>To</label>

                            <div class="form-group">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control datepicker" name="end_date">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label>Invigilator</label>

                            <div class="select2-wrapper">
                                <select name="professor_id"
                                    class="form-control select2"
                                    id="block-select"
                                    data-placeholder="Select a lecturer">
                                    @foreach ($professors as $professor)
                                        <option value="{{ $professor->id }}">{{ $professor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <button class="btn btn-block btn-success" style="margin-top: 24px" id="generate-button">Run Report</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-10 col-md-offset-1">
                            <div id="errors-container">
                                @include('partials.modal_errors')
                            </div>
                        </div>
                    </div>
                </form>

                <div class="page-body" id="resource-container">
                    @include('reports.invigilators_table')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{URL::asset('/js/reports/index.js')}}"></script>
@endsection