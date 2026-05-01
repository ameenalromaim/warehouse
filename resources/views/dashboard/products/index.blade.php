<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>الاصناف</title>
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

        .empty-state {
            border: 2px dashed #e2e8f0;
            border-radius: 1rem;
            background: #fafafa;
        }

        .actions-cell .btn {
            min-width: 88px;
        }

        .delete-product-modal .modal-content {
            border: none;
            border-radius: 1.25rem;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(220, 38, 38, 0.35), 0 0 0 1px rgba(15, 23, 42, 0.06);
        }

        .delete-product-modal .delete-modal-head {
            background: linear-gradient(145deg, #f87171 0%, #dc2626 42%, #991b1b 100%);
            position: relative;
            padding: 1.75rem 1.25rem 1.5rem;
        }

        .delete-product-modal .delete-modal-head::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 30% 20%, rgba(255, 255, 255, 0.2) 0%, transparent 45%);
            pointer-events: none;
        }

        .delete-product-modal .delete-modal-icon {
            width: 4.25rem;
            height: 4.25rem;
            margin: 0 auto 1rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.85rem;
            color: #fef2f2;
            background: rgba(0, 0, 0, 0.15);
            border: 2px solid rgba(255, 255, 255, 0.35);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 1;
        }

        .delete-product-modal .delete-modal-title {
            color: #fff;
            font-weight: 700;
            font-size: 1.15rem;
            margin: 0;
            position: relative;
            z-index: 1;
        }

        .delete-product-modal .delete-modal-sub {
            color: rgba(255, 255, 255, 0.88);
            font-size: 0.875rem;
            margin: 0.35rem 0 0;
            position: relative;
            z-index: 1;
        }

        .delete-product-modal .delete-modal-body {
            padding: 1.5rem 1.25rem 1.25rem;
            background: linear-gradient(180deg, #fff 0%, #f8fafc 100%);
        }

        .delete-product-modal .delete-modal-name {
            display: inline-block;
            margin-top: 0.65rem;
            padding: 0.35rem 1rem;
            border-radius: 999px;
            background: linear-gradient(180deg, #fef2f2 0%, #fee2e2 100%);
            border: 1px solid rgba(220, 38, 38, 0.2);
            color: #991b1b;
            font-weight: 700;
            max-width: 100%;
            word-break: break-word;
        }

        .delete-product-modal .modal-footer {
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
            padding: 1rem 1.25rem;
        }

        .delete-product-modal .btn-delete-confirm {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border: none;
            font-weight: 600;
            box-shadow: 0 4px 14px rgba(220, 38, 38, 0.45);
        }

        .delete-product-modal .btn-delete-confirm:hover {
            filter: brightness(1.06);
            color: #fff;
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
                        <i class="bi bi-box-seam text-info"></i>
                        الاصناف
                    </h1>
                    <button type="button" class="btn btn-p-accent btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#importProductsModal">
                        <i class="bi bi-upload ms-1"></i>
                        استيراد ملف Excel
                    </button>
                </div>
            </header>

            <main>
                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="card table-card border-0">
                    <div class="card-body border-bottom bg-white py-3">
                        <span class="text-muted small">
                            <i class="bi bi-table ms-1"></i>
                            إجمالي الاصناف في هذه الصفحة: {{ $products->count() }}
                        </span>
                    </div>

                    @if($products->count() === 0)
                        <div class="card-body py-5">
                            <div class="empty-state text-center py-5 px-3">
                                <i class="bi bi-inbox display-4 text-muted d-block mb-3"></i>
                                <p class="mb-0 text-muted fw-semibold">لا توجد بيانات اصناف حالياً</p>
                            </div>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">الاسم</th>
                                        <th>الكود</th>
                                        <th>الوحدة</th>
                                        <th class="pe-4 text-center">الاجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $item)
                                        <tr>
                                            <td class="ps-4 fw-semibold text-dark">{{ $item->name ?? '—' }}</td>
                                            <td>{{ $item->code ?? '—' }}</td>
                                            <td>{{ $item->unit?->name ?? '—' }}</td>
                                            <td class="pe-4">
                                                <div class="d-flex justify-content-center gap-2 actions-cell">
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editProductModal"
                                                        data-id="{{ $item->uuid }}"
                                                        data-name="{{ $item->name }}"
                                                        data-code="{{ $item->code }}"
                                                        data-unit-uuid="{{ $item->unit_uuid }}"
                                                        data-description="{{ $item->description }}"
                                                    >
                                                        <i class="bi bi-pencil-square ms-1"></i>
                                                        تعديل
                                                    </button>
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteProductModal"
                                                        data-delete-url="{{ route('dashboard.products.destroy', $item) }}"
                                                        data-product-name="{{ $item->name ?? '—' }}"
                                                    >
                                                        <i class="bi bi-trash3 ms-1"></i>
                                                        حذف
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
                            {{ $products->links() }}
                        </div>
                    @endif
                </div>
            </main>
        </div>
    </div>

    <div class="modal fade" id="importProductsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('dashboard.products.import') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">استيراد الاصناف من Excel</h5>
                        <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label for="products-file" class="form-label">اختر الملف</label>
                        <input id="products-file" type="file" name="file" class="form-control" accept=".xlsx,.csv" required>
                        <small class="text-muted d-block mt-2">
                            الأعمدة المطلوبة: رقم المنتج + اسم الصنف + اسم الوحدة.<br>
                            ملفك الحالي مدعوم مباشرة بالأسماء: id + name + unit.
                        </small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">الغاء</button>
                        <button type="submit" class="btn btn-primary">استيراد</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade delete-product-modal" id="deleteProductModal" tabindex="-1" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="deleteProductForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="delete-modal-head text-center">
                        <div class="delete-modal-icon" aria-hidden="true">
                            <i class="bi bi-trash3"></i>
                        </div>
                        <h2 class="delete-modal-title" id="deleteProductModalLabel">حذف الصنف</h2>
                        <p class="delete-modal-sub">سيتم إزالة السجل نهائياً من النظام</p>
                    </div>
                    <div class="delete-modal-body text-center">
                        <p class="text-secondary mb-0 small">هل تريد المتابعة وحذف الصنف التالي؟</p>
                        <p class="delete-modal-name mb-0" id="deleteProductNameDisplay">—</p>
                    </div>
                    <div class="modal-footer flex-nowrap gap-2 justify-content-between">
                        <button type="button" class="btn btn-light border px-3 flex-grow-1" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg ms-1"></i>
                            الغاء
                        </button>
                        <button type="submit" class="btn btn-danger btn-delete-confirm px-3 flex-grow-1 text-white">
                            <i class="bi bi-check-lg ms-1"></i>
                            تأكيد الحذف
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editProductForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">تعديل بيانات الصنف</h5>
                        <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="product-name" class="form-label">الاسم</label>
                            <input id="product-name" type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="product-code" class="form-label">الكود</label>
                            <input id="product-code" type="text" name="code" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="product-unit-id" class="form-label">الوحدة</label>
                            <select id="product-unit-id" name="unit_uuid" class="form-select" required>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->uuid }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="product-description" class="form-label">الوصف</label>
                            <textarea id="product-description" name="description" rows="3" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">الغاء</button>
                        <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const editProductModal = document.getElementById('editProductModal');
        if (editProductModal) {
            editProductModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                if (!button) {
                    return;
                }

                const productId = button.getAttribute('data-id');
                const productName = button.getAttribute('data-name') ?? '';
                const productCode = button.getAttribute('data-code') ?? '';
                const unitUuid = button.getAttribute('data-unit-uuid') ?? '';
                const description = button.getAttribute('data-description') ?? '';

                const form = document.getElementById('editProductForm');
                form.action = `/dashboard/products/${productId}`;

                document.getElementById('product-name').value = productName;
                document.getElementById('product-code').value = productCode;
                document.getElementById('product-description').value = description;

                const unitSelect = document.getElementById('product-unit-id');
                if (unitSelect && unitUuid !== '') {
                    unitSelect.value = unitUuid;
                }
            });
        }

        const deleteProductModal = document.getElementById('deleteProductModal');
        if (deleteProductModal) {
            deleteProductModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                if (!button) {
                    return;
                }

                const url = button.getAttribute('data-delete-url');
                const name = button.getAttribute('data-product-name') ?? '—';

                const form = document.getElementById('deleteProductForm');
                if (url) {
                    form.action = url;
                }

                const nameEl = document.getElementById('deleteProductNameDisplay');
                if (nameEl) {
                    nameEl.textContent = name;
                }
            });
        }
    </script>
</body>
</html>
