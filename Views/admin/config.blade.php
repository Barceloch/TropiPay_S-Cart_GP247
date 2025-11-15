@extends('gp247-core::layouts.admin')

@section('content')
<div class="container mt-3">
    <h2>Configuración TropiPay</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form method="POST" action="{{ route('tropipay.admin.config.save') }}">
        @csrf
        <div class="form-group mb-3">
            <label>Modo sandbox</label>
            <select name="sandbox" class="form-control">
                <option value="1" {{ $config['sandbox'] ? 'selected' : '' }}>Sí</option>
                <option value="0" {{ !$config['sandbox'] ? 'selected' : '' }}>No</option>
            </select>
            <small class="form-text text-muted">
                Las credenciales reales se configuran en el fichero <code>.env</code>:
                <br>TROPIPAY_CLIENT_ID, TROPIPAY_CLIENT_SECRET, TROPIPAY_SANDBOX
            </small>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection
