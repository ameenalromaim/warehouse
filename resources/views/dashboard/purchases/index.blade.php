<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>لوحة المشتريات</title>

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
.invoice-pill{
    font-family:ui-monospace,monospace;
    font-size:.8rem;
    background:#eff6ff;
    color:#1d4ed8;
    border:1px solid #bfdbfe;
}
.actions-cell{
    display:flex;
    flex-wrap:wrap;
    gap:.35rem;
    justify-content:center;
    align-items:center;
}
.filter-input{
    min-width:120px;
    font-size:13px;
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
<i class="bi bi-receipt-cutoff text-info"></i>
استلام مخزني
</h1>

<div class="d-flex gap-2">
<a id="exportExcelBtn"
href="{{ route('dashboard.purchases.export') }}"
class="btn btn-p-accent btn-sm px-3 shadow-sm">
<i class="bi bi-file-earmark-spreadsheet ms-1"></i>
تصدير Excel
</a>
</div>

</div>
</header>

<main>

<div class="card table-card border-0">

<div class="card-body border-bottom bg-white py-3">
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
<span class="text-muted small">
<i class="bi bi-table ms-1"></i>
إجمالي الفواتير في هذه الصفحة: {{ $purchases->count() }}
</span>
</div>
</div>

<div class="table-responsive">

<table class="table table-hover align-middle mb-0" id="purchaseTable">

<thead>

<tr>
<th class="ps-4">رقم الفاتورة</th>
<th>المورد</th>
<th>التاريخ</th>
<th>الصنف</th>
<th>الوحدة</th>
<th>الكمية</th>
<th class="pe-4 text-center">الاجراءات</th>
</tr>

<tr>

<th>
<input type="text" id="filterInvoice" class="form-control form-control-sm filter-input column-filter" placeholder="فلتر">
</th>

<th>
<input type="text" id="filterSupplier" class="form-control form-control-sm filter-input column-filter" placeholder="فلتر">
</th>

<th>
<input type="date" id="filterDate" class="form-control form-control-sm filter-input column-filter">
</th>

<th>
<input type="text" id="filterProduct" class="form-control form-control-sm filter-input column-filter" placeholder="فلتر">
</th>

<th></th>
<th></th>

<th class="text-center">

<button id="clearFilters"
class="btn btn-sm btn-outline-danger">
<i class="bi bi-x-circle"></i>
الغاء الفلتر
</button>

</th>

</tr>

</thead>

<tbody>

@foreach($purchases as $purchase)
@foreach($purchase->items as $item)

<tr>

<td class="ps-4">
<span class="badge rounded-pill invoice-pill fw-normal">
{{ $purchase->invoice_number ?: '—' }}
</span>
</td>

<td class="fw-semibold text-dark">
{{ $purchase->supplier?->name ?? '—' }}
</td>

<td class="text-muted small">
{{ optional($purchase->date)->format('Y-m-d') ?? '—' }}
</td>

<td>{{ $item->product?->name ?? '—' }}</td>

<td>
<span class="badge text-bg-light border text-secondary">
{{ $item->unit?->name ?? '—' }}
</span>
</td>

<td>
<span class="fw-semibold">
{{ number_format((float)$item->quantity,0,'.','') }}
</span>
</td>

<td class="pe-4 text-center">
<div class="actions-cell">

<a href="{{ route('dashboard.purchases.export-item',$item) }}"
class="btn btn-sm btn-outline-primary">
<i class="bi bi-file-earmark-arrow-down ms-1"></i>
تصدير الفاتورة
</a>

</div>
</td>

</tr>

@endforeach
@endforeach

</tbody>
</table>

</div>

<div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
{{ $purchases->links() }}
</div>

</div>

</main>

</div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

const table = document.getElementById("purchaseTable");
const rows = table.querySelectorAll("tbody tr");

const supplier = document.getElementById("filterSupplier");
const invoice  = document.getElementById("filterInvoice");
const date     = document.getElementById("filterDate");
const product  = document.getElementById("filterProduct");
const clearBtn = document.getElementById("clearFilters");

function applyFilter(){

rows.forEach(row=>{

const td = row.querySelectorAll("td");

const vInvoice  = td[0].innerText.trim().toLowerCase();
const vSupplier = td[1].innerText.trim().toLowerCase();
const vDate     = td[2].innerText.trim().toLowerCase();
const vProduct  = td[3].innerText.trim().toLowerCase();

const ok =
vInvoice.includes(invoice.value.toLowerCase()) &&
vSupplier.includes(supplier.value.toLowerCase()) &&
vDate.includes(date.value.toLowerCase()) &&
vProduct.includes(product.value.toLowerCase());

row.style.display = ok ? "" : "none";

});

updateExportLink();
}

function updateExportLink(){

let url = new URL("{{ route('dashboard.purchases.export') }}", window.location.origin);

if(supplier.value) url.searchParams.append("supplier", supplier.value);
if(invoice.value)  url.searchParams.append("invoice", invoice.value);
if(date.value)     url.searchParams.append("date", date.value);
if(product.value)  url.searchParams.append("product", product.value);

document.getElementById("exportExcelBtn").href = url.toString();
}

document.querySelectorAll(".column-filter").forEach(input=>{
input.addEventListener("keyup", applyFilter);
input.addEventListener("change", applyFilter);
});

clearBtn.addEventListener("click", function(){

supplier.value = "";
invoice.value  = "";
date.value     = "";
product.value  = "";

applyFilter();

});

});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>