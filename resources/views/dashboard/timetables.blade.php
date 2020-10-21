<div class="row margin-top">
    <div class="col-lg-12 col-md-12 col-sm-12 colxs-12">
        <h3 class="text-center margin-top margin-bottom">RECENTLY CREATED TIMETABLES</h3>

        @if (count($timetables))
        <table class="table table-striped">
            <thead>
                <tr class="table-head">
                    <td>Timetable Name</td>
                    <td>Status</td>
                    <td>Started Generating On</td>
                    <td>Completed On</td>
                    <td style="width: 10%">View</td>
                </tr>
            </thead>

            <tbody>
                @foreach ($timetables as $timetable)
                <tr>
                    <td>{{ $timetable->name }}</td>
                    <td>{{ $timetable->status }}</td>
                    <td>{{ $timetable->created_at->format('l, jS M, Y \a\t g:i a') }}</td>
                    <td>{{ $timetable->updated_at->format('l, jS M, Y \a\t g:i a') }}</td>
                    <td>
                        <a class="btn btn-success btn-sm" data-id="{{ $timetable->id }}"
                            href="{{ url('/timetables/' . $timetable->id ) }}"
                            @if ($timetable->status != 'COMPLETED')
                            disabled
                            @endif>
                            <i class="fa fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
         <div id="pagination">
            {!!
                $timetables->render()
            !!}
        </div>
        @else
        <div class="no-data text-center">
            <p>No timetables generated yet</p>
        </div>
        @endif
    </div>
</div>