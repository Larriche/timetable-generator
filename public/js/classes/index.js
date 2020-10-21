/**
 * An object for managing tasks related to courses
 */
function CollegeClass(url, resourceName) {
    Resource.call(this, url, resourceName);
}

App.extend(Resource, CollegeClass);

CollegeClass.prototype.init = function() {
    var self = this;

    Resource.prototype.init.call(self);
};

CollegeClass.prototype.prepareForUpdate = function (resource) {
    var self = this;

    $('input[name=name]').val(resource.name);
    $('#block-select').val(resource.block_id).change();
};

window.addEventListener('load', function () {
    var collegeClass = new CollegeClass('/classes', 'class');
    collegeClass.init();
});