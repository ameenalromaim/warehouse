<div class="sidebar-backdrop d-lg-none" id="dashboardSidebarBackdrop" aria-hidden="true"></div>
<aside class="sidebar" id="dashboardSidebarNav">
    <div class="sidebar-mobile-bar d-flex d-lg-none align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom border-secondary border-opacity-25">
        <span class="text-light small fw-semibold opacity-75">القائمة</span>
        <button type="button" class="btn btn-sm btn-outline-light position-relative" id="dashboardSidebarCloseBtn" aria-label="إغلاق القائمة">
            <i class="bi bi-x-lg" aria-hidden="true"></i>
        </button>
    </div>
    <h2 class="h5 sidebar-title d-none d-lg-block">القائمة الرئيسية</h2>
    <nav class="nav flex-column sidebar-nav">
        <a class="nav-link {{ request()->routeIs('dashboard.purchases') ? 'active' : '' }}" href="{{ route('dashboard.purchases') }}">
            <i class="bi bi-receipt-cutoff"></i>
            استلام مخزني 
        </a>
        <a class="nav-link {{ request()->routeIs('dashboard.suppliers') ? 'active' : '' }}" href="{{ route('dashboard.suppliers') }}">
            <i class="bi bi-truck"></i>
            الموردين
        </a>
        <a class="nav-link {{ request()->routeIs('dashboard.products') ? 'active' : '' }}" href="{{ route('dashboard.products') }}">
            <i class="bi bi-box-seam"></i>
            الاصناف
        </a>
        <a class="nav-link {{ request()->routeIs('dashboard.reports.returns.normal') ? 'active' : '' }}" href="{{ route('dashboard.reports.returns.normal') }}">
            <i class="bi bi-arrow-return-left"></i>
            تقرير المردود
        </a>
        <a class="nav-link {{ request()->routeIs('dashboard.reports.returns.damage') ? 'active' : '' }}" href="{{ route('dashboard.reports.returns.damage') }}">
            <i class="bi bi-exclamation-octagon"></i>
            تقرير التالف
        </a>
            @if(auth()->user()?->isSuperAdmin())
                <a class="nav-link {{ request()->routeIs('dashboard.users') ? 'active' : '' }}" href="{{ route('dashboard.users') }}">
                    <i class="bi bi-people"></i>
                    المستخدمين
                </a>
            @endif

        {{-- <div class="sidebar-section mt-3 pt-2 border-top border-secondary border-opacity-25">
            <div class="sidebar-section-title px-2 mb-2 small fw-semibold text-secondary">الفروع</div>
            <a class="nav-link {{ request()->routeIs('dashboard.branches.create') ? 'active' : '' }}" href="{{ route('dashboard.branches.create') }}">
                <i class="bi bi-building-add"></i>
                إضافة فرع جديد
            </a>
            <a class="nav-link {{ request()->routeIs('dashboard.branches') ? 'active' : '' }}" href="{{ route('dashboard.branches') }}">
                <i class="bi bi-building"></i>
                عرض بيانات الفروع
            </a>
        </div> --}}
    </nav>
</aside>
<script>
(function () {
    var body = document.body;

    function setOpen(open) {
        body.classList.toggle('dashboard-sidebar-open', open);
        var openBtn = document.getElementById('dashboardSidebarOpenBtn');
        if (openBtn) {
            openBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
        }
        var backdrop = document.getElementById('dashboardSidebarBackdrop');
        if (backdrop) {
            backdrop.setAttribute('aria-hidden', open ? 'false' : 'true');
        }
    }

    function closeSidebar() {
        setOpen(false);
    }

    function toggleSidebar() {
        setOpen(!body.classList.contains('dashboard-sidebar-open'));
    }

    function wireNavLinks() {
        document.querySelectorAll('#dashboardSidebarNav .sidebar-nav a.nav-link').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.matchMedia('(max-width: 991.98px)').matches) {
                    closeSidebar();
                }
            });
        });
    }

    function wireOpenAndBackdrop() {
        var openBtn = document.getElementById('dashboardSidebarOpenBtn');
        var backdrop = document.getElementById('dashboardSidebarBackdrop');
        if (openBtn) {
            openBtn.addEventListener('click', function (e) {
                e.preventDefault();
                toggleSidebar();
            });
        }
        if (backdrop) {
            backdrop.addEventListener('click', closeSidebar);
        }
    }

    document.addEventListener(
        'click',
        function (e) {
            if (!e.target || !e.target.closest) {
                return;
            }
            if (e.target.closest('#dashboardSidebarCloseBtn')) {
                e.preventDefault();
                e.stopPropagation();
                closeSidebar();
            }
        },
        true
    );

    function init() {
        wireOpenAndBackdrop();
        wireNavLinks();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    window.addEventListener('resize', function () {
        if (window.matchMedia('(min-width: 992px)').matches) {
            closeSidebar();
        }
    });
})();
</script>
