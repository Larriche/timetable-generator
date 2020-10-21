<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        @if (count($classes))
        <table class="table table-bordered">
            <thead>
                <tr class="table-head">
                    <th>Name</th>
                    <th>Lecture Block</th>
                    <th>Number of Courses</th>
                    <th style="width: 10%">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($classes as $class)
                <tr>
                    <td>{{ $class->name }}</td>
                    <td>{{ $class->block->name }}</td>
                    <td>{{ $class->courses()->count() }}</td>
                    <td>
                    <a class="btn btn-success btn-sm" href="{{ url('/classes/' . $class->id . '/courses' ) }}"><i class="fa fa-book"></i></a>
                    <button class="btn btn-info btn-sm resource-update-btn action-btn" data-id="{{ $class->id }}"><i class="fa fa-pencil"></i></button>
                    <button class="btn btn-danger btn-sm resource-delete-btn action-btn" data-id="{{ $class->id }}"><i class="fa fa-trash-o"></i></button></td>
                </tr>
                @endforeach
            </tbody>
        </table>
         <div id="pagination">
            {!!
                $classes->render()
            !!}
        </div>
        @else
        <div class="no-data text-center">
            <p>No matching data was found</p>
        </div>
        @endif
    </div>
</div>