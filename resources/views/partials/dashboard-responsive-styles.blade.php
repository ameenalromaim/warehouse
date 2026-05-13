<style>
    @media (max-width: 991.98px) {
        body.dashboard-sidebar-open {
            overflow: hidden;
        }

        .app-main {
            width: 100%;
            min-width: 0;
            padding: 0.75rem;
        }

        .sidebar-backdrop {
            position: fixed;
            inset: 72px 0 0 0;
            background: rgba(15, 23, 42, 0.55);
            z-index: 1038;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, visibility 0.2s ease;
        }

        body.dashboard-sidebar-open .sidebar-backdrop {
            opacity: 1;
            visibility: visible;
        }

        .sidebar {
            position: fixed;
            top: 72px;
            bottom: 0;
            /* إحداثيات فيزيائية: القائمة على يمين الشاشة (تطبيق عربي RTL) */
            right: 0;
            left: auto;
            width: min(280px, 88vw);
            max-height: none;
            height: auto;
            z-index: 1040;
            box-shadow: none;
            transform: translateX(calc(100% + 24px));
            transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.28s ease;
            border-inline-start: 1px solid rgba(148, 163, 184, 0.25);
            -webkit-overflow-scrolling: touch;
        }

        body.dashboard-sidebar-open .sidebar {
            transform: translateX(0);
            box-shadow: -12px 0 40px rgba(0, 0, 0, 0.35);
        }

        .sidebar .sidebar-mobile-bar {
            position: relative;
            z-index: 5;
        }

        .sidebar #dashboardSidebarCloseBtn {
            z-index: 6;
            pointer-events: auto;
        }

        .form-card {
            max-width: 100% !important;
        }

        .topbar.p-3 {
            padding: 0.75rem !important;
        }

        .site-main-title {
            font-size: clamp(0.95rem, 4vw, 1.15rem);
        }

        .actions-cell .btn {
            min-width: 0 !important;
        }

        .actions-cell {
            flex-direction: column;
            align-items: stretch;
        }

        .pagination {
            flex-wrap: wrap;
            justify-content: center;
            row-gap: 0.35rem;
        }

        .filter-input {
            min-width: 0;
            width: 100%;
            max-width: 100%;
        }

        #purchaseTable thead th {
            white-space: normal;
            vertical-align: middle;
        }
    }

    @media (min-width: 992px) {
        .sidebar-backdrop {
            display: none !important;
        }
    }

    @media (max-width: 575.98px) {
        .table-card .table thead th {
            white-space: normal;
            font-size: 0.8rem;
        }

        .modal-footer.flex-nowrap {
            flex-wrap: wrap !important;
        }
    }
</style>
