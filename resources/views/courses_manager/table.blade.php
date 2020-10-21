<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        @if (count($classCourses))
        <table class="table table-bordered">
            <thead>
                <tr class="table-head">
                    <th style="width: 15%">Course Code</th>
                    <th style="width: 30%">Name</th>
                    <th style="width: 20%">Examiners</th>
                    <th style="width: 15%">Number of students</th>
                    <th style="width: 10%">Lectures per week</th>
                    <th style="width: 10%">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($classCourses as $course)
                <tr>
                    <td>{{ $course->course->course_code }}</td>
                    <td>{{ $course->course->name }}</td>
                    <td>
                        @foreach ($course->professors as $professor)
                            {{ $professor->name }} <br>
                        @endforeach
                    </td>
                    <td>{{ $course->size }}</td>
                    <td>{{ $course->credits }}</td>
                    <td>
                    <button class="btn btn-info btn-sm resource-update-btn" data-id="{{ $course->id }}"><i class="fa fa-pencil"></i></button>
                    <button class="btn btn-danger btn-sm resource-delete-btn" data-id="{{ $course->id }}"><i class="fa fa-trash-o"></i></button></td>
                </tr>
                @endforeach
            </tbody>
        </table>

         <div id="pagination">
            {!!
                $classCourses->render()
            !!}
        </div>
        @else
        <div class="no-data text-center">
            <p>No matching data was found</p>
        </div>
        @endif
    </div>
</div>