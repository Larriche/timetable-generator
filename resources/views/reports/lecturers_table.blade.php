@if (isset($schedules) && count($schedules))
    @if (!isset($print))
        @include('reports.exports_widget')
    @endif

    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <?php $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']; ?>
            @foreach ($days as $day)
            <?php
                if (!isset($schedules[$day])) {
                    continue;
                }
                $daySchedules = $schedules[$day];
            ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th colspan="4">{{ $day }}</th>
                    </tr>

                    <tr>
                        <th class="col-md-3">Time</th>
                        <th class="col-md-3">Course</th>
                        <th class="col-md-3">Class</th>
                        <th class="col-md-3">Venue</th>
                    </tr>

                    <?php
                        $times = array_keys($daySchedules);
                        natcasesort($times);
                    ?>

                    @foreach ($times as $time)
                        @foreach ($daySchedules[$time] as $schedule)
                            <tr>
                                <td>{{ $time }}</td>
                                <td>{{ $schedule->course->course_code . ' ' . $schedule->course->name }}</td>
                                <td>{{ $schedule->class->name }}</td>
                                <td>{{ $schedule->rooms()->first()->name }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </table>
            </div>
            @endforeach
        </div>
    </div>
@else
    <div class="no-data text-center">
        <p>No matching data was found</p>
    </div>
@endif