@extends('gp247-core::layout')
@section('main')
      <div class="card card-primary card-outline card-outline-tabs">
        <div class="card-header p-0 border-bottom-0">
          <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" id="tab-admin-config-tab" data-toggle="pill" href="#tab-admin-config" role="tab" aria-controls="tab-admin-config" aria-selected="false">{{ gp247_language_render($plugin->appPath.'::lang.plugin_settings') }}</a>
            </li>
          </ul>
        </div>
        
        <div class="card-body">
          <div class="tab-content" id="custom-tabs-four-tabContent">
              {{-- Tab admin config --}}
              <div class="tab-pane fade active show" id="tab-admin-config" role="tabpanel" aria-labelledby="tab-admin-config-tab">
                  <div class="row">
                        <div class="col-md-12">
                          <div class="card">
                            <div class="card-header with-border">
                              <h3 class="card-title">{{ gp247_language_render($plugin->appPath.'::lang.plugin_settings') }}</h3>
                            </div>
                            <div class="card-body table-responsivep-0">
                             <table class="table table-hover box-body text-wrap table-bordered">
                              <tbody>
                                <tr>
                                    <td width="40%">{{ gp247_language_render($plugin->appPath.'::lang.client_id') }}</td>
                                    <td>
                                        <a href="#" class="editable" 
                                           data-name="TropiPay_client_id" 
                                           data-type="text" 
                                           data-pk="" 
                                           data-url="{{ $urlUpdateConfigGlobal ?? '' }}" 
                                           data-title="Client ID" 
                                           data-value="{{ gp247_config('TropiPay_client_id', '') }}"  
                                           data-original-title="" 
                                           title="" 
                                           data-placement="left"></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ gp247_language_render($plugin->appPath.'::lang.client_secret') }}</td>
                                    <td>
                                        <a href="#" class="editable" 
                                           data-name="TropiPay_client_secret" 
                                           data-type="text" 
                                           data-pk="" 
                                           data-url="{{ $urlUpdateConfigGlobal ?? '' }}" 
                                           data-title="Client Secret" 
                                           data-value="{{ gp247_config('TropiPay_client_secret', '') }}"  
                                           data-original-title="" 
                                           title="" 
                                           data-placement="left"></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ gp247_language_render($plugin->appPath.'::lang.sandbox_mode') }}</td>
                                    <td>
                                        <input type="checkbox" 
                                               class="check-data-config-global" 
                                               name="TropiPay_sandbox" 
                                               value="1"
                                               {{ gp247_config('TropiPay_sandbox', '0') == '1' ? 'checked' : '' }}>
                                        <small class="form-text text-muted">
                                            {{ gp247_language_render($plugin->appPath.'::lang.sandbox_environment') }}
                                        </small>
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ gp247_language_render($plugin->appPath.'::lang.enabled') }}</td>
                                    <td>
                                        <input type="checkbox" 
                                               class="check-data-config-global" 
                                               name="TropiPay_enabled" 
                                               value="1"
                                               {{ gp247_config('TropiPay_enabled', '0') == '1' ? 'checked' : '' }}>
                                        <small class="form-text text-muted">
                                            {{ gp247_language_render($plugin->appPath.'::lang.plugin_enabled') }}
                                        </small>
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ gp247_language_render($plugin->appPath.'::lang.order_status_success') }}</td>
                                    <td>
                                        <a href="#" class="editable-required" 
                                           data-name="TropiPay_order_status_success" 
                                           data-type="select" 
                                           data-pk="" 
                                           data-source="{{ json_encode($orderStatusSuccess ?? []) }}" 
                                           data-url="{{ $urlUpdateConfigGlobal ?? '' }}" 
                                           data-title="{{ gp247_language_render($plugin->appPath.'::lang.order_status_success') }}"
                                           data-value="{{ gp247_config('TropiPay_order_status_success') }}"  
                                           data-original-title="" 
                                           title="" 
                                           data-placement="left"></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ gp247_language_render($plugin->appPath.'::lang.payment_status_success') }}</td>
                                    <td>
                                        <a href="#" class="editable-required" 
                                           data-name="TropiPay_payment_status_success" 
                                           data-type="select" 
                                           data-pk="" 
                                           data-source="{{ json_encode($paymentStatusSuccess ?? []) }}" 
                                           data-url="{{ $urlUpdateConfigGlobal ?? '' }}" 
                                           data-title="{{ gp247_language_render($plugin->appPath.'::lang.payment_status_success') }}"
                                           data-value="{{ gp247_config('TropiPay_payment_status_success') }}"  
                                           data-original-title="" 
                                           title="" 
                                           data-placement="left"></a>
                                    </td>
                                </tr>
                              </tbody>
                             </table>
                            </div>
                        </div>
                      </div>
              </div>
              {{-- // admin config --}}
          </div>
        </div>
        <!-- /.card -->
</div>

{{-- Webhook Info Alert --}}
<div class="alert alert-info">
    <h5><i class="fas fa-info-circle"></i> {{ gp247_language_render($plugin->appPath.'::lang.webhook_configuration') }}</h5>
    <ul class="mb-0">
        <li>Antes de usar en producción, asegúrate de tener configurados los <strong>webhooks</strong> en tu cuenta TropiPay.</li>
        <li>La URL del webhook para tu tienda es: <code>{{ url('/tropipay/webhook') }}</code></li>
        <li>En el panel de TropiPay, configura el webhook para recibir notificaciones de:</li>
        <ul>
            <li><code>payment.paid</code> - {{ gp247_language_render($plugin->appPath.'::lang.payment_paid_event') }}</li>
            <li><code>payment.failed</code> - {{ gp247_language_render($plugin->appPath.'::lang.payment_failed_event') }}</li>
            <li><code>payment.cancelled</code> - {{ gp247_language_render($plugin->appPath.'::lang.payment_cancelled_event') }}</li>
        </ul>
    </ul>
</div>

{{-- Connection Test Button --}}
<div class="row">
    <div class="col-md-12">
        <button type="button" class="btn btn-info" onclick="testConnection()">
            <i class="fas fa-plug"></i> {{ gp247_language_render($plugin->appPath.'::lang.test_connection') }}
        </button>
    </div>
</div>

{{-- Modal de prueba de conexión --}}
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
                            <span class="sr-only">Probando...</span>
                        </div>
                        <p class="mt-3">Probando conexión con TropiPay...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<!-- Editable -->
<link rel="stylesheet" href="{{ gp247_file('GP247/Core/plugin/bootstrap-editable.css')}}">
<style type="text/css">
  #maintain_content img{
    max-width: 100%;
  }
</style>
@endpush

@if (empty($dataNotFound))
@push('scripts')
<!-- Editable -->
<script src="{{ gp247_file('GP247/Core/plugin/bootstrap-editable.min.js')}}"></script>

<script type="text/javascript">
function testConnection() {
    $('#connectionTestModal').modal('show');
    
    fetch('{{ gp247_route_admin("admin.tropipay.test_connection") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            action: 'test_connection'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#connectionResult').html(`
                <div class="alert alert-success">
                    <h5><i class="icon fas fa-check"></i> ¡Conexión Exitosa!</h5>
                    <p>La conexión con TropiPay se estableció correctamente.</p>
                    <p><strong>Balance disponible:</strong> €${data.balance.available[0].amount}</p>
                </div>
            `);
        } else {
            $('#connectionResult').html(`
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-times"></i> Error de Conexión</h5>
                    <p>${data.message}</p>
                </div>
            `);
        }
    })
    .catch(error => {
        $('#connectionResult').html(`
            <div class="alert alert-danger">
                <h5><i class="icon fas fa-times"></i> Error</h5>
                <p>Ocurrió un error inesperado: ${error.message}</p>
            </div>
        `);
    });
}

