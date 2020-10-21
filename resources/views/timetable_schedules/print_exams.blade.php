<!DOCTYPE html>
    <head>
        <title>{{ $timetable->name }}</title>
        <link href="{!! URL::asset('/vendors/bootstrap/dist/css/bootstrap.min.css') !!}" rel="stylesheet">
        <style>
            .header,
            .footer {
                width: 100%;
                text-align: center;
                position: fixed;
            }
            .header {
                top: 0px;
            }
            .footer {
                bottom: 0px;
            }
            .pagenum:before {
                content: counter(page);
            }
            .margin-top {
                margin-top: 200px;
            }
            .margin-bottom {
                margin-bottom: 50px;
            }

            .centered {
                text-align: center;
            }
            .page-break {
                page-break-after: always;
                page-break-inside: avoid;
            }
            .logo {
                margin-left: 40%;
            }
            .timetable-course {
                font-weight: bold;
                font-size: 1.1em;
                margin-bottom: 30px;
            }

            .timetable-schedule {
                padding: 10px;
            }

            .timetable-day {
                padding-top: 30%;
            }
            h2{
                font-size: 4em;
            }
            h3 {
                font-size: 3em;
            }
            h4 {
                font-size: 2em;
            }
        </style>
    </head>

    <body>
        <div id="content">
            <div class="row margin-bottom">
                <div class="col-md-12">
                     <div class="text-center logo margin-bottom">
                         <img class="img img-responsive" src="{!! URL::asset('/images/logo.jpeg') !!}">
                     </div>

                    <h2 class="text-center">COLLEGE OF SCIENCE, KNUST</h2>
                    <h3 class="text-center margin-top">{{ strtoupper($timetable->name) }}</h3>
                    <div class="page-break"></div>
                </div>
            </div>

            @if (count(array_values($schedules)))
                @foreach ($schedules as $date => $period_data)
                    @foreach ($period_data as $period => $date_schedule)
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td colspan="6" class="timetable-heading">
                                        <b class="pull-left">{{ strtoupper(date('l, jS M, Y', strtotime($date))) }}</b>
                                        <b class="pull-right">{{ $period }}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width: 30%">COURSE NO./TITLE</th>
                                    <th style="width: 20%">EXAMINERS</th>
                                    <th style="width: 10%">CLASS</th>
                                    <th style="width: 10%">NO. OF CAND</th>
                                    <th style="width: 10%">VENUE</th>
                                    <th style="width: 10%">INVIGILATORS</th>
                                </tr>
                                @foreach ($date_schedule as $course_id => $schedule)
                                    <tr>
                                        <td rowspan="{{ count($schedule['schedules']) + 1 }}">{{ $schedule['course'] }}</td>
                                    </tr>

                                    @foreach ($schedule['schedules'] as $scheduled_data)
                                        <tr>
                                            <td>
                                                <?php $professor_names = []; ?>
                                                @if ($scheduled_data->class_course)
                                                    @foreach ($scheduled_data->class_course->professors as $professor)
                                                        <?php $professor_names[] = $professor->name ;?>
                                                    @endforeach
                                                @endif

                                                @if (count($professor_names))
                                                {{ implode("/", $professor_names) }}
                                                @else
                                                N/A
                                                @endif
                                            </td>
                                            <td>{{ $scheduled_data->class->name }}</td>
                                            <td>{{ $scheduled_data->class_course->size }}</td>
                                            <td>
                                                {{ implode("/", $scheduled_data->rooms()->pluck('name')->toArray()) }}
                                            </td>
                                            <td>
                                                {{ implode("/", $scheduled_data->invigilators()->pluck('name')->toArray()) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    @endforeach
                    <div class="page-break"></div>
                @endforeach
            @else
            <div class="no-data text-center">
                <p>No matching data was found</p>
            </div>
            @endif
        </div>
    </body>
</html>
