@extends('gp247-front::layout')

@section('title', '¡Pago Exitoso!')

@section('content')
<div class="tropipay-success-container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-success">
                <div class="card-header bg-success text-white text-center">
                    <h2><i class="fas fa-check-circle"></i> ¡Pago Exitoso!</h2>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h4 class="text-success mb-3">Tu pago ha sido procesado correctamente</h4>
                    
                    <div class="row">
                        <div class="col-md-8 mx-auto">
                            <div class="alert alert-success">
                                <h5><i class="fas fa-shopping-bag"></i> Detalles de la Orden</h5>
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Número de Orden:</strong></td>
                                            <td>#{{ $order->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Pagado:</strong></td>
                                            <td class="h5 text-success">{{ gp247_currency_render($order->total) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Método de Pago:</strong></td>
                                            <td>
                                                <span class="badge badge-success">
                                                    <i class="fas fa-credit-card"></i> TropiPay
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Fecha:</strong></td>
                                            <td>{{ $order->updated_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <p class="lead">¡Gracias por tu compra! Te enviaremos una confirmación por email.</p>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('home') }}" class="btn btn-primary btn-lg mr-3">
                            <i class="fas fa-home"></i> Volver al Inicio
                        </a>
                        <a href="{{ route('shop.product.index') }}" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-shopping-cart"></i> Seguir Comprando
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.tropipay-success-container {
    padding: 2rem 0;
}

.tropipay-success-container .card {
    border-radius: 15px;
}

.tropipay-success-container .card-header {
    border-radius: 15px 15px 0 0 !important;
}

.tropipay-success-container .fas.fa-check-circle {
    animation: bounce 1s infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}
</style>
@endpush