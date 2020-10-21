<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
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
                                <td></td>
                            </tr>
                            <tr>
                                <th style="width: 25%">COURSE NO./TITLE</th>
                                <th style="width: 20%">EXAMINERS</th>
                                <th style="width: 9%">CLASS</th>
                                <th style="width: 9%">NO. OF CAND</th>
                                <th style="width: 10%">VENUE</th>
                                <th style="width: 15%">INVIGILATORS</th>
                                <th style="width: 8%">ACTIONS</th>
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
                                        <td>
                                            <button class="btn btn-success btn-sm attendance-add" data-id="{{ $scheduled_data->id }}"><i class="fa fa-users"></i></button>
                                            <button class="btn btn-info btn-sm incidents-add" data-id="{{ $scheduled_data->id }}"><i class="fa fa-book"></i></button></td>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>

                    <div class="page-break"></div>
                @endforeach
            @endforeach
        @else
        <div class="no-data text-center">
            <p>No matching data was found</p>
        </div>
        @endif
    </div>
</div>