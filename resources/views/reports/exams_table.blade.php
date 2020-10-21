@if (isset($schedules) && count($schedules))
@if (!isset($print))
    @include('reports.exports_widget')
@endif

<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Exam</th>
                        <th>Class</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($schedules as $schedule)
                        <tr>
                            <td>{{ date('l, jS M, Y', strtotime($schedule->day)) }}</td>
                            <td>{{ $schedule->course->course_code }} {{ $schedule->course->name }}</td>
                            <td>{{ $schedule->class->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
