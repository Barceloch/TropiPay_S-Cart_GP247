@extends('gp247-core::layout')

@section('main')
<div class="card card-primary card-outline card-outline-tabs">
  <div class="card-header p-0 border-bottom-0">
    <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" id="tab-settings-tab" data-toggle="pill" href="#tab-settings" role="tab" aria-controls="tab-settings" aria-selected="false">{{ gp247_language_render($plugin->appPath.'::lang.plugin_settings') }}</a>
      </li>
    </ul>
  </div>
  
  <div class="card-body">
    <div class="tab-content" id="custom-tabs-four-tabContent">
        <div class="tab-pane fade active show" id="tab-settings" role="tabpanel" aria-labelledby="tab-settings-tab">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-check"></i> {{ gp247_language_render($plugin->appPath.'::lang.admin.success') }}</h5>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-exclamation-triangle"></i> {{ gp247_language_render($plugin->appPath.'::lang.admin.error') }}</h5>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ gp247_route_admin('admin.tropipay.update_settings') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tropipay_client_id">{{ gp247_language_render($plugin->appPath.'::lang.client_id') }} *</label>
                            <input type="text" 
                                   class="form-control @error('tropipay_client_id') is-invalid @enderror" 
                                   id="tropipay_client_id" 
                                   name="tropipay_client_id" 
                                   value="{{ old('tropipay_client_id', $config['tropipay_client_id']) }}"
                                   placeholder="@if(App::getLocale() == 'es') Ingresa tu Client ID de TropiPay @else Enter your TropiPay Client ID @endif"
                                   required>
                            @error('tropipay_client_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                @if(App::getLocale() == 'es')
                                    obtenido desde el panel de TropiPay
                                @else
                                    obtained from TropiPay panel
                                @endif
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tropipay_client_secret">{{ gp247_language_render($plugin->appPath.'::lang.client_secret') }} *</label>
                            <input type="password" 
                                   class="form-control @error('tropipay_client_secret') is-invalid @enderror" 
                                   id="tropipay_client_secret" 
                                   name="tropipay_client_secret" 
                                   value="{{ old('tropipay_client_secret', $config['tropipay_client_secret']) }}"
                                   placeholder="@if(App::getLocale() == 'es') Ingresa tu Client Secret @else Enter your Client Secret @endif"
                                   required>
                            @error('tropipay_client_secret')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                @if(App::getLocale() == 'es')
                                    obtenido desde el panel de TropiPay
                                @else
                                    obtained from TropiPay panel
                                @endif
                            </small>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ gp247_language_render($plugin->appPath.'::lang.sandbox_mode') }}</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="tropipay_sandbox" 
                                       name="tropipay_sandbox" 
                                       value="1"
                                       {{ $config['tropipay_sandbox'] == '1' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="tropipay_sandbox">
                                    @if(App::getLocale() == 'es')
                                        {{ gp247_language_render($plugin->appPath.'::lang.sandbox_mode') }} (Pruebas)
                                    @else
                                        {{ gp247_language_render($plugin->appPath.'::lang.sandbox_mode') }} (Test)
                                    @endif
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                {{ gp247_language_render($plugin->appPath.'::lang.sandbox_environment') }}
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ gp247_language_render($plugin->appPath.'::lang.enabled') }}</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="tropipay_enabled" 
                                       name="tropipay_enabled" 
                                       value="1"
                                       {{ $config['tropipay_enabled'] == '1' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="tropipay_enabled">
                                    {{ gp247_language_render($plugin->appPath.'::lang.enabled') }}
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                {{ gp247_language_render($plugin->appPath.'::lang.plugin_enabled') }}
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tropipay_default_currency">{{ gp247_language_render($plugin->appPath.'::lang.default_currency') }}</label>
                            <select class="form-control @error('tropipay_default_currency') is-invalid @enderror"
                                    id="tropipay_default_currency"
                                    name="tropipay_default_currency">
                                <option value="USD" {{ old('tropipay_default_currency', $config['tropipay_default_currency']) == 'USD' ? 'selected' : '' }}>
                                    {{ gp247_language_render($plugin->appPath.'::lang.usd') }}
                                </option>
                                <option value="EUR" {{ old('tropipay_default_currency', $config['tropipay_default_currency']) == 'EUR' ? 'selected' : '' }}>
                                    {{ gp247_language_render($plugin->appPath.'::lang.eur') }}
                                </option>
                            </select>
                            @error('tropipay_default_currency')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                {{ gp247_language_render($plugin->appPath.'::lang.currency_description') }}
                            </small>
                        </div>
                    </div>
                </div>

                </div>

                <hr>

                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle"></i> {{ gp247_language_render($plugin->appPath.'::lang.webhook_configuration') }}</h5>
                            <ul class="mb-0">
                                <li>{{ gp247_language_render($plugin->appPath.'::lang.production_webhook_notice') }}</li>
                                <li>{{ gp247_language_render($plugin->appPath.'::lang.webhook_url_notice') }} <code>{{ url('/tropipay/webhook') }}</code></li>
                                <li>{{ gp247_language_render($plugin->appPath.'::lang.tropipay_panel_webhook_notice') }}</li>
                                <ul>
                                    <li><code>payment.paid</code> - {{ gp247_language_render($plugin->appPath.'::lang.payment_paid_event') }}</li>
                                    <li><code>payment.failed</code> - {{ gp247_language_render($plugin->appPath.'::lang.payment_failed_event') }}</li>
                                    <li><code>payment.cancelled</code> - {{ gp247_language_render($plugin->appPath.'::lang.payment_cancelled_event') }}</li>
                                </ul>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ gp247_language_render($plugin->appPath.'::lang.admin.save') }}
                        </button>
                        <button type="button" class="btn btn-info ml-2" onclick="testConnection()">
                            <i class="fas fa-plug"></i> {{ gp247_language_render($plugin->appPath.'::lang.test_connection') }}
                        </button>
                    </div>
                    <div class="col-md-6 text-right">
                        <a href="{{ gp247_route_admin('admin.tropipay.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ gp247_language_render($plugin->appPath.'::lang.admin.back') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
  </div>
