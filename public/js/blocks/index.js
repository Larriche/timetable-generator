/**
 * An object for managing tasks related to courses
 */
function Block(url, resourceName) {
    Resource.call(this, url, resourceName);
}

App.extend(Resource, Block);

Block.prototype.prepareForUpdate = function (resource) {
    $('input[name=name]').val(resource.name);
};

window.addEventListener('load', function () {
    var course = new Block('/blocks', 'Building');
    course.init();
});