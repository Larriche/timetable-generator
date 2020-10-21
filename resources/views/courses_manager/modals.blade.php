<!-- Modal for adding and updating class courses -->
<div class="modal custom-modal" id="resource-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">x</span>
                </button>

                <h4 class="modal-heading">Add New Course</h4>
            </div>

            <div id="courses-manager-container">
                @include('courses_manager.form')
            </div>
        </div>
    </div>
</div>

<!--snippets -->
<div class="hidden" id="available-courses">
    <select name="course_id" class="form-control select-2" data-placeholder="Select a course">
        @foreach ($courses as $course)
            @if (!in_array($course->id, $courseIds))
                <option value="{{ $course->id }}">{{ $course->course_code . " " . $course->name }}</option>
            @endif
        @endforeach
    </select>
</div>

<div class="hidden" id="all-courses">
    <select name="course_id" class="form-control select-2" data-placeholder="Select a course">
        @foreach ($courses as $course)
        <option value="{{ $course->id }}">{{ $course->course_code . " " . $course->name }}</option>
        @endforeach
    </select>
</div>