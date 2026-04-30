<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تسجيل الدخول — إدارة المخازن</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --login-accent: #38bdf8;
            --login-accent-deep: #0ea5e9;
            --login-slate-900: #0f172a;
            --login-slate-800: #1e293b;
        }

        body {
            font-family: 'Cairo', system-ui, sans-serif;
            min-height: 100vh;
            margin: 0;
            background: var(--login-slate-900);
        }

        .login-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            background:
                radial-gradient(ellipse 80% 50% at 100% 0%, rgba(56, 189, 248, 0.18) 0%, transparent 50%),
                radial-gradient(ellipse 60% 40% at 0% 100%, rgba(99, 102, 241, 0.15) 0%, transparent 45%),
                linear-gradient(160deg, var(--login-slate-900) 0%, #0c1222 50%, var(--login-slate-800) 100%);
        }

        .login-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.9;
            pointer-events: none;
        }

        .site-main-header {
            position: fixed;
            top: 0;
            inset-inline: 0;
            height: 72px;
            z-index: 1050;
            background: rgba(15, 23, 42, 0.88);
            border-bottom: 1px solid rgba(148, 163, 184, 0.15);
            backdrop-filter: blur(10px);
        }

        .site-main-title {
            color: #f8fafc;
            font-size: 1.2rem;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .login-wrap {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 5.5rem 1.25rem 2rem;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 2.25rem 2rem 2rem;
            border-radius: 1.25rem;
            background: rgba(30, 41, 59, 0.75);
            border: 1px solid rgba(148, 163, 184, 0.2);
            box-shadow:
                0 0 0 1px rgba(255, 255, 255, 0.05) inset,
                0 25px 50px -12px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(12px);
        }

        .login-icon-wrap {
            width: 64px;
            height: 64px;
            margin: 0 auto 1.25rem;
            border-radius: 1rem;
            background: linear-gradient(135deg, var(--login-accent) 0%, var(--login-accent-deep) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.75rem;
            box-shadow: 0 10px 25px -5px rgba(14, 165, 233, 0.45);
        }

        .login-heading {
            color: #f1f5f9;
            font-weight: 800;
            font-size: 1.5rem;
            text-align: center;
            margin-bottom: 0.35rem;
        }

        .login-sub {
            color: #94a3b8;
            font-size: 0.95rem;
            text-align: center;
            margin-bottom: 1.75rem;
        }

        .form-label {
            color: #cbd5e1;
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 0.4rem;
        }

        .login-field {
            display: flex;
            align-items: stretch;
            gap: 0.5rem;
        }

        .login-field-icon {
            flex-shrink: 0;
            width: 2.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.6rem;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(148, 163, 184, 0.25);
            color: var(--login-accent);
            font-size: 1.1rem;
        }

        .login-field .form-control {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(148, 163, 184, 0.25);
            border-radius: 0.6rem;
            color: #e2e8f0;
            min-height: 2.75rem;
        }

        .login-field .form-control::placeholder {
            color: #64748b;
        }

        .login-field:focus-within .login-field-icon {
            border-color: rgba(56, 189, 248, 0.5);
        }

        .login-field .form-control:focus {
            background: rgba(15, 23, 42, 0.85);
            border-color: rgba(56, 189, 248, 0.5);
            box-shadow: 0 0 0 0.2rem rgba(56, 189, 248, 0.15);
            color: #f8fafc;
        }

        .btn-login {
            --bs-btn-color: #fff;
            --bs-btn-bg: linear-gradient(135deg, var(--login-accent) 0%, var(--login-accent-deep) 100%);
            background: linear-gradient(135deg, var(--login-accent) 0%, var(--login-accent-deep) 100%);
            border: none;
            font-weight: 700;
            padding: 0.7rem 1rem;
            border-radius: 0.6rem;
            box-shadow: 0 10px 20px -8px rgba(14, 165, 233, 0.6);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #7dd3fc 0%, var(--login-accent) 100%);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 14px 24px -8px rgba(14, 165, 233, 0.7);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:focus-visible {
            outline: 2px solid rgba(125, 211, 252, 0.9);
            outline-offset: 2px;
        }

        .login-alert {
            background: rgba(127, 29, 29, 0.35);
            border: 1px solid rgba(248, 113, 113, 0.4);
            color: #fecaca;
            font-size: 0.9rem;
            border-radius: 0.5rem;
        }
    </style>
</head>

<body>
    <div class="login-bg" aria-hidden="true"></div>
    @include('partials.site-header')

    <div class="login-wrap">
        <form method="POST" action="{{ route('login.attempt') }}" class="login-card" autocomplete="on">
            @csrf

            <div class="login-icon-wrap" aria-hidden="true">
                <i class="bi bi-shield-lock"></i>
            </div>
            <h1 class="login-heading">مرحباً بك</h1>
            <p class="login-sub">سجّل بياناتك للوصول إلى لوحة إدارة المخازن</p>

            <div class="mb-3">
                <label for="email" class="form-label">البريد الإلكتروني</label>
                <div class="login-field">
                    <span class="login-field-icon" aria-hidden="true"><i class="bi bi-envelope"></i></span>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="form-control flex-grow-1"
                        placeholder="you@example.com"
                        required
                    >
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">كلمة المرور</label>
                <div class="login-field">
                    <span class="login-field-icon" aria-hidden="true"><i class="bi bi-key"></i></span>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control flex-grow-1"
                        placeholder="••••••••"
                        required
                    >
                </div>
            </div>

            @error('email')
                <div class="alert login-alert py-2 mb-3" role="alert">
                    <i class="bi bi-exclamation-circle ms-1"></i>
                    {{ $message }}
                </div>
            @enderror

            <button type="submit" class="btn w-100 btn-login text-white">
                <i class="bi bi-box-arrow-in-left ms-1"></i>
                تسجيل الدخول
            </button>
        </form>
    </div>
</body>
</html>
