<header class="site-main-header">
    <div class="container-fluid px-3 px-md-4 h-100">
        <div class="d-flex align-items-center justify-content-between h-100 w-100 gap-2">
            @auth
                <button type="button" class="btn btn-outline-light btn-sm d-lg-none flex-shrink-0 px-2" id="dashboardSidebarOpenBtn" aria-controls="dashboardSidebarNav" aria-expanded="false" aria-label="فتح القائمة">
                    <i class="bi bi-list fs-5" aria-hidden="true"></i>
                </button>
            @endauth
            <div class="d-flex align-items-center gap-2 flex-wrap flex-grow-1 min-w-0">
                <h1 class="site-main-title mb-0 text-truncate">ادارة المخازن</h1>
                @auth
                    @php($u = auth()->user())
                    @if($u->isSuperAdmin())
                        <span class="badge rounded-pill text-bg-light text-dark fw-semibold small px-3 py-2 border border-secondary border-opacity-25">كل الفروع</span>
                    @else
                        <span class="badge rounded-pill text-bg-light text-dark fw-semibold small px-3 py-2 border border-secondary border-opacity-25">{{ trim((string) $u->type_location) !== '' ? $u->type_location : 'لم يُحدَّد فرع' }}</span>
                    @endif
                @endauth
            </div>
            @auth
                <div class="d-flex align-items-center gap-2">
                    <small class="text-light-emphasis mb-0 d-none d-md-inline">
                        {{ auth()->user()->name }}
                    </small>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
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
