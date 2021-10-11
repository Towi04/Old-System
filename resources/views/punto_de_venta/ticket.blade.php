<style>
    @media print{
       .no_print{
           display: none;
       }
   }

</style>

<p style="text-align: center">
   <a class="no_print" onclick="window.print()"><i class="fas fa-arrow-left " style="margin-bottom:10px;cursor: pointer;"></i> Imprimir</a><br>

   <img src="{{ asset('img/logo.png') }}" width="120px"><br><br>
   Centro Nacional de Computación de México S.C.<br>
    Domicilio fiscal: Andador Gongora No. 104 Colonia Centro. CP 38000<br>
    RFC CNC050207D21 - TEL (461) 613-01-01<BR> Celaya, Guanajuato. <br><br>
        
    <b>Sucursal:</b> {{ $pago->sucursal->nombre }} <br>
    Domicilio sucursal: {{$pago->sucursal->direccion}}, {{$pago->sucursal->municipio}}, {{$pago->sucursal->estado}}
</p>
<div class="content" >

   <p style="line-height : .5cm; text-align:center">
       Fecha {{ $pago->fecha->format('d-m-Y') }}<br>
       Hora {{ $pago->fecha->format('H:i:s') }} 
       <hr>

       Folio: {{ $pago->folio }}<br>
       Recibio: {{ $pago->recibio->fullname }}<br><br>
       Alumno: {{ $pago->alumno->fullname }}<br>
       No Control: {{ $pago->alumno->numero_control }}<br>

   </p>
   <hr>
    <p style="line-height : .5cm;">
        <b>Monto Total del pago:</b> {{ number_format($pago->monto,2,'.',',') }}
   <br><br>
    <u>Désgloce del pago</u>
    <br>
        <table  style="width:100%;">
            @if($pago->abonos->count() > 0)
                @foreach ($pago->abonos as $abono)
                    <tr>
                        <td>{{ $abono->alumno_pago->concepto }}</td>
                        <td style="text-align: right">$ {{ number_format($abono->monto,2,'.',',') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="2" style="padding-top:35px;border-top:2px solid black; text-align:right"><b>Total: $ {{ number_format($pago->abonos->sum('monto'),2,'.',',') }}</td>
                </tr>
            @else 
                <tr>
                    <td>A CUENTA</td>
                    <td style="text-align: right">$ {{ number_format($pago->monto,2,'.',',') }}</td>
                </tr>
            @endif
           
        </table>

<br>
       Efectios fiscales al pago.<br>
       Pago hecho en una sola exhibición.<br><br>

       Para cualquier duda o sugerencia enviarnos un correo a corporativo@cncm.com.mx
</div>
<script>
   setTimeout(function () { window.print(); }, 500);
</script>