$(document).ready(function() {
    $.fn.editable.defaults.params = function (params) {
        params._token = "{{ csrf_token() }}";
        params.storeId = "{{ $storeId ?? '' }}";
        return params;
    };

    $('.editable-required').editable({
        validate: function(value) {
            if (value == '') {
                return '{{ gp247_language_render('admin.not_empty') }}';
            }
        },
        success: function(data) {
            if(data.error == 0){
                alertJs('success', '{{ gp247_language_render('admin.msg_change_success') }}');
            } else {
                alertJs('error', data.msg);
            }
        }
    });

    $('.editable').editable({
        validate: function(value) {
        },
        success: function(data) {
            console.log(data);
            if(data.error == 0){
                alertJs('success', '{{ gp247_language_render('admin.msg_change_success') }}');
            } else {
                alertMsg('error', data.msg);
            }
        }
    });

    $('input.check-data-config').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%'
    }).on('ifChanged', function(e) {
        var isChecked = e.currentTarget.checked;
        isChecked = (isChecked == false)?0:1;
        var name = $(this).attr('name');
        $.ajax({
            url: '{{ $urlUpdateConfig ?? '' }}',
            type: 'POST',
            dataType: 'JSON',
            data: {
                "_token": "{{ csrf_token() }}",
                "name": $(this).attr('name'),
                "storeId": "{{ $storeId ?? '' }}",
                "value": isChecked
            },
        })
        .done(function(data) {
            if(data.error == 0){
                alertJs('success', '{{ gp247_language_render('admin.msg_change_success') }}');
            } else {
                alertJs('error', data.msg);
            }
        });
    });

    $('input.check-data-config-global').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%'
    }).on('ifChanged', function(e) {
        var isChecked = e.currentTarget.checked;
        isChecked = (isChecked == false)?0:1;
        var name = $(this).attr('name');
        $.ajax({
            url: '{{ $urlUpdateConfigGlobal ?? '' }}',
            type: 'POST',
            dataType: 'JSON',
            data: {
                "_token": "{{ csrf_token() }}",
                "name": $(this).attr('name'),
                "value": isChecked
            },
        })
        .done(function(data) {
            if(data.error == 0){
                alertJs('success', '{{ gp247_language_render('admin.msg_change_success') }}');
            } else {
                alertJs('error', data.msg);
            }
        });
    });
});
</script>
@endpush
@endif
