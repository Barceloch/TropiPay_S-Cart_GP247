@extends('front.shop.shop_layout')
@section('main')
<div class="container mt-5">
    <div class="card border-success">
        <div class="card-header bg-success text-white text-center">
            <h3>✓ Payment Successful</h3>
        </div>
        <div class="card-body text-center">
            <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
            <h4 class="mt-3">Thank you for your purchase!</h4>
            <p>{{ $message }}</p>
            <div class="alert alert-info mt-4">
                <strong>Order #{{ $order->id }}</strong><br>
                Total: {{ $order->currency }} {{ number_format($order->total, 2) }}
            </div>
            <a href="{{ route('home') }}" class="btn btn-primary mt-3">Back to Home</a>
        </div>
    </div>
</div>
@endsection