<!DOCTYPE html>
<html>
  <head>
    <title>Lista de asistencia</title>
    <meta charset="utf-8">
    <meta content="ie=edge" http-equiv="x-ua-compatible">
    <meta content="Aldo Aranza" name="author">
    <meta content="Reporte Alerta de pagos" name="description">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{asset('img/logo.png')}}" rel="shortcut icon">
    <link href="apple-touch-icon.png" rel="apple-touch-icon">
    <link href="{{public_path('template-kineticpro-horizontal/assets/css/style.css')}}" rel="stylesheet" type="text/css">
    <script src="https://kit.fontawesome.com/1f556c46ab.js" crossorigin="anonymous"></script>
    <style>
        @page {
            margin: 0cm 0cm;
            size: letter landscape;
        }

        body {
            margin: 2.75cm 1cm 4cm 1cm;
            line-height: .5cm;
            font-family: "Gill Sans, sans-serif" !important;
            font-size: 10pt;
            background-color: white
        }

        .page-break {
            page-break-after: always;
        }

        .header {
            position: fixed;
            top: 0.2cm;
            left: 0cm;
            right: 0cm;
            height: 2.2cm;
            text-align: center;
            line-height: 30px;
        }

        .footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 1.5cm;
            color: white;
            text-align: center;
            line-height: 30px;
            padding: 0px !important;
        }

       .table thead > tr > th{
           margin: 0;
           padding: 0.10rem;
           font-size: 0.5rem;
        }

       .table tbody > tr > td{
            margin: 0;
            padding: 0.10rem;
            color: black;
            font-size: 0.5rem;
        }

        .table-bordered {
            border: 1px solid #636e72;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #636e72;
        }

        .titulo-principal{
            font-size: 1.2rem;
        }
    </style>
    </head>
    <body>
        <div class="header">
            <div class="row pt-2 d-flex">
                <div class="col-12 text-center">
                    <span class="text-uppercase titulo-principal text-primary">Centro integral tecnologico de estudios de méxico</span> <br>
                    <span class="text-dark">{{ $sucursal->direccion }} , {{ $sucursal->municipio }} {{ $sucursal->estado }} </span> <br>
                    <span class="text-dark">Lista de asistencia</span>
                </div>
            </div>
        </div>
        <div class="footer">
            {{-- <img src="{{ imgToBase64(public_path('img/pie_cotizacion.png')) }}" alt="" width="100%"> --}}
        </div>

        <main style="overflow:hidden">
            <table style="width: 100%; padding-bottom:0.5rem;">
                <tr class="text-dark">
                    <td><span class="font-weight-bold">Horario:</span>  {{ $grupo->horario }}</td>
                    <td class="text-right"> <span class="font-weight-bold">Especialidad:</span> {{ $grupo->especialidad->nombre }}</td>
                </tr>
                <tr class="text-dark">
                    <td> <span class="font-weight-bold">Grupo:</span> </td>
                    <td class="text-right"> <span class="font-weight-bold">Profesor:</span><span>________________________</span> </td>
                </tr>
            </table>

            <table class="table table-bordered table-condensed">
                <thead>
                    <tr>
                        <th colspan="4" class="text-right text-dark">Fase</th>
                        @foreach ($semanas as $semana)
                            <th colspan="{{ count($dias_semana) }}"></th>
                        @endforeach
                    </tr>
                    <tr>
                        <th colspan="4" class="text-right text-dark">Semana</th>
                        @foreach ($semanas as $semana)
                            <th class="text-center text-dark" colspan="{{ count($dias_semana) }}" >{{ $semana }}</th>
                        @endforeach
                    </tr>
                    <tr class="bg-primary">
                        <th>N°</th>
                        <th>Nombre</th>
                        <th class="text-nowrap">N° Ctrl</th>
                        <th>Tel</th>
                        @foreach ($semanas as $semana)
                            @foreach ($dias_semana as $dia)
                                <th>{{ $dia }}</th>
                            @endforeach
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($grupo->alumnos as $alumno)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="text-nowrap">{{ $alumno->fullname }}</td>
                            <td class="text-nowrap">{{ $alumno->numero_control }}</td>
                            <td class="text-nowrap">{{ $alumno->telefono }}</td>
                            @foreach ($semanas as $semana)
                                @foreach ($dias_semana as $item)
                                    <td></td>
                                @endforeach
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div>
                <span class="text-dark">Observaciones:</span> <span style="display: inline-block; border-bottom: 1px solid ;width:90%;"></span>
            </div>
            <div>
                <span style="display: inline-block; border-bottom: 1px solid ;width:99%;"></span>
            </div>
            <div>
                <span style="display: inline-block; border-bottom: 1px solid ;width:99%;"></span>
            </div>
        </main>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="{{asset('template-'.config('sitio.template').'/js/main.js?version=4.3.0')}}"></script>
</body>
</html>
