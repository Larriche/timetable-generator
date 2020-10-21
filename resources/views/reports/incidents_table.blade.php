@if (isset($incidents))
@if (!isset($print))
    @include('reports.exports_widget')
@endif

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        @foreach ($incidents as $incident)
            <div class="incidents-panel margin-top margin-bottom">
                <div class="incidents-panel-header">
                    <h4>
                        <span class="bold">Recorded on: </span> {{ date('l, jS M, Y \a\t h:m:s a', strtotime($incident->schedule->day)) }}
                    </h4>

                    <h4>
                        <span class="bold">Exam: </span> {{ $incident->schedule->course->course_code . ' '. $incident->schedule->course->name }}
                    </h4>

                    <h4>
                        <span class="bold">Invigilators: </span> {{ implode("/", $incident->schedule->invigilators()->pluck('name')->toArray()) }}
                    </h4>
                </div>

                <div class="incidents-panel-body">
                    <h4 class="bold">Summary</h4>

                    <p>{{ $incident->summary or 'No summary available' }}</p>

                    <h4 class="bold margin-top">
                        Details
                    </h4>

                    <p>{{ $incident->description or 'No details given' }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
