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
            min-height: 100vh;
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

        .table-card {
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
        }

        .table-card .table thead th {
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
            color: #334155;
            font-weight: 600;
            font-size: 0.875rem;
            border-bottom: 2px solid #e2e8f0;
            white-space: nowrap;
        }

        .table-card .table tbody tr:hover {
            background-color: rgba(56, 189, 248, 0.06);
        }

        .invoice-pill {
            font-family: ui-monospace, monospace;
            font-size: 0.8rem;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }

        .pagination {
            margin-bottom: 0;
        }

        .empty-state {
            border: 2px dashed #e2e8f0;
            border-radius: 1rem;
            background: #fafafa;
        }

        .actions-cell {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
            justify-content: center;
            align-items: center;
        }

        .actions-cell .btn {
            min-width: auto;
            font-size: 0.78rem;
            padding: 0.25rem 0.5rem;
        }
    </style>
</head>
<body>
    @include('partials.site-header')
    <div class="app-shell">
        <aside class="sidebar">
            <h2 class="h5 sidebar-title">القائمة الرئيسية</h2>
            <nav class="nav flex-column sidebar-nav">
                <a class="nav-link {{ request()->routeIs('dashboard.purchases') ? 'active' : '' }}" href="{{ route('dashboard.purchases') }}">
                    <i class="bi bi-receipt-cutoff"></i>
                    فواتير المشتريات
                </a>
                <a class="nav-link {{ request()->routeIs('dashboard.suppliers') ? 'active' : '' }}" href="{{ route('dashboard.suppliers') }}">
                    <i class="bi bi-truck"></i>
                    الموردين
                </a>
                <a class="nav-link {{ request()->routeIs('dashboard.products') ? 'active' : '' }}" href="{{ route('dashboard.products') }}">
                    <i class="bi bi-box-seam"></i>
                    الاصناف
                </a>
            </nav>
        </aside>

        <div class="app-main">
            <header class="topbar p-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <h1 class="h4 topbar-title d-flex align-items-center gap-2">
                        <i class="bi bi-receipt-cutoff text-info"></i>
                        فواتير المشتريات
                    </h1>
                    <div class="d-flex gap-2">
                        <a href="{{ route('dashboard.purchases.export') }}" class="btn btn-p-accent btn-sm px-3 shadow-sm">
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

                    @if($purchases->count() === 0)
                        <div class="card-body py-5">
                            <div class="empty-state text-center py-5 px-3">
                                <i class="bi bi-inbox display-4 text-muted d-block mb-3"></i>
                                <p class="mb-0 text-muted fw-semibold">لا توجد فواتير لعرضها حالياً</p>
                                <p class="small text-muted mt-2 mb-0">يمكنك إضافة بيانات من لوحة الإدارة أو قاعدة البيانات</p>
                            </div>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
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
                                                    {{ optional($purchase->date)->format('Y/m/d') ?? '—' }}
                                                </td>
                                                <td>
                                                    <span class="badge text-bg-light border text-secondary">
                                                        {{ $item->unit?->name ?? '—' }}
                                                    </span>
                                                </td>
                                                <td>{{ $item->product?->name ?? '—' }}</td>
                                                <td>
                                                    <span class="fw-semibold">{{ number_format((float) $item->quantity, 0, '.', '') }}</span>
                                                </td>
                                                <td class="pe-4 text-center">
                                                    <div class="actions-cell">
                                                        {{-- <a
                                                        style="display: none"
                                                            href="{{ route('dashboard.purchases.export-one', $purchase) }}"
                                                            class="btn btn-sm btn-outline-success"
                                                            title="تصدير كل بنود هذه الفاتورة في ملف Excel"
                                                        >
                                                            <i class="bi bi-receipt ms-1"></i>
                                                            تصدير الفاتورة 
                                                        </a> --}}
                                                        <a
                                                           {{-- style="display: none" --}}
                                                            href="{{ route('dashboard.purchases.export-item', $item) }}"
                                                            class="btn btn-sm btn-outline-primary"
                                                            title="تصدير هذا السطر (البند) فقط في ملف Excel"
                                                        >
                                                            <i class="bi bi-file-earmark-arrow-down ms-1" ></i>
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
                    @endif
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
