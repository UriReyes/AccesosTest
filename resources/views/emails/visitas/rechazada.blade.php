<h1>Visita rechazada</h1>
<p>Hola {{ $visita->nombre_visitante }},</p>
<p>Lamentablemente, tu visita a <strong>{{ $visita->sede->nombre }}</strong> para el día
    <strong>{{ $visita->fecha }}</strong> ha sido rechazada.</p>
<p>Si tienes dudas, por favor comunícate con el responsable correspondiente.</p>
