<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <h4>Exam time periods</h4>
        @if (count($examTimeslots))
        <table class="table table-bordered">
            <thead>
                <tr class="table-head">
                    <th style="width: 40%">Period</th>
                    <th>Time</th>
                    <th style="width: 7%">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($examTimeslots as $timeslot)
                <tr>
                    <td>{{ $timeslot->label }}</td>
                    <td>{{ $timeslot->time }}</td>
                    <td>
                    <button class="btn btn-info btn-sm resource-update-btn" data-id="{{ $timeslot->id }}"><i class="fa fa-pencil"></i></button>
                    <button class="btn btn-danger btn-sm resource-delete-btn" data-id="{{ $timeslot->id }}"><i class="fa fa-trash-o"></i></button></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="no-data text-center">
            <p>No periods added for exam timetables</p>
        </div>
        @endif

        <h4>Lectures time periods</h4>
        @if (count($lectureTimeslots))
        <table class="table table-bordered">
            <thead>
                <tr class="table-head">
                    <th style="width: 40%">Period</th>
                    <th>Time</th>
                    <th style="width: 7%">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($lectureTimeslots as $timeslot)
                <tr>
                    <td>{{ $timeslot->label }}</td>
                    <td>{{ $timeslot->time }}</td>
                    <td>
                    <button class="btn btn-info btn-sm resource-update-btn" data-id="{{ $timeslot->id }}"><i class="fa fa-pencil"></i></button>
                    <button class="btn btn-danger btn-sm resource-delete-btn" data-id="{{ $timeslot->id }}"><i class="fa fa-trash-o"></i></button></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="no-data text-center">
            <p>No periods added for lecture timetables</p>
        </div>
        @endif
    </div>
</div>