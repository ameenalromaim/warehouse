<header class="site-main-header">
    <div class="container-fluid px-3 px-md-4 h-100">
        <div class="d-flex align-items-center justify-content-between h-100 w-100">
            <h1 class="site-main-title mb-0">ادارة المخازن</h1>
            @auth
                <div class="d-flex align-items-center gap-2">
                    <small class="text-light-emphasis mb-0 d-none d-md-inline">
                        {{ auth()->user()->name }}
                    </small>
                    <form method="POST" action="{{ url('/logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm px-3">
                            <i class="bi bi-box-arrow-right ms-1"></i>
                            تسجيل الخروج
                        </button>
                    </form>
                </div>
            @endauth
        </div>
    </div>
</header>
