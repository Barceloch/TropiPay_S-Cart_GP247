@extends('front.shop.shop_layout')
@section('main')
<div class="container mt-5">
    <div class="card border-danger">
        <div class="card-header bg-danger text-white text-center">
            <h3>✗ Payment Failed</h3>
        </div>
        <div class="card-body text-center">
            <i class="fas fa-times-circle text-danger" style="font-size: 80px;"></i>
            <h4 class="mt-3">Payment could not be processed</h4>
            <p>{{ $message }}</p>
            <div class="alert alert-warning mt-4">
                Order #{{ $order->id }} - Payment Failed
            </div>
            <a href="{{ route('cart.checkout') }}" class="btn btn-primary mt-3">Try Again</a>
            <a href="{{ route('home') }}" class="btn btn-secondary mt-3">Back to Home</a>
        </div>
    </div>
</div>
