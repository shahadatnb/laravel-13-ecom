@extends('admin.layouts.app')
@section('title', 'Dashboard')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/admin/plugins/chart.js/Chart.min.css') }}">
<style>
  .stat-card { border-left: 4px solid; transition: transform 0.15s; }
  .stat-card:hover { transform: translateY(-2px); }
  .stat-card .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
  .stat-card .stat-value { font-size: 1.6rem; font-weight: 700; line-height: 1.2; }
  .stat-card .stat-label { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: #6c757d; }
  .stat-card .stat-change { font-size: 0.75rem; font-weight: 600; }
  .stat-change.up { color: #28a745; }
  .stat-change.down { color: #dc3545; }
  .stat-change.neutral { color: #6c757d; }
  .chart-container { position: relative; height: 280px; }
  .table-sm td, .table-sm th { padding: 0.45rem 0.6rem; font-size: 0.82rem; }
  .badge-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 6px; }
  .status-pending { background: #ffc107; }
  .status-processing { background: #17a2b8; }
  .status-completed { background: #28a745; }
  .status-cancelled { background: #dc3545; }
  .status-default { background: #6c757d; }
</style>
@endsection

@section('content')
{{-- ═══════════════ ROW 1: Stat Cards ═══════════════ --}}
<div class="row mb-4">
  @php
    $todayVsYesterday = $yesterdayOrders > 0 ? round(($todayOrders - $yesterdayOrders) / $yesterdayOrders * 100) : 0;
    $todayRevenueVsYesterday = $yesterdayRevenue > 0 ? round(($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue * 100) : 0;
  @endphp

  {{-- Total Revenue --}}
  <div class="col-xl-3 col-md-6 mb-3">
    <div class="card stat-card" style="border-left-color: #28a745;">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <div class="stat-label">Total Revenue</div>
          <div class="stat-value text-success">৳{{ number_format($stats['revenue']) }}</div>
          <div class="stat-change {{ $todayRevenueVsYesterday >= 0 ? 'up' : 'down' }}">
            <i class="fas fa-arrow-{{ $todayRevenueVsYesterday >= 0 ? 'up' : 'down' }}"></i>
            {{ abs($todayRevenueVsYesterday) }}% vs yesterday
          </div>
        </div>
        <div class="stat-icon" style="background: #d4edda; color: #28a745;">
          <i class="fas fa-bangladeshi-taka-sign"></i>
        </div>
      </div>
    </div>
  </div>

  {{-- Total Orders --}}
  <div class="col-xl-3 col-md-6 mb-3">
    <div class="card stat-card" style="border-left-color: #007bff;">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <div class="stat-label">Total Orders</div>
          <div class="stat-value text-primary">{{ number_format($stats['orders']) }}</div>
          <div class="stat-change {{ $todayVsYesterday >= 0 ? 'up' : 'down' }}">
            <i class="fas fa-arrow-{{ $todayVsYesterday >= 0 ? 'up' : 'down' }}"></i>
            {{ abs($todayVsYesterday) }}% vs yesterday
          </div>
        </div>
        <div class="stat-icon" style="background: #cce5ff; color: #007bff;">
          <i class="fas fa-shopping-cart"></i>
        </div>
      </div>
    </div>
  </div>

  {{-- Customers --}}
  <div class="col-xl-3 col-md-6 mb-3">
    <div class="card stat-card" style="border-left-color: #fd7e14;">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <div class="stat-label">Customers</div>
          <div class="stat-value" style="color: #fd7e14;">{{ number_format($stats['customers']) }}</div>
          <div class="stat-change neutral">{{ number_format($stats['products']) }} products listed</div>
        </div>
        <div class="stat-icon" style="background: #fff3cd; color: #fd7e14;">
          <i class="fas fa-users"></i>
        </div>
      </div>
    </div>
  </div>

  {{-- Today's Summary --}}
  <div class="col-xl-3 col-md-6 mb-3">
    <div class="card stat-card" style="border-left-color: #6f42c1;">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <div class="stat-label">Today</div>
          <div class="stat-value" style="color: #6f42c1;">{{ $todayOrders }} orders</div>
          <div class="stat-change neutral">৳{{ number_format($todayRevenue) }} revenue</div>
        </div>
        <div class="stat-icon" style="background: #e8daef; color: #6f42c1;">
          <i class="fas fa-calendar-day"></i>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ═══════════════ ROW 2: Quick Status + Pending ═══════════════ --}}
<div class="row mb-4">
  <div class="col-lg-3 col-md-6 mb-3">
    <div class="info-box">
      <span class="info-box-icon" style="background: #fff3cd; color: #e0a800;"><i class="fas fa-clock"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Pending Orders</span>
        <span class="info-box-number">{{ $pendingOrders }}</span>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6 mb-3">
    <div class="info-box">
      <span class="info-box-icon" style="background: #d1ecf1; color: #0c5460;"><i class="fas fa-cog fa-spin"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Processing</span>
        <span class="info-box-number">{{ $processingOrders }}</span>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6 mb-3">
    <div class="info-box">
      <span class="info-box-icon" style="background: #d4edda; color: #155724;"><i class="fas fa-box"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Total Products</span>
        <span class="info-box-number">{{ number_format($stats['products']) }}</span>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6 mb-3">
    <div class="info-box">
      <span class="info-box-icon" style="background: #f8d7da; color: #721c24;"><i class="fas fa-exclamation-triangle"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Low Stock Items</span>
        <span class="info-box-number">{{ $lowStockProducts->count() }}</span>
      </div>
    </div>
  </div>
</div>

{{-- ═══════════════ ROW 3: Revenue Chart + Order Status Pie ═══════════════ --}}
<div class="row mb-4">
  <div class="col-xl-8 col-lg-7 mb-3">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-chart-line mr-1 text-success"></i> Revenue Overview (12 Months)</h3>
      </div>
      <div class="card-body">
        <div class="chart-container">
          <canvas id="revenueChart"></canvas>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-4 col-lg-5 mb-3">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-chart-pie mr-1 text-primary"></i> Order Status</h3>
      </div>
      <div class="card-body">
        <div class="chart-container" style="height: 240px;">
          <canvas id="orderStatusChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ═══════════════ ROW 4: Orders Chart + Payment Status ═══════════════ --}}
<div class="row mb-4">
  <div class="col-xl-8 col-lg-7 mb-3">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-chart-bar mr-1 text-info"></i> Orders Trend (12 Months)</h3>
      </div>
      <div class="card-body">
        <div class="chart-container">
          <canvas id="ordersChart"></canvas>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-4 col-lg-5 mb-3">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-credit-card mr-1 text-warning"></i> Payment Status</h3>
      </div>
      <div class="card-body">
        <div class="chart-container" style="height: 240px;">
          <canvas id="paymentStatusChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ════════
 ROW 5: Top Products + Low Stock --}}
<div class="row mb-4">
  <div class="col-xl-7 mb-3">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-trophy mr-1 text-warning"></i> Top Selling Products</h3>
      </div>
      <div class="card-body p-0">
        @if($topProducts->count())
        <table class="table table-hover table-sm mb-0">
          <thead class="thead-light">
            <tr><th>#</th><th>Product</th><th class="text-right">Qty Sold</th><th class="text-right">Revenue</th></tr>
          </thead>
          <tbody>
            @foreach($topProducts as $i => $product)
            <tr>
              <td><span class="badge badge-{{ $i < 3 ? 'warning' : 'secondary' }}">{{ $i + 1 }}</span></td>
              <td class="text-truncate" style="max-width:250px;">{{ $product->name }}</td>
              <td class="text-right"><strong>{{ number_format($product->total_qty) }}</strong></td>
              <td class="text-right text-success font-weight-bold">৳{{ number_format($product->total_revenue) }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
        @else
        <div class="text-center py-4 text-muted">No order data yet.</div>
        @endif
      </div>
    </div>
  </div>
  <div class="col-xl-5 mb-3">
    <div class="card card-danger">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-exclamation-circle mr-1"></i> Low Stock Alert</h3>
      </div>
      <div class="card-body p-0">
        @if($lowStockProducts->count())
        <table class="table table-hover table-sm mb-0">
          <thead class="thead-light">
            <tr><th>Product</th><th class="text-center">Stock</th><th class="text-center">Min</th><th>Status</th></tr>
          </thead>
          <tbody>
            @foreach($lowStockProducts as $product)
            <tr>
              <td class="text-truncate" style="max-width:180px;">{{ $product->name }}</td>
              <td class="text-center"><strong class="text-danger">{{ $product->stock }}</strong></td>
              <td class="text-center">{{ $product->minimum_stock }}</td>
              <td>
                @if($product->stock == 0)<span class="badge badge-danger">Out of Stock</span>
                @else<span class="badge badge-warning">Low</span>@endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
        @else
        <div class="text-center py-4 text-muted"><i class="fas fa-check-circle text-success mr-1"></i> All products are well stocked.</div>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- ═══════════════ ROW 6: Recent Orders ═══════════════ --}}
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list-alt mr-1"></i> Recent Orders</h3>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary float-right">View All</a>
      </div>
      <div class="card-body p-0">
        @if($recentOrders->count())
        <table class="table table-hover table-sm mb-0">
          <thead class="thead-light">
            <tr><th>Order #</th><th>Customer</th><th class="text-right">Total</th><th>Status</th><th>Payment</th><th>Date</th></tr>
          </thead>
          <tbody>
            @foreach($recentOrders as $order)
            @php
              $sc = ['pending'=>'warning','confirmed'=>'info','processing'=>'info','shipped'=>'primary','delivered'=>'success','completed'=>'success','cancelled'=>'danger','returned'=>'danger'];
              $pc = ['paid'=>'success','pending'=>'warning','failed'=>'danger'];
            @endphp
            <tr>
              <td><a href="{{ route('admin.orders.show', $order) }}"><strong>{{ $order->order_number }}</strong></a></td>
              <td>{{ $order->customer->name ?? 'Guest' }}</td>
              <td class="text-right font-weight-bold">৳{{ number_format($order->grand_total) }}</td>
              <td><span class="badge badge-{{ $sc[$order->status] ?? 'secondary' }}">{{ ucfirst($order->status) }}</span></td>
              <td><span class="badge badge-{{ $pc[$order->payment_status] ?? 'secondary' }}">{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</span></td>
              <td class="text-muted">{{ $order->created_at->format('d M, h:i A') }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
        @else
        <div class="text-center py-4 text-muted">No orders yet.</div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/admin/plugins/chart.js/Chart.min.js') }}"></script>
<script>
$(function() {
  var g={bg:'rgba(40,167,69,0.15)',bd:'#28a745'},b={bg:'rgba(0,123,255,0.15)',bd:'#007bff'};

  new Chart(document.getElementById('revenueChart').getContext('2d'),{
    type:'line',
    data:{
      labels:{!! json_encode($revenueMonths) !!},
      datasets:[{label:'Revenue',data:{!! json_encode($revenueValues) !!},borderColor:g.bd,backgroundColor:g.bg,fill:true,tension:0.4,pointRadius:4,pointHoverRadius:7,borderWidth:2.5}]
    },
    options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:{callbacks:{label:function(c){return '৳'+c.parsed.y.toLocaleString()}}}},scales:{y:{beginAtZero:true,ticks:{callback:function(v){return '৳'+(v/1000).toFixed(0)+'k'}},grid:{color:'rgba(0,0,0,0.05)'}},x:{grid:{display:false}}}}
  });

  var osd={!! json_encode($orderStatusCounts) !!};var sl=Object.keys(osd),sv=Object.values(osd);
  var scm={pending:'#ffc107',confirmed:'#17a2b8',processing:'#007bff',packed:'#6f42c1',shipped:'#fd7e14',delivered:'#20c997',completed:'#28a745',cancelled:'#dc3545',returned:'#6c757d',failed:'#343a40'};
  new Chart(document.getElementById('orderStatusChart').getContext('2d'),{
    type:'doughnut',
    data:{labels:sl.map(function(s){return s.charAt(0).toUpperCase()+s.slice(1)}),datasets:[{data:sv,backgroundColor:sl.map(function(s){return scm[s]||'#adb5bd'}),borderWidth:2,borderColor:'#fff'}]},
    options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom',labels:{padding:12,font:{size:11}}}},cutout:'60%'}
  });

  new Chart(document.getElementById('ordersChart').getContext('2d'),{
    type:'bar',
    data:{labels:{!! json_encode($orderMonths) !!},datasets:[{label:'Orders',data:{!! json_encode($orderValues) !!},backgroundColor:b.bd+'cc',borderColor:b.bd,borderWidth:1,borderRadius:6,barPercentage:0.6}]},
    options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{stepSize:1},grid:{color:'rgba(0,0,0,0.05)'}},x:{grid:{display:false}}}}
  });

  var psd={!! json_encode($paymentStatusCounts) !!};var pl=Object.keys(psd),pv=Object.values(psd);
  var pcm={pending:'#ffc107',paid:'#28a745',partially_paid:'#17a2b8',failed:'#dc3545',refunded:'#6f42c1',cancelled:'#6c757d'};
  new Chart(document.getElementById('paymentStatusChart').getContext('2d'),{
    type:'doughnut',
    data:{labels:pl.map(function(s){return s.replace(/_/g,' ').replace(/\b\w/g,function(c){return c.toUpperCase()})}),datasets:[{data:pv,backgroundColor:pl.map(function(s){return pcm[s]||'#adb5bd'}),borderWidth:2,borderColor:'#fff'}]},
    options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom',labels:{padding:12,font:{size:11}}}},cutout:'60%'}
  });
});
</script>
@endsection
