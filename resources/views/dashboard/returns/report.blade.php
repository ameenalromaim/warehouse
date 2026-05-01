<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $title }}</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<style>
        :root {
            --p-bg: #0f172a;
            --p-bg-soft: #1e293b;
            --p-accent: #38bdf8;
            --p-accent-2: #818cf8;
        }

        body {
            font-family: 'Cairo', system-ui, sans-serif;
            background: linear-gradient(160deg, var(--p-bg) 0%, #1a1f35 45%, var(--p-bg-soft) 100%);
            min-height: 100vh;
            margin: 0;
            color: #0f172a;
            padding-top: 72px;
        }

        .site-main-header {
            position: fixed;
            top: 0;
            inset-inline: 0;
            height: 72px;
            z-index: 1050;
            background: rgba(15, 23, 42, 0.95);
            border-bottom: 1px solid rgba(148, 163, 184, 0.2);
            backdrop-filter: blur(6px);
        }

        .site-main-title {
            color: #f8fafc;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .app-shell {
            min-height: calc(100vh - 72px);
            display: flex;
        }

        .sidebar {
            width: 260px;
            background: rgba(15, 23, 42, 0.95);
            border-inline-start: 1px solid rgba(148, 163, 184, 0.2);
            padding: 1.5rem 1rem;
        }

        .sidebar-title {
            color: #f8fafc;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .sidebar-nav .nav-link {
            color: #cbd5e1;
            border-radius: 0.75rem;
            padding: 0.7rem 0.85rem;
            margin-bottom: 0.4rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            background: rgba(56, 189, 248, 0.2);
            color: #f8fafc;
        }

        .app-main {
            flex: 1;
            padding: 1rem;
        }

        .topbar {
            background: linear-gradient(120deg, rgba(56, 189, 248, 0.12) 0%, rgba(129, 140, 248, 0.15) 100%);
            border: 1px solid rgba(148, 163, 184, 0.15);
            border-radius: 1rem;
            margin-bottom: 1rem;
        }

        .topbar-title {
            color: #f8fafc;
            margin: 0;
        }

        .btn-p-accent {
            background: linear-gradient(135deg, var(--p-accent) 0%, var(--p-accent-2) 100%);
            border: none;
            color: #0f172a;
            font-weight: 600;
        }

        .btn-p-accent:hover {
            filter: brightness(1.05);
            color: #0f172a;
        }
        .table-card{
            border-radius:1rem;
            overflow:hidden;
            box-shadow:0 25px 50px -12px rgba(0,0,0,.35);
        }
        .table-card .table thead th{
            background:linear-gradient(180deg,#f8fafc 0%,#f1f5f9 100%);
            color:#334155;
            font-weight:600;
            font-size:.875rem;
            border-bottom:2px solid #e2e8f0;
            white-space:nowrap;
        }
        .table-card .table tbody tr:hover{
            background-color:rgba(56,189,248,.06);
        }
        .id-pill{
            font-family:ui-monospace,monospace;
            font-size:.8rem;
            background:#f0fdf4;
            color:#166534;
            border:1px solid #bbf7d0;
        }
        .id-pill.damage{
            background:#fef2f2;
            color:#991b1b;
            border:1px solid #fecaca;
        }
        .filter-bar label{
            font-size:.75rem;
            color:#64748b;
            margin-bottom:.2rem;
        }
</style>
</head>

<body>

@include('partials.site-header')

<div class="app-shell">

@include('partials.dashboard-sidebar')

<div class="app-main">

<header class="topbar p-3">
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2">

<h1 class="h4 topbar-title d-flex align-items-center gap-2">
<i class="bi {{ $type === 'damage' ? 'bi-exclamation-octagon text-warning' : 'bi-arrow-return-left text-info' }}"></i>
{{ $title }}
</h1>

<div class="d-flex gap-2">
<a class="btn btn-p-accent btn-sm px-3 shadow-sm"
href="{{ route('dashboard.reports.returns.export', array_merge(['type' => $type], request()->only(['supplier', 'date', 'product']))) }}">
<i class="bi bi-file-earmark-spreadsheet ms-1"></i>
تصدير Excel
</a>
</div>

</div>
</header>

<main>

@php
    $indexRoute = $type === 'damage' ? 'dashboard.reports.returns.damage' : 'dashboard.reports.returns.normal';
@endphp

<div class="card table-card border-0 mb-3">
<div class="card-body bg-white py-3">
<form method="get" action="{{ route($indexRoute) }}" class="row g-2 align-items-end filter-bar">
<div class="col-sm-6 col-md-4 col-lg-3">
<label for="f_supplier" class="form-label mb-0">المورد</label>
<input type="text" name="supplier" id="f_supplier" class="form-control form-control-sm"
value="{{ request('supplier') }}" placeholder="اسم المورد" autocomplete="off">
</div>
<div class="col-sm-6 col-md-3 col-lg-2">
<label for="f_date" class="form-label mb-0">التاريخ</label>
<input type="date" name="date" id="f_date" class="form-control form-control-sm"
value="{{ request('date') }}">
</div>
<div class="col-sm-6 col-md-4 col-lg-3">
<label for="f_product" class="form-label mb-0">الصنف</label>
<input type="text" name="product" id="f_product" class="form-control form-control-sm"
value="{{ request('product') }}" placeholder="اسم الصنف" autocomplete="off">
</div>
<div class="col-sm-6 col-md-auto d-flex flex-wrap gap-2 pt-1">
<button type="submit" class="btn btn-primary btn-sm">
<i class="bi bi-funnel ms-1"></i>
تصفية
</button>
<a href="{{ route($indexRoute) }}" class="btn btn-outline-secondary btn-sm">
<i class="bi bi-x-lg ms-1"></i>
إعادة تعيين
</a>
</div>
</form>
</div>
</div>

<div class="card table-card border-0">

<div class="card-body border-bottom bg-white py-3">
<span class="text-muted small">
<i class="bi bi-table ms-1"></i>
عدد الحركات في هذه الصفحة: {{ $returns->count() }}
</span>
</div>

<div class="table-responsive">

<table class="table table-hover align-middle mb-0" id="returnsTable">

<thead>
<tr>
<th class="ps-4">رقم الحركة</th>
<th>التاريخ</th>
<th>المورد</th>
<th>الصنف</th>
<th>الوحدة</th>
<th>الكمية</th>
<th class="pe-4">ملاحظات</th>
</tr>
</thead>

<tbody>

@if($returns->isEmpty())
<tr>
<td colspan="7" class="text-center py-4 text-muted">
لا توجد حركات مطابقة للفلتر.
</td>
</tr>
@else
@foreach($returns as $return)
@if($return->items->isEmpty())
<tr>
<td class="ps-4">
<span class="badge rounded-pill id-pill fw-normal {{ $type === 'damage' ? 'damage' : '' }}">
#{{ $return->id }}
</span>
</td>
<td class="text-muted small">
{{ optional($return->date)->format('Y-m-d') ?? '—' }}
</td>
<td class="fw-semibold text-dark">
{{ $return->supplier?->name ?? '—' }}
</td>
<td colspan="3" class="text-warning small">
<i class="bi bi-inbox ms-1"></i>
لا توجد أصناف مسجلة لهذه الحركة.
</td>
<td class="pe-4 small text-secondary">
{{ $return->note ?: '—' }}
</td>
</tr>
@else
@foreach($return->items as $item)
@php
    $pf = request('product');
    $pName = \Illuminate\Support\Str::lower($item->product?->name ?? '');
    $showLine = ! filled($pf) || \Illuminate\Support\Str::contains($pName, \Illuminate\Support\Str::lower(trim((string) $pf)));
@endphp
@if($showLine)

<tr>

<td class="ps-4">
<span class="badge rounded-pill id-pill fw-normal {{ $type === 'damage' ? 'damage' : '' }}">
#{{ $return->id }}
</span>
</td>

<td class="text-muted small">
{{ optional($return->date)->format('Y-m-d') ?? '—' }}
</td>

<td class="fw-semibold text-dark">
{{ $return->supplier?->name ?? '—' }}
</td>

<td>{{ $item->product?->name ?? '—' }}</td>

<td>
<span class="badge text-bg-light border text-secondary">
{{ $item->unit?->name ?? '—' }}
</span>
</td>

<td>
<span class="fw-semibold">
{{ number_format((float) $item->quantity, 2, '.', '') }}
</span>
</td>

<td class="pe-4 small text-secondary">
{{ $return->note ?: '—' }}
</td>

</tr>

@endif
@endforeach
@endif
@endforeach
@endif

</tbody>
</table>

</div>

<div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
{{ $returns->links() }}
</div>

</div>

</main>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
