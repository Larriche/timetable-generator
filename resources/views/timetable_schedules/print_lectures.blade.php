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
            }
            .logo {
                margin-left: 40%;
            }
            .timetable-course {
                font-weight: bold;
                font-size: 1.5em;
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
                @foreach ($schedules as $class => $schedule)
                    <h4 class="text-center">{{ $class }}</h4>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Days</th>

                                @foreach ($timeslots as $timeslot)
                                    <th>{{ $timeslot->label }}</th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                                $days = [
                                    'Monday',
                                    'Tuesday',
                                    'Wednesday',
                                    'Thursday',
                                    'Friday',
                                    'Saturday',
                                    'Sunday'
                                ]
                            ?>
                            @foreach ($days as $day)
                                <?php
                                    $include_day = !in_array($day, explode(",", $timetable->days_exempted));

                                    if ($searched_day) {
                                        $include_day = ($searched_day == $day);
                                    }
                                ?>
                                @if ($include_day)
                                <tr>
                                    <td>
                                        <div class="timetable-day">
                                            <p>{{ strtoupper($day) }}</p>
                                        </div>
                                    </td>

                                    @foreach ($timeslots as $timeslot)
                                        <td>
                                            @if (isset($schedules[$class][$day][$timeslot->label]))
                                                @foreach ($schedules[$class][$day][$timeslot->label] as $schedule)
                                                <div class="timetable-schedule">
                                                    <?php
                                                        $professor_names = [];
                                                    ?>

                                                    @foreach ($schedule['lecturers'] as $professor)
                                                        <?php $professor_names[] = $professor->name ;?>
                                                    @endforeach

                                                    <p class="timetable-course text-center">{{ $schedule['course']->course_code }}</p>

                                                    <p class="text-center">{{ $schedule['course']->name }}</p>

                                                    @if (count($professor_names))
                                                    <p class="pull-left">
                                                    {{ implode("/", $professor_names) }}
                                                    </p>
                                                    @endif

                                                    <p class="pull-right">
                                                        {{ $schedule['room'] }}
                                                    </p>
                                                </div>
                                                @endforeach
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>

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
