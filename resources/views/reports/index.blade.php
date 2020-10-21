@extends('layouts.app')

@section('title')
Reports
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-12">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 page-title">
                <h1><span class="fa fa-file"></span> List of reports</h1>
            </div>
        </div>

        <div class="x_panel">
            <div class="x_title">
                <h4>View various reports available</h4>
            </div>

            <div class="x_content">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <table class="table">
                            <thead>
                                <th>Report Name</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>Examination Attendance Report</td>
                                    <td>View attendance for various exam sittings</td>
                                    <td><a href="/reports/attendance"><i class="fa fa-cog"></i>  Run Report</a></td>
                                </tr>

                                <tr>
                                    <td>Examinations Schedule Report</td>
                                    <td>View exams scheduled within a chosen date period</td>
                                    <td><a href="/reports/exams"><i class="fa fa-cog"></i>  Run Report</a></td>
                                </tr>

                                <tr>
                                    <td>Examination Incidence Report</td>
                                    <td>View incident records for various exam sittings</td>
                                    <td><a href="/reports/incidents"><i class="fa fa-cog"></i>  Run Report</a></td>
                                </tr>

                                <tr>
                                    <td>Invigilators Schedule Report</td>
                                    <td>View exams scheduled within a chosen date period to be invigilated by selected invigilator</td>
                                    <td><a href="/reports/invigilators"><i class="fa fa-cog"></i>  Run Report</a></td>
                                </tr>

                                <tr>
                                    <td>Lecturers Schedule Report</td>
                                    <td>View lecture schedules for a selected lecturer</td>
                                    <td><a href="/reports/lecturers"><i class="fa fa-cog"></i>  Run Report</a></td>
                                </tr>

                                <tr>
                                    <td>Examination Venues Report</td>
                                    <td>View exams scheduled within a chosen date period to be conducted at a selected venue</td>
                                    <td><a href="/reports/rooms"><i class="fa fa-cog"></i>  Run Report</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{URL::asset('/js/reports/index.js')}}"></script>
@endsection