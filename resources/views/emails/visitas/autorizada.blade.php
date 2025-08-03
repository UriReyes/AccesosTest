<h1>Visita autorizada</h1>

<p>Hola {{ $visita->nombre_visitante }},</p>

<p>Tu visita a <strong>{{ $visita->sede->nombre }}</strong> ha sido autorizada para el día
    <strong>{{ $visita->fecha }}</strong>.</p>

<p>Presenta el siguiente código QR al ingresar:</p>

<p style="text-align: center;">
    <img src="{{ $message->embed($qrPath) }}" alt="Código QR de acceso" width="200" height="200" />
</p>

<p>También se adjuntó este QR como archivo en caso de que no se visualice correctamente.</p>
