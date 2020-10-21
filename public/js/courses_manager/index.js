/**
 * An object for managing tasks related to course/class associations
 */
function Course(url, resourceName) {
    Resource.call(this, url, resourceName);
}

App.extend(Resource, Course);

Course.prototype.refreshPage = function (keyword) {
    var self = this;

    if (!keyword) {
        location.reload();
    } else {
        Resource.prototype.refreshPage.call(self, keyword);
    }
};

Course.prototype.prepareForUpdate = function (resource) {
    $('[name=academic_period_id]').val(resource.academic_period_id).change();
    $('[name=credits]').val(resource.credits);
    $('[name=size]').val(resource.size);
    $('#professors-select').val(resource.professor_ids).change();

    $('#courses-select-container').html("").html($('#all-courses').html());
    $('#courses-select-container').find('select').select2();
    $('#courses-select-container').find('select').val(resource.course_id).change();
};

Course.prototype.initializeAddModal = function () {
    var self = this;

    $('#courses-select-container').html("").html($('#available-courses').html());
    $('#courses-select-container').find('select').select2();

    Resource.prototype.initializeAddModal.call(self);
}


window.addEventListener('load', function () {
    var $el = $('#class-id');
    var classId = $el ? $el.val() : null;
    var url = '/classes/' + classId + '/courses';
    var course = new Course(url , 'Course');

    course.init();
});
