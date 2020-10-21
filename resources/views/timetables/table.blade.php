<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        @if (count($timetables))
        <table class="table table-bordered">
            <thead>
                <tr class="table-head">
                    <th style="width: 30%">Name</th>
                    <th style="width: 30%">Status</th>
                    <th>Started Generating On</th>
                    <th>Completed On</th>
                    <th style="width: 7%">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($timetables as $timetable)
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
                        <button class="btn btn-sm btn-danger delete-button" data-id="{{ $timetable->id }}"><i class="fa fa-trash-o"></i></button>
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
            <p>No matching data was found</p>
        </div>
        @endif
    </div>
</div>