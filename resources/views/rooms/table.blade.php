<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        @if (count($rooms))
        <table class="table table-bordered table-striped">
            <thead>
                <tr class="table-head">
                    <th style="width: 30%">Name</th>
                    <th>Building</th>
                    <th style="width: 30%">Capacity</th>
                    <th>Closest Rooms</th>
                    <th style="width: 7%">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($rooms as $room)
                <tr>
                    <td>{{ $room->name }}</td>
                    <td>{{ $room->block->name }}</td>
                    <td>{{ $room->capacity }}</td>
                    <td>
                        @if (count($room->adjacent_rooms))
                            @foreach ($room->adjacent_rooms as $adjacent_room)
                                {{ $adjacent_room->name }} <br />
                            @endforeach
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                    <button class="btn btn-info btn-sm resource-update-btn" data-id="{{ $room->id }}"><i class="fa fa-pencil"></i></button>
                    <button class="btn btn-danger btn-sm resource-delete-btn" data-id="{{ $room->id }}"><i class="fa fa-trash-o"></i></button></td>
                </tr>
                @endforeach
            </tbody>
        </table>
         <div id="pagination">
            {!!
                $rooms->render()
            !!}
        </div>
        @else
        <div class="no-data text-center">
            <p>No matching data was found</p>
        </div>
        @endif
    </div>
</div>