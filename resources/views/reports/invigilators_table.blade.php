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
                            <th>Venues</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($schedules as $schedule)
                            <tr>
                                <td>{{ date('l, jS M, Y', strtotime($schedule->day)) }}</td>
                                <td>{{ $schedule->course->course_code }} {{ $schedule->course->name }}</td>
                                <td>{{ implode("/", $schedule->rooms()->pluck('name')->toArray()) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
@else
    <div class="no-data text-center">
        <p>No matching data was found</p>
    </div>
@endif