<style>
    @media print{
       .no_print{
           display: none;
       }
   }

</style>

<p style="text-align: center">
   <a class="no_print" onclick="window.print()"><i class="fas fa-arrow-left " style="margin-bottom:10px;cursor: pointer;"></i> Imprimir</a><br>

   <img src="{{ asset('img/logo.png') }}" width="120px">
</p>
<div class="content" style="margin-right: 25px" >

   <p style="line-height : 35px; text-align:center">
    {{ config('app.name') }}<br>

       {{ $pago->fecha->format('d-m-Y H:i:s') }}<br>
       <hr>
   </p>
   <p style="line-height : 20px;">
       FOLIO:<br>
       {{ $pago->folio }}<br>
   </p>
   <p style="line-height : 50px;">
       <b>Sucursal:</b> {{ $pago->sucursal->nombre }}
    </p>
   <hr>
    <p style="line-height : 10px;">
        <b>Monto Total del pago:</b> {{ number_format($pago->monto,2,'.',',') }}
    </p>
    <hr>

    <u>Désgloce del pago</u>
    <br>
        <table  style="width:100%; margin-bottom:100px; margin-right: 25px">
            @foreach ($pago->abonos as $abono)
                <tr>
                    <td>{{ $abono->alumno_pago->concepto }}</td>
                    <td style="text-align: right">$ {{ number_format($abono->monto,2,'.',',') }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2" style="padding-top:35px;border-top:2px solid black; text-align:right"><b>Saldo: $ {{ number_format($pago->abonos->sum('monto'),2,'.',',') }}</td>
            </tr>
        </table>


       Para generar factura solicitar el mismo día del pago.<br>
       Este ticket no es un comprobante fiscal.<br>
       Gracias por su pago.
       <br>
       <p style="font-size:10pt">T: {{ $pago->folio }}</p>
</div>
<script>
   setTimeout(function () { window.print(); }, 500);
</script>
