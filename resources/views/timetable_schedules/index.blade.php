@extends('layouts.app')

@section('title')
{{ $timetable->name }}
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-12">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 page-title">
                <h1><span class="fa fa-calendar"></span> {{ $timetable->name }}</h1>
            </div>
        </div>

        <div class="x_panel">
            <div class="x_title">
                @if ($timetable->type == 'EXAM')
                    <h4>View exams scheduled for {{ $timetable->name }}</h4>
                @else
                    <h4>View lectures timetable for {{ $timetable->name }}</h4>
                @endif
            </div>

            <div class="x_content">
                <div class="page-body" id="resource-container">
                    <div class="row margin-bottom">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Filter By Day</label>

                                <div class="select2-wrapper">
                                    <select name="date"
                                        class="form-control select2"
                                        id="date-select"
                                        data-placeholder="Select a day">
                                        <option value=""></option>
                                        @if ($timetable->type == 'EXAM')
                                            @foreach ($schedules as $date => $schedule_data)
                                                <option value="{{ $date }}">{{ date('l, jS M, Y', strtotime($date)) }}</option>
                                            @endforeach
                                        @else
                                            <?php
                                            $days = [
                                                'Monday',
                                                'Tuesday',
                                                'Wednesday',
                                                'Thursday',
                                                'Friday',
                                                'Saturday',
                                                'Sunday'
                                            ];
                                            ?>
                                            @foreach ($days as $day)
                                                @if (!in_array($day, explode(",", $timetable->days_exempted)))
                                                    <option value="{{ $day }}">{{ $day }}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Filter By Course</label>

                                <div class="select2-wrapper">
                                    <select name="course_id"
                                        class="form-control select2"
                                        id="course-select"
                                        data-placeholder="Select a course">
                                        @foreach ($courses as $course)
                                            <option value=""></option>
                                            <option value="{{ $course->id }}">{{ $course->course_code . " " . $course->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Filter By Class</label>

                                <div class="select2-wrapper">
                                    <select name="class_id"
                                        class="form-control select2"
                                        id="class-select"
                                        data-placeholder="Select a class">
                                        @foreach ($classes as $class)
                                            <option value=""></option>
                                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            @if ($timetable->type == 'NORMAL')
                            <label>Filter by Lecturer</label>
                            @else
                            <label>Filter by Invigilator</label>
                            @endif

                            <div class="select2-wrapper">
                                <select name="professor_id"
                                    class="form-control select2"
                                    id="class-select"
                                    data-placeholder="Select a lecturer">
                                    @foreach ($professors as $professor)
                                        <option value=""></option>
                                        <option value="{{ $professor->id }}">{{ $professor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="toolbar-button">
                                <button class="btn btn-success btn-block" id="print-button" data-id="{{ $timetable->id }}"><i class="fa fa-file-pdf-o"></i>  Print Timetable</button>
                            </div>
                        </div>
                    </div>

                    <div id="schedules-table">
                        @if ($timetable->type == 'EXAM')
                            @include('timetable_schedules.exams_table')
                        @else
                            @include('timetable_schedules.lectures_table')
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('timetable_schedules.modals')
@endsection

@section('scripts')
<script src="{{URL::asset('/js/timetable_schedules/index.js')}}"></script>
@endsection