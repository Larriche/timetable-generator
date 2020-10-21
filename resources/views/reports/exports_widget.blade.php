<div class="row margin-top margin-bottom">
    <div class="col-md-3 col-md-offset-5">
        <div class="text-center">
            @if (in_array('pdf', $formats))
            <button type="button" class="btn btn-lg pull-left btn-danger report-export" data-format="pdf"><i class="fa fa-file-pdf-o"></i> Export as PDF</button>
            @endif

            @if (in_array('excel', $formats))
            <button type="button" class="btn btn-lg pull-right btn-success report-export" data-format="excel"><i class="fa fa-file-excel-o"></i> Export to Excel</button>
            @endif
        </div>
    </div>
</div>