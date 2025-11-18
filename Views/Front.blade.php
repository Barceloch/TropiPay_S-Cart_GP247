<div class="row">
    <div class="col-md-12">
        <h4>{{ trans('TropiPay::lang.title') }}</h4>
        <p>{{ trans('TropiPay::lang.payment_description') }}</p>
        
        <div class="form-group">
            <label for="tropipay_payment">
                <input type="radio" name="payment_method" id="tropipay_payment" value="TropiPay">
                <img src="{{ asset('GP247/Plugins/TropiPay/images/logo.jpg') }}" alt="TropiPay" style="width: 120px; margin-left: 10px;">
            </label>
        </div>
    </div>
</div>