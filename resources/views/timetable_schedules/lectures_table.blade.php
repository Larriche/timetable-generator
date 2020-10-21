<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        @if (count(array_values($schedules)))
            @foreach ($schedules as $class => $schedule)
                <h3 class="text-center">{{ $class }}</h3>

                <table class="table table-bordered margin-top margin-bottom">
                    <thead>
                        <tr>
                            <th>DAYS</th>

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
                                    <td class="col-md-3">
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
</div>
