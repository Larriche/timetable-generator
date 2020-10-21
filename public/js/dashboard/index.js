/**
 * An object for managing tasks related to courses
 */
function Timetable(url, resourceName) {
    Resource.call(this, url, resourceName);
}

App.extend(Resource, Timetable);

Timetable.prototype.init = function () {
    var self = this;
    Resource.prototype.init.call(self);

    $(document).on('click', '.print-btn', function (event) {
        var url = '/timetables/view/' + $(this).data('id');
        var printWin = window.open('', '', 'width=5,height=5');

        event.preventDefault();
        self.printTimetable(printWin, url);
    });

    $(document).on('change', '[name=type]', function (event) {
        var type = $(this).val();

        if (type == 'NORMAL') {
            $('.exams-field').hide();
        } else {
            $('.exams-field').show();

            var $form = $('#resource-form');

            $form.find('[name=start_date]').datepicker("update", new Date());
            $form.find('[name=end_date]').datepicker("update", new Date());
        }
    });
};

Timetable.prototype.printTimetable = function (printWin, url) {
    $.get(url, null, function (response) {
        printWin.resizeTo(window.innerWidth, window.innerHeight);
        printWin.document.open();
        printWin.document.write(response);
        printWin.document.close();

        // Wait for the page to load, and after that we print and close the window
        printWin.onload = function () {
            printWin.focus();
            printWin.print();
            printWin.close();
        };
    });
};

Timetable.prototype.initializeAddModal = function () {
    var $modal = $('#resource-modal');
    Resource.prototype.initializeAddModal.call(this);

    $('[name=type]').val('NORMAL').change();

    // Set up modal title and button label
    $modal.find('.modal-heading').html('Create New Timetable Set');
    $modal.find('.submit-btn').html('Generate');
};

/**
 * Refresh page for a resource after
 */
Timetable.prototype.refreshPage = function (keyword) {
    var $container = $('#resource-container');
    var url = '/dashboard';

    keyword = keyword || null;

    if (keyword) {
        url += "?keyword=" + keyword;
    }

    $container.html("");

    $.ajax({
        type: 'get',
        url: url,
        success: function (response) {
            $container.html(response);
        }
    });
};

window.addEventListener('load', function () {
    var timetable = new Timetable('/timetables', 'Timetable');
    timetable.init();
});