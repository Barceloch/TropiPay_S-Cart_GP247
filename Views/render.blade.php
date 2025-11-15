{{-- Views/render.blade.php --}}
@if (!empty($dataPayment))
<div class="payment_method_tropipay payment_method">
    <input id="payment_method_tropipay" type="radio" 
           class="input-radio" 
           name="payment_method" 
           value="TropiPay" 
           {{ ($dataPayment['key'] == 'TropiPay') ? 'checked' : '' }}>
    <label for="payment_method_tropipay">
        <img src="{{ asset('GP247/Plugins/TropiPay/images/logo.jpg') }}" 
             alt="TropiPay" 
             style="height: 30px; vertical-align: middle; margin-right: 10px;">
        {{ $dataPayment['title'] ?? 'TropiPay Payment Gateway' }}
    </label>
    <div class="payment_box payment_method_tropipay" style="display:none; padding: 10px; margin-top: 10px; background: #f9f9f9;">
        <p>{{ trans('Payment/TropiPay::lang.description') }}</p>
        <p><small>{{ trans('Payment/TropiPay::lang.payment_info') }}</small></p>
    </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    const radio = document.getElementById('payment_method_tropipay');
    const box = document.querySelector('.payment_box.payment_method_tropipay');
    
    if (radio && box) {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.payment_box').forEach(b => b.style.display = 'none');
            if (this.checked) {
                box.style.display = 'block';
            }
        });
        
        // Show/hide al cargar si está seleccionado
        if (radio.checked) {
            box.style.display = 'block';
        }
    }
});
</script>