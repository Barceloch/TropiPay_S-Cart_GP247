@extends('gp247-front::layout')

@section('title', 'Error en el Pago')

@section('content')
<div class="tropipay-error-container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-danger">
                <div class="card-header bg-danger text-white text-center">
                    <h2><i class="fas fa-exclamation-triangle"></i> Error en el Pago</h2>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-exclamation-triangle text-danger" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h4 class="text-danger mb-3">Hubo un problema con tu pago</h4>
                    
                    @if(isset($error))
                        <div class="alert alert-danger" role="alert">
                            <h5><i class="fas fa-info-circle"></i> Detalles del Error</h5>
                            <p class="mb-0">{{ $error }}</p>
                        </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-8 mx-auto">
                            <div class="alert alert-warning">
                                <h5><i class="fas fa-shopping-bag"></i> Información de la Orden</h5>
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Número de Orden:</strong></td>
                                            <td>#{{ $order->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total:</strong></td>
                                            <td class="h5">{{ gp247_currency_render($order->total) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Estado:</strong></td>
                                            <td>
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock"></i> Pendiente de Pago
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Fecha:</strong></td>
                                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-lightbulb"></i> ¿Qué puedes hacer?</h6>
                            <ul class="text-left mb-0">
                                <li>Verificar que tienes fondos suficientes en tu cuenta</li>
                                <li>Comprobar que los datos de tu tarjeta son correctos</li>
                                <li>Contactar con tu banco si el problema persiste</li>
                                <li>Intentar el pago nuevamente en unos minutos</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <p class="text-muted">
                            Si el problema continúa, contacta con nuestro equipo de soporte.
                        </p>
                    </div>
                    
                    <div class="mt-4">
                        <button type="button" class="btn btn-danger btn-lg mr-3" onclick="retryPayment()">
                            <i class="fas fa-redo"></i> Reintentar Pago
                        </button>
                        <a href="{{ route('cart') }}" class="btn btn-outline-danger btn-lg">
                            <i class="fas fa-arrow-left"></i> Volver al Carrito
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
.tropipay-error-container {
    padding: 2rem 0;
}

.tropipay-error-container .card {
    border-radius: 15px;
}

.tropipay-error-container .card-header {
    border-radius: 15px 15px 0 0 !important;
}

.tropipay-error-container .fas.fa-exclamation-triangle {
    animation: shake 1s infinite;
}

@keyframes shake {
    0%, 100% {
        transform: translateX(0);
    }
    10%, 30%, 50%, 70%, 90% {
        transform: translateX(-3px);
    }
    20%, 40%, 60%, 80% {
        transform: translateX(3px);
    }
}
</style>
@endpush

@push('scripts')
<script>
function retryPayment() {
    // Redirigir a la página de pago
    window.location.href = "{{ route('tropipay.payment') }}?order_id={{ $order->id }}";
}
</script>
@endpush