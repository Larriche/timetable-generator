<!DOCTYPE html>
    <head>
        <title>Exams Incidence Report</title>
        <link href="{!! URL::asset('/vendors/bootstrap/dist/css/bootstrap.min.css') !!}" rel="stylesheet">
        <style>
            .header,
            .footer {
                width: 100%;
                text-align: center;
                position: fixed;
            }
            .header {
                top: 0px;
            }
            .footer {
                bottom: 0px;
            }
            .pagenum:before {
                content: counter(page);
            }
            .margin-top {
                margin-top: 20px;
            }
            .margin-bottom {
                margin-bottom: 20px;
            }

            .centered {
                text-align: center;
            }
            .page-break {
                page-break-after: always;
                page-break-inside: avoid;
            }
            .logo {
                width: 70px;
                margin-bottom: 10px;
            }
            .bold {
                font-weight: bold !important;
            }

            .incidents-panel {
                border: 1px solid grey;
            }

            .incidents-panel-header {
                padding: 20px;
                border-bottom: 1px solid grey;
            }

            .incidents-panel-body {
                padding: 20px;
            }

            .incidents-panel-body p {
                text-align: justify;
            }
        </style>
    </head>

    <body>
        <div id="content">
            <div class="row">
                <div class="col-md-12">
                     <div class="pull-right logo">
                         <img class="img img-responsive" src="{!! URL::asset('/images/logo.jpeg') !!}">
                     </div>

                    <h2 class="text-center">COLLEGE OF SCIENCE, KNUST</h2>
                    <h3 class="text-center">Exams Incidence Report</h3>
                    <h4 class="text-center"> From {{ date('l, jS M, Y', strtotime($from)) }} to {{ date('l, jS M, Y', strtotime($to)) }}</h4>
                </div>
            </div>

            @include('reports.incidents_table')
        </div>
    </body>
</html>
