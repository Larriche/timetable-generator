var Schedules = {
    filters: {
        date: '',
        course_id: '',
        class_id: '',
        lecturer_id: ''
    },

    init: function() {
        Schedules.registerEventListeners();
    },

    registerEventListeners() {
        $(document).on('change', '[name=date]', function () {
            Schedules.filters.date = $(this).val();
            Schedules.filter();
        });

        $(document).on('change', '[name=course_id]', function() {
            Schedules.filters.course_id = $(this).val();
            Schedules.filter();
        });

        $(document).on('change', '[name=class_id]', function() {
            Schedules.filters.class_id = $(this).val();
            Schedules.filter();
        });

        $(document).on('change', '[name=professor_id]', function() {
            Schedules.filters.professor_id = $(this).val();
            Schedules.filter();
        });

        $(document).on('click', '#print-button', function() {
            Schedules.print();
        });

        $(document).on('click', '.incidents-add', function(event) {
            var scheduleId = $(this).data('id');

            $('#incidents-add-modal').find('input[name=schedule_id]').val(scheduleId);
            $('#incidents-add-modal').modal('show');
        });

        $(document).on('click', '.attendance-add', function(event) {
            var scheduleId = $(this).data('id');
            Schedules.setUpAttendanceForm(scheduleId);
        });

        $(document).on('submit', '#incidents-add-form', function(event) {
            event.preventDefault();
            App.submitForm(this, null, $('#incidents-errors-container'));
        });

        $(document).on('submit', '#attendance-form', function(event) {
            event.preventDefault();
            App.submitForm(this, null, $('#attendance-errors-container'));
        });
    },

    filter() {
        var $container = $('#schedules-table');
        var url = window.location.href;
        var params = {};

        for (var key of Object.keys(Schedules.filters)) {
            if (Schedules.filters[key]) {
                params[key] = Schedules.filters[key];
            }
        }

        $container.html("");

        $.ajax({
            type: 'get',
            data: params,
            url: url,
            success: function (response) {
                $container.html(response);
            }
        });
    },

    print() {
        var timetable_id = $('#print-button').data('id');
        var url = '/timetables/print/' + timetable_id;
        var query = false;

        for (var key of Object.keys(Schedules.filters)) {
            if (Schedules.filters[key]) {
                if (!query) {
                    url += '?';
                    query = true;
                }

                url += key + '=' + Schedules.filters[key];
            }
        }

        window.open(url, '_blank');
    },

    /**
     * Set up attendance form
     *
     * @param {Integer} scheduleId
     */
    setUpAttendanceForm(scheduleId) {
        var url = '/timetable_schedules/' + scheduleId;

        $.ajax({
            type: 'get',
            url: url,
            success: function (response) {
                var $form = $('#attendance-form');

                $form.attr('action', '/timetable_schedules/attendance/' + scheduleId);
                $form.find('input[name=schedule_id]').val(scheduleId);
                $form.find('input[name=attendance]').val(response.attendance);
                $('#attendance-modal').modal('show');
            }
        });
    }
}

window.addEventListener('load', Schedules.init);