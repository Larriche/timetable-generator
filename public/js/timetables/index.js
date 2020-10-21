var Timetables = {
    init: function() {
        Timetables.registerEventListeners();
    },

    registerEventListeners: function() {
        $(document).on('click', '.delete-button', function(event) {
            var timetableId = $(this).data('id');
            Timetables.initializeDeleteModal(timetableId);
        });

        // Set up event listener for resource delete form submissions
        $(document).on('submit', '#resource-delete-form', function (event) {
            event.preventDefault();
            Timetables.submitDeleteForm();
        });

        // Set up event listener for search button click
        $(document).on('click', '#search-button', function (event) {
            event.preventDefault();
            Timetables.search($('[name=search_term]').val());
        });
    },

    initializeDeleteModal: function(timetableId) {
        App.setDeleteForm( '/timetables/' + timetableId, 'Delete Timetable');
        App.showConfirmDialog("Do you want to delete this timetable?");
    },

    submitDeleteForm: function() {
        App.submitForm($('#resource-delete-form').get(0), Timetables.refreshPage, null);
    },

    refreshPage: function(keyword) {
        var $container = $('#resource-container');
        var url = '/timetables';

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
    },

    search: function (keyword) {
        Timetables.refreshPage(keyword);
    }
};

window.addEventListener('load', Timetables.init);