<aside class="sidebar">
    <h2 class="h5 sidebar-title">القائمة الرئيسية</h2>
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
            <a class="nav-link {{ request()->routeIs('dashboard.users') ? 'active' : '' }}" href="{{ route('dashboard.users') }}">
                <i class="bi bi-people"></i>
                 المستخدمين
            </a>

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
