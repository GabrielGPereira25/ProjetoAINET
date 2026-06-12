<h1>Olá, {{ $order->customer->user->name }}!</h1>
<p>Infelizmente, informamos que a tua encomenda nº <strong>{{ $order->id }}</strong> foi cancelada.</p>
@if($order->reason_for_cancellation)
    <p>Motivo: {{ $order->reason_for_cancellation }}</p>
@endif
<p>Se tiveres dúvidas, por favor entra em contacto com o nosso suporte.</p>
<br>
<p>Os nossos cumprimentos,<br>A equipa da Loja</p>
