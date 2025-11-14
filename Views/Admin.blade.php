@extends('gp247-core::layout')

@section('main')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-cog"></i> {{ trans('Plugins/TropiPay::lang.admin.config') }}
                </h3>
            </div>
            
            <div class="card-body">
                <form action="{{ route('admin_tropipay.save') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label>{{ trans('Plugins/TropiPay::lang.admin.status') }}</label>
                        <div class="form-check">
                            <input type="checkbox" name="status" value="1" class="form-check-input" 
                                {{ tropipay_get_config('status') == 1 ? 'checked' : '' }}>
                            <label class="form-check-label">Activar TropiPay</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>{{ trans('Plugins/TropiPay::lang.admin.server_mode') }} *</label>
                        <select name="server_mode" class="form-control" required>
                            <option value="Development" {{ tropipay_get_config('server_mode') == 'Development' ? 'selected' : '' }}>
                                Development (Pruebas)
                            </option>
                            <option value="Production" {{ tropipay_get_config('server_mode') == 'Production' ? 'selected' : '' }}>
                                Production (En vivo)
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>{{ trans('Plugins/TropiPay::lang.admin.client_id') }} *</label>
                        <input type="text" name="client_id" class="form-control" 
                            value="{{ tropipay_get_config('client_id') }}" required>
                    </div>

                    <div class="form-group">
                        <label>{{ trans('Plugins/TropiPay::lang.admin.client_secret') }} *</label>
                        <input type="password" name="client_secret" class="form-control" 
                            value="{{ tropipay_get_config('client_secret') }}" required>
                    </div>

                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> Cómo obtener credenciales:</h5>
                        <ul>
                            <li><strong>Development:</strong> <a href="https://tropipay-dev.herokuapp.com" target="_blank">tropipay-dev.herokuapp.com</a> (Código: 123456)</li>
                            <li><strong>Production:</strong> <a href="https://www.tropipay.com" target="_blank">www.tropipay.com</a></li>
                        </ul>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection