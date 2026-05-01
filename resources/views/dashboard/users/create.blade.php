<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>إضافة مستخدم جديد</title>
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

        .form-card {
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            max-width: 520px;
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
    </style>
</head>
<body>
    @include('partials.site-header')
    <div class="app-shell">
        @include('partials.dashboard-sidebar')

        <div class="app-main">
            <header class="topbar p-3">
                <h1 class="h4 topbar-title d-flex align-items-center gap-2">
                    <i class="bi bi-person-plus text-info"></i>
                    إضافة مستخدم جديد
                </h1>
            </header>

            <main>
                @if($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="card form-card border-0">
                    <div class="card-body bg-white p-4">
                        <p class="text-muted small mb-4">أدخل بيانات المستخدم للسماح له بتسجيل الدخول رقم الهاتف وكلمة المرور.</p>
                        <form method="POST" action="{{ route('dashboard.users.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">الاسم</label>
                                <input id="name" type="text" name="name" class="form-control form-control-lg" value="{{ old('name') }}" required maxlength="255">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">البريد</label>
                                <input id="email" type="email" name="email" class="form-control form-control-lg" value="{{ old('email') }}" required maxlength="255">
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label fw-semibold">الهاتف</label>
                                <input id="phone" type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" maxlength="32" required >
                                @error('phone')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="type_location" class="form-label fw-semibold">الموقع / الفرع</label>
                                <select id="type_location" name="type_location" class="form-select form-select-lg" required>
                                    <option value="" @selected(old('type_location', '') === '')>اختر الموقع</option>
                                    @foreach($locationOptions as $loc)
                                        <option value="{{ $loc }}" @selected(old('type_location') === $loc)>{{ $loc }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">كلمة المرور</label>
                                <input id="password" type="password" name="password" class="form-control form-control-lg" required minlength="8" autocomplete="new-password">
                            </div>
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label fw-semibold">تأكيد كلمة المرور</label>
                                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control form-control-lg" required minlength="8" autocomplete="new-password">
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-p-accent px-4">
                                    <i class="bi bi-check-lg ms-1"></i>
                                    حفظ المستخدم
                                </button>
                                <a href="{{ route('dashboard.users') }}" class="btn btn-outline-secondary">العودة لعرض بيانات المستخدمين</a>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
