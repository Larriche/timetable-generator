@if (isset($schedules) && count($schedules))
@if (!isset($print))
    @include('reports.exports_widget')
@endif

<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Exam</th>
                        <th>Expected Attendance</th>
                        <th>Actual Attendance</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($schedules as $schedule)
                        <tr>
                            <td>{{ date('l, jS M, Y', strtotime($schedule->day)) }}</td>
                            <td>{{ $schedule->course->course_code }} {{ $schedule->course->name }}</td>
                            <td>{{ $schedule->class_course->size }}</td>
                            <td>{{ $schedule->attendance }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
