<!DOCTYPE html>
    <head>
        <title>Lecturer Schedule Report</title>
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
                margin-top: 200px;
            }
            .margin-bottom {
                margin-bottom: 50px;
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
                    <h3 class="text-center">Lectures Schedule for {{ $lecturer->name }}</h3>
                </div>
            </div>

            @include('reports.lecturers_table')
        </div>
    </body>
</html>
