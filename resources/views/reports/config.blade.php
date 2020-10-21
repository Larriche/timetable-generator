<form id="reports-form">
    {{ csrf_field() }}
    <div class="row">
        <div class="col-md-4 col-md-offset-1">
            <label>From</label>

            <div class="form-group">
                <div class="input-group date">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control datepicker" name="start_date">
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <label>To</label>

            <div class="form-group">
                <div class="input-group date">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control datepicker" name="end_date">
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <button class="btn btn-block btn-success" style="margin-top: 24px" id="generate-button">Run Report</button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div id="errors-container">
                @include('partials.modal_errors')
            </div>
        </div>
    </div>
</form>