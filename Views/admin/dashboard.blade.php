@extends('gp247-core::layout')

@section('main')
<div class="card card-primary card-outline card-outline-tabs">
  <div class="card-header p-0 border-bottom-0">
    <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" id="tab-dashboard-tab" data-toggle="pill" href="#tab-dashboard" role="tab" aria-controls="tab-dashboard" aria-selected="false">{{ gp247_language_render($plugin->appPath.'::lang.admin.dashboard') }}</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="tab-settings-tab" data-toggle="pill" href="#tab-settings" role="tab" aria-controls="tab-settings" aria-selected="false">{{ gp247_language_render($plugin->appPath.'::lang.admin.settings') }}</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="tab-orders-tab" data-toggle="pill" href="#tab-orders" role="tab" aria-controls="tab-orders" aria-selected="false">{{ gp247_language_render($plugin->appPath.'::lang.admin.orders') }}</a>
      </li>
    </ul>
  </div>
  
  <div class="card-body">
    <div class="tab-content" id="custom-tabs-four-tabContent">
        {{-- DASHBOARD TAB --}}
        <div class="tab-pane fade active show" id="tab-dashboard" role="tabpanel" aria-labelledby="tab-dashboard-tab">
            {{-- Estadísticas generales --}}
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $stats['total_orders'] }}</h3>
                            <p>{{ gp247_language_render($plugin->appPath.'::lang.admin.total_orders') }}</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $stats['paid_orders'] }}</h3>
                            <p>{{ gp247_language_render($plugin->appPath.'::lang.admin.paid_orders') }}</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $stats['pending_orders'] }}</h3>
                            <p>{{ gp247_language_render($plugin->appPath.'::lang.admin.pending_orders') }}</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $stats['failed_orders'] }}</h3>
                            <p>{{ gp247_language_render($plugin->appPath.'::lang.admin.failed_orders') }}</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Información financiera --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-euro-sign"></i> {{ gp247_language_render($plugin->appPath.'::lang.admin.income') }}
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <strong>{{ gp247_language_render($plugin->appPath.'::lang.admin.total_revenue') }}:</strong><br>
                                    <span class="text-success h5">{{ gp247_currency_render($stats['total_revenue']) }}</span>
                                </div>
                                <div class="col-6">
                                    <strong>{{ gp247_language_render($plugin->appPath.'::lang.admin.today_revenue') }}:</strong><br>
                                    <span class="text-info h5">{{ gp247_currency_render($stats['today_revenue']) }}</span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <strong>{{ gp247_language_render($plugin->appPath.'::lang.admin.success_rate') }}:</strong>
                                <div class="progress">
                                    <div class="progress-bar bg-success" style="width: {{ $stats['success_rate'] }}%">
                                        {{ $stats['success_rate'] }}%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cog"></i> {{ gp247_language_render($plugin->appPath.'::lang.admin.plugin_status') }}
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <strong>{{ gp247_language_render($plugin->appPath.'::lang.admin.mode') }}:</strong><br>
                                    <span class="badge badge-{{ $config['tropipay_sandbox'] == '1' ? 'warning' : 'success' }}">
                                        {{ $config['tropipay_sandbox'] == '1' ? gp247_language_render($plugin->appPath.'::lang.admin.sandbox') : gp247_language_render($plugin->appPath.'::lang.admin.production') }}
                                    </span>
                                </div>
                                <div class="col-6">
                                    <strong>{{ gp247_language_render($plugin->appPath.'::lang.admin.status') }}:</strong><br>
                                    <span class="badge badge-{{ $config['tropipay_enabled'] == '1' ? 'success' : 'danger' }}">
                                        {{ $config['tropipay_enabled'] == '1' ? gp247_language_render($plugin->appPath.'::lang.admin.enabled') : gp247_language_render($plugin->appPath.'::lang.admin.disabled') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Órdenes recientes --}}
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-history"></i> {{ gp247_language_render($plugin->appPath.'::lang.admin.recent_orders') }}
                            </h3>
                        </div>
                        <div class="card-body">
                            @if($recentOrders->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ gp247_language_render($plugin->appPath.'::lang.admin.id') }}</th>
                                                <th>{{ gp247_language_render($plugin->appPath.'::lang.admin.order_number') }}</th>
                                                <th>{{ gp247_language_render($plugin->appPath.'::lang.admin.customer') }}</th>
                                                <th>{{ gp247_language_render($plugin->appPath.'::lang.admin.amount') }}</th>
                                                <th>{{ gp247_language_render($plugin->appPath.'::lang.admin.status') }}</th>
                                                <th>{{ gp247_language_render($plugin->appPath.'::lang.admin.payment_status') }}</th>
                                                <th>{{ gp247_language_render($plugin->appPath.'::lang.admin.date') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentOrders as $order)
                                                <tr>
                                                    <td>{{ $order->id }}</td>
                                                    <td>
                                                        <strong>{{ $order->order_number ?? $order->id }}</strong>
                                                    </td>
                                                    <td>
                                                        @if($order->customer)
                                                            {{ $order->customer->name ?? 'N/A' }}
                                                            <br><small class="text-muted">{{ $order->customer->email ?? 'N/A' }}</small>
                                                        @else
                                                            <span class="text-muted">Guest</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <strong>{{ gp247_currency_render($order->total) }}</strong>
                                                        <br><small class="text-muted">{{ $order->currency ?? 'USD' }}</small>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $statusClass = match($order->status) {
                                                                1 => 'secondary',
                                                                2 => 'info', 
                                                                3 => 'success',
                                                                4 => 'danger',
                                                                5 => 'warning',
                                                                default => 'secondary'
                                                            };
                                                            $statusName = match($order->status) {
                                                                1 => gp247_language_render($plugin->appPath.'::lang.admin.pending'),
                                                                2 => gp247_language_render($plugin->appPath.'::lang.admin.processing'),
                                                                3 => gp247_language_render($plugin->appPath.'::lang.admin.completed'),
                                                                4 => gp247_language_render($plugin->appPath.'::lang.admin.cancelled'),
                                                                5 => gp247_language_render($plugin->appPath.'::lang.admin.failed'),
                                                                default => 'Unknown'
                                                            };
                                                        @endphp
                                                        <span class="badge badge-{{ $statusClass }}">{{ $statusName }}</span>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $paymentClass = match($order->payment_status) {
                                                                1 => 'secondary',
                                                                2 => 'warning',
                                                                3 => 'success',
                                                                4 => 'danger',
                                                                default => 'secondary'
                                                            };
                                                            $paymentName = match($order->payment_status) {
                                                                1 => gp247_language_render($plugin->appPath.'::lang.admin.pending'),
                                                                2 => gp247_language_render($plugin->appPath.'::lang.admin.processing'),
                                                                3 => gp247_language_render($plugin->appPath.'::lang.admin.paid'),
                                                                4 => gp247_language_render($plugin->appPath.'::lang.admin.failed'),
                                                                default => 'Unknown'
                                                            };
                                                        @endphp
                                                        <span class="badge badge-{{ $paymentClass }}">{{ $paymentName }}</span>
                                                    </td>
                                                    <td>
                                                        <strong>{{ $order->created_at->format('d/m/Y') }}</strong>
                                                        <br><small class="text-muted">{{ $order->created_at->format('H:i:s') }}</small>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="text-center mt-3">
                                    <a href="#" onclick="$('#tab-orders-tab').click()" class="btn btn-outline-primary">
                                        {{ gp247_language_render($plugin->appPath.'::lang.admin.view_all_orders') }}
                                    </a>
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>{{ gp247_language_render($plugin->appPath.'::lang.admin.no_orders_yet') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SETTINGS TAB --}}
        <div class="tab-pane fade" id="tab-settings" role="tabpanel" aria-labelledby="tab-settings-tab">
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

                <hr>

                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle"></i> {{ gp247_language_render($plugin->appPath.'::lang.webhook_configuration') }}</h5>
                            <ul class="mb-0">
                                <li>@if(App::getLocale() == 'es') Antes de usar en producción, asegúrate de tener configurados los webhooks en tu cuenta TropiPay. @else Before using in production, make sure you have configured webhooks in your TropiPay account. @endif</li>
                                <li>@if(App::getLocale() == 'es') La URL del webhook para tu tienda es: @else The webhook URL for your store is: @endif <code>{{ url('/tropipay/webhook') }}</code></li>
                                <li>@if(App::getLocale() == 'es') En el panel de TropiPay, configura el webhook para recibir notificaciones de: @else In TropiPay panel, configure the webhook to receive notifications of: @endif</li>
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
                </div>
            </form>
        </div>

        {{-- ORDERS TAB --}}
        <div class="tab-pane fade" id="tab-orders" role="tabpanel" aria-labelledby="tab-orders-tab">
            {{-- Filtros --}}
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-filter"></i> {{ gp247_language_render($plugin->appPath.'::lang.admin.filter') }}
                            </h3>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ gp247_route_admin('admin.tropipay.orders') }}">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="status">{{ gp247_language_render($plugin->appPath.'::lang.admin.status') }}</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="">{{ gp247_language_render($plugin->appPath.'::lang.admin.all') }}</option>
                                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>{{ gp247_language_render($plugin->appPath.'::lang.admin.pending') }}</option>
                                                <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>{{ gp247_language_render($plugin->appPath.'::lang.admin.processing') }}</option>
                                                <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>{{ gp247_language_render($plugin->appPath.'::lang.admin.completed') }}</option>
                                                <option value="4" {{ request('status') == '4' ? 'selected' : '' }}>{{ gp247_language_render($plugin->appPath.'::lang.admin.cancelled') }}</option>
                                                <option value="5" {{ request('status') == '5' ? 'selected' : '' }}>{{ gp247_language_render($plugin->appPath.'::lang.admin.failed') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="order_id">{{ gp247_language_render($plugin->appPath.'::lang.admin.order_id') }}</label>
                                            <input type="number"
                                                   class="form-control"
                                                   id="order_id"
                                                   name="order_id"
                                                   value="{{ request('order_id') }}"
                                                   placeholder="ID de la orden">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="date_from">{{ gp247_language_render($plugin->appPath.'::lang.admin.date_from') }}</label>
                                            <input type="date"
                                                   class="form-control"
                                                   id="date_from"
                                                   name="date_from"
                                                   value="{{ request('date_from') }}">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="date_to">{{ gp247_language_render($plugin->appPath.'::lang.admin.date_to') }}</label>
                                            <input type="date"
                                                   class="form-control"
                                                   id="date_to"
                                                   name="date_to"
                                                   value="{{ request('date_to') }}">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <div>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-search"></i> {{ gp247_language_render($plugin->appPath.'::lang.admin.search') }}
                                                </button>
                                                <a href="{{ gp247_route_admin('admin.tropipay.orders') }}" class="btn btn-secondary">
                                                    <i class="fas fa-times"></i> {{ gp247_language_render($plugin->appPath.'::lang.admin.clear') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabla de órdenes --}}
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-table"></i> {{ gp247_language_render($plugin->appPath.'::lang.admin.list') }} ({{ $orders->total() }} {{ gp247_language_render($plugin->appPath.'::lang.admin.total') }})
                            </h3>
                        </div>
                        <div class="card-body">
                            @if($orders->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ gp247_language_render($plugin->appPath.'::lang.admin.id') }}</th>
                                                <th>{{ gp247_language_render($plugin->appPath.'::lang.admin.order_number') }}</th>
                                                <th>{{ gp247_language_render($plugin->appPath.'::lang.admin.customer') }}</th>
                                                <th>{{ gp247_language_render($plugin->appPath.'::lang.admin.amount') }}</th>
                                                <th>{{ gp247_language_render($plugin->appPath.'::lang.admin.status') }}</th>
                                                <th>{{ gp247_language_render($plugin->appPath.'::lang.admin.payment_status') }}</th>
                                                <th>{{ gp247_language_render($plugin->appPath.'::lang.admin.date') }}</th>
                                                <th>{{ gp247_language_render($plugin->appPath.'::lang.admin.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($orders as $order)
                                                <tr>
                                                    <td>{{ $order->id }}</td>
                                                    <td>
                                                        <strong>{{ $order->order_number ?? $order->id }}</strong>
                                                    </td>
                                                    <td>
                                                        @if($order->customer)
                                                            {{ $order->customer->name ?? 'N/A' }}
                                                            <br><small class="text-muted">{{ $order->customer->email ?? 'N/A' }}</small>
                                                        @else
                                                            <span class="text-muted">Guest</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <strong>{{ gp247_currency_render($order->total) }}</strong>
                                                        <br><small class="text-muted">{{ $order->currency ?? 'USD' }}</small>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $statusClass = match($order->status) {
                                                                1 => 'secondary',
                                                                2 => 'info', 
                                                                3 => 'success',
                                                                4 => 'danger',
                                                                5 => 'warning',
                                                                default => 'secondary'
                                                            };
                                                            $statusName = match($order->status) {
                                                                1 => gp247_language_render($plugin->appPath.'::lang.admin.pending'),
                                                                2 => gp247_language_render($plugin->appPath.'::lang.admin.processing'),
                                                                3 => gp247_language_render($plugin->appPath.'::lang.admin.completed'),
                                                                4 => gp247_language_render($plugin->appPath.'::lang.admin.cancelled'),
                                                                5 => gp247_language_render($plugin->appPath.'::lang.admin.failed'),
                                                                default => 'Unknown'
                                                            };
                                                        @endphp
                                                        <span class="badge badge-{{ $statusClass }}">{{ $statusName }}</span>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $paymentClass = match($order->payment_status) {
                                                                1 => 'secondary',
                                                                2 => 'warning',
                                                                3 => 'success',
                                                                4 => 'danger',
                                                                default => 'secondary'
                                                            };
                                                            $paymentName = match($order->payment_status) {
                                                                1 => gp247_language_render($plugin->appPath.'::lang.admin.pending'),
                                                                2 => gp247_language_render($plugin->appPath.'::lang.admin.processing'),
                                                                3 => gp247_language_render($plugin->appPath.'::lang.admin.paid'),
                                                                4 => gp247_language_render($plugin->appPath.'::lang.admin.failed'),
                                                                default => 'Unknown'
                                                            };
                                                        @endphp
                                                        <span class="badge badge-{{ $paymentClass }}">{{ $paymentName }}</span>
                                                    </td>
                                                    <td>
                                                        <strong>{{ $order->created_at->format('d/m/Y') }}</strong>
                                                        <br><small class="text-muted">{{ $order->created_at->format('H:i:s') }}</small>
                                                    </td>
                                                    <td>
                                                        @if($order->order_number)
                                                            <a href="{{ route('admin.order.detail', ['id' => $order->id]) }}" class="btn btn-sm btn-info" target="_blank">
                                                                <i class="fas fa-eye"></i> {{ gp247_language_render($plugin->appPath.'::lang.admin.view_details') }}
                                                            </a>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                {{-- Paginación --}}
                                <div class="mt-3">
                                    {{ $orders->links() }}
                                </div>
                            @else
                                <div class="text-center text-muted py-5">
                                    <i class="fas fa-search fa-3x mb-3"></i>
                                    <h4>{{ gp247_language_render($plugin->appPath.'::lang.admin.no_results') }}</h4>
                                    <p>{{ gp247_language_render($plugin->appPath.'::lang.admin.no_orders_found') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                    <h5><i class="icon fas fa-check"></i> {{ gp247_language_render($plugin->appPath.'::lang.admin.success') }}</h5>
                    <p>${data.message}</p>
                    <p><strong>Estado:</strong> ${data.connection_info.authenticated ? 'Conectado' : 'Desconectado'}</p>
                </div>
            `);
        } else {
            $('#connectionResult').html(`
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-times"></i> {{ gp247_language_render($plugin->appPath.'::lang.admin.error') }}</h5>
                    <p>${data.message}</p>
                </div>
            `);
        }
    })
    .catch(error => {
        $('#connectionResult').html(`
            <div class="alert alert-danger">
                <h5><i class="icon fas fa-times"></i> {{ gp247_language_render($plugin->appPath.'::lang.admin.error') }}</h5>
                <p>{{ gp247_language_render($plugin->appPath.'::lang.admin.error') }}: ${error.message}</p>
            </div>
        `);
    });
}
</script>
@endpush