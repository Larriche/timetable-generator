
<form class="form" method="POST" action="" id="resource-form">
    {{ csrf_field() }}

    <input type="hidden" id="class-id" name="class_id" value="{{ $class->id }}">

    <div class="modal-body">
        <div id="errors-container">
            @include('partials.modal_errors')
        </div>

        <input type="hidden" name="_method" value="">
        <input type="hidden" name="class_id" value="{{ $class->id }}">

        <div class="form-group">
            <label>Select a course</label>

            <div class="select2-wrapper" id="courses-select-container">
            </div>
        </div>

        <div class="form-group">
            <label>Lecturer(s)</label>

            <div class="select2-wrapper">
                <select name="professor_ids[]"
                    class="form-control select2"
                    id="professors-select"
                    data-placeholder="Select lecturer(s)"
                    multiple>
                    @foreach ($professors as $professor)
                        <option value="{{ $professor->id }}">{{ $professor->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Semester</label>

            <div class="select2-wrapper">
                <select name="academic_period_id"
                    class="form-control select2"
                    data-placeholder="Select a period">
                    @foreach ($academicPeriods as $period)
                    <option value="{{ $period->id }}">{{ $period->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Lectures per week</label>
            <input type="text" class="form-control" name="credits" placeholder="Meeting times">
        </div>

        <div class="form-group">
            <label>Number of students</label>
            <input type="text" class="form-control" name="size" placeholder="Number of students sitting for course">
        </div>
    </div>

    <div class="modal-footer">
        <div class="form-group">
            <div class="row">
                <div class="col-lg-5 col-md-5 col-sm-5 col-offset-1 col-md-offset-1">
                    <button type="button" class="btn btn-danger btn-block" data-dismiss="modal">Cancel</button>
                </div>

                <div class="col-lg-5 col-md-5 col-sm-5">
                    <button type="submit" class="submit-btn btn btn-success btn-block">Add Resource</button>
                </div>
            </div>
        </div>
    </div>
</form>