</div>

{{-- Connection test modal --}}
<div class="modal fade" id="connectionTestModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ gp247_language_render($plugin->appPath.'::lang.api_connection_test') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="connectionResult">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">{{ gp247_language_render($plugin->appPath.'::lang.admin.loading') }}</span>
                        </div>
                        <p class="mt-3">{{ gp247_language_render($plugin->appPath.'::lang.admin.processing') }}</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ gp247_language_render($plugin->appPath.'::lang.admin.close') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function testConnection() {
    $('#connectionTestModal').modal('show');
    $('#connectionResult').html(`
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="sr-only">{{ gp247_language_render($plugin->appPath.'::lang.admin.loading') }}</span>
            </div>
            <p class="mt-3">{{ gp247_language_render($plugin->appPath.'::lang.admin.processing') }}</p>
        </div>
    `);
    
    // Use AJAX to properly handle the response
    $.ajax({
        url: '{{ gp247_route_admin("admin.tropipay.test_connection") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            action: 'test_connection'
        },
        success: function(response) {
            if (response.success) {
                $('#connectionResult').html(`
                    <div class="alert alert-success">
                        <h5><i class="fas fa-check-circle"></i> ¡Conexión exitosa!</h5>
                        <p>${response.message || 'La conexión con TropiPay ha sido establecida correctamente.'}</p>
                        ${response.balance ? '<pre><small>' + JSON.stringify(response.balance, null, 2) + '</small></pre>' : ''}
                        ${response.debug ? '<small class="text-muted">Endpoint: ' + response.debug.endpoint + '</small>' : ''}
                    </div>
                `);
            } else {
                $('#connectionResult').html(`
                    <div class="alert alert-danger">
                        <h5><i class="fas fa-exclamation-triangle"></i> Error de conexión</h5>
                        <p>${response.message || 'Error desconocido'}</p>
                        ${response.debug ? '<hr><h6>Información de depuración:</h6><pre><small>' + JSON.stringify(response.debug, null, 2) + '</small></pre>' : ''}
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            let errorMessage = 'Error de conexión con el servidor';
            let debugInfo = '';
            
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.message) {
                    errorMessage = response.message;
                }
                if (response.debug) {
                    debugInfo = '<h6>Información de depuración:</h6><pre><small>' + JSON.stringify(response.debug, null, 2) + '</small></pre>';
                }
            } catch (e) {
                errorMessage = 'Error de servidor: ' + (xhr.status || 'Sin código de estado');
            }
            
            $('#connectionResult').html(`
                <div class="alert alert-danger">
                    <h5><i class="fas fa-times-circle"></i> Error del servidor</h5>
                    <p>${errorMessage}</p>
                    <p><strong>Código:</strong> ${xhr.status} - ${xhr.statusText}</p>
                    ${debugInfo}
                </div>
            `);
        }
    });
}
</script>
@endpush