@extends('gp247-front::layout')
@section('title', 'Pago con TropiPay')

@push('styles')
<link href="{{ asset('GP247/Plugins/TropiPay/css/tropipay.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="tropipay-payment-container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h3><i class="fas fa-credit-card"></i> Pago con TropiPay</h3>
                </div>
                <div class="card-body">
                    <div class="order-summary mb-4">
                        <h5>Resumen de la Orden</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Número de orden:</strong></td>
                                    <td>#{{ $order->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total a pagar:</strong></td>
                                    <td><span class="text-primary h5">{{ gp247_currency_render($order->total) }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Método de pago:</strong></td>
                                    <td>TropiPay <span class="badge badge-info">Seguro</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <h6><i class="fas fa-shield-alt"></i> Pago Seguro</h6>
                        <p class="mb-0">Serás redirigido a TropiPay para completar tu pago de forma segura.</p>
                    </div>

                    <button id="tropipay-button" onclick="processTropiPay()" class="btn btn-success btn-lg btn-block">
                        <i class="fas fa-external-link-alt"></i> Continuar con TropiPay
                    </button>

                    <div class="text-center mt-3">
                        <a href="{{ route('cart') }}" class="btn btn-link">
                            <i class="fas fa-arrow-left"></i> Volver al carrito
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="loading-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-3">Procesando pago, por favor espera...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('GP247/Plugins/TropiPay/js/tropipay.js') }}"></script>
<script>
function processTropiPay() {
    $('#tropipay-button').prop('disabled', true);
    $('#loading-modal').modal('show');

    fetch('{{ route("tropipay.process") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            order_id: {{ $order->id }}
        })
    })
    .then(response => response.json())
    .then(data => {
        $('#loading-modal').modal('hide');
        
        if (data.error == 0) {
            window.location.href = data.url;
        } else {
            alert('Error: ' + data.msg);
            $('#tropipay-button').prop('disabled', false);
        }
    })
    .catch(error => {
        $('#loading-modal').modal('hide');
        console.error('Error:', error);
        alert('Error al procesar el pago');
        $('#tropipay-button').prop('disabled', false);
    });
}
</script>
@endpush