var Reports = {
    init: function() {
        $(document).on('click', '#generate-button', function(event) {
            event.preventDefault();
            Reports.getReport();
        });

        $(document).on('click', '.report-export', function(event) {
            var type = $('[name=report_type]').val();
            var url = '/reports/' + type + '/export';
            var format = $(this).data('format');

            var queryString = new URLSearchParams(new FormData($('#reports-form').get(0))).toString();
            url += '?' + queryString + '&format=' + format;

            window.open(url, '_blank');
        });

        var $form = $('#reports-form');

        $form.find('[name=start_date]').datepicker("update", new Date());
        $form.find('[name=end_date]').datepicker("update", new Date());
    },

    getReport() {
        var formData = new FormData($('#reports-form').get(0));

        var type = $('[name=report_type]').val();

        var ajaxData = {
            url: '/reports/' + type,
            type: 'POST',
            data: formData,
            processData: false,
            cache: false,
            contentType: false,
            success: function (report) {
                $('#resource-container').html(report);
            },
            error: function (response, text_status, xhr) {
                var $errorContainer = $('#reports-errors');

                if ($errorContainer && response.status == 422) {
                    // We make it possible to extract errors whether they were returned
                    // by Laravel $this->validator or by Validator::make()
                    // The former has errors array directly in JSON response body
                    // while Validator::make() has it in an errors field
                    var responseContent = response.responseJSON;
                    var errors = responseContent.errors ? responseContent.errors : responseContent;

                    var errorHtml = App.buildErrorHtml(errors);

                    $errorContainer.find('ul').html(errorHtml);
                    $('.modal-error-div').removeClass('hidden')
                        .delay(15000).queue(function () {
                            $(this).addClass('hidden').dequeue();
                        });
                    $('#errors-container').show();
                }

                var text = (response.status == 422) ?
                    'The form submission failed. Check form for details.' :
                    'Oops! A system error occurred';

                new PNotify({
                    title: 'Error',
                    text: text,
                    styling: 'bootstrap3',
                    type: 'error',
                    delay: 9500
                });
            }
        }

        NProgress.start();

        $.ajax(ajaxData).always(function () {
            NProgress.done();
        });
    }
}

window.addEventListener('load', Reports.init);