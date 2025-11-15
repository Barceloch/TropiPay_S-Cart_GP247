@extends('gp247-front::layouts.shop')

@section('content')
<div class="container my-5">
    <h2>Resultado del pago con TropiPay</h2>
    <p>{{ $message }}</p>
    <a href="{{ url('/') }}" class="btn btn-primary">Volver a la tienda</a>
</div>
@endsection
