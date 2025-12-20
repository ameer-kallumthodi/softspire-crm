<aside class="left-sidebar" data-sidebarbg="skin6">
    <div class="scroll-sidebar" data-sidebarbg="skin6">
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <li class="sidebar-item">
                    <a class="sidebar-link sidebar-link" href="{{ route('admin.dashboard') }}" aria-expanded="false">
                        <i data-feather="home" class="feather-icon"></i>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>
                <li class="list-divider"></li>
                <li class="nav-small-cap"><span class="hide-menu">CRM</span></li>
                <li class="sidebar-item">
                    <a class="sidebar-link sidebar-link" href="{{ route('admin.leads.index') }}" aria-expanded="false">
                        <i data-feather="users" class="feather-icon"></i>
                        <span class="hide-menu">Leads</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link sidebar-link" href="{{ route('admin.customers.index') }}" aria-expanded="false">
                        <i data-feather="user-check" class="feather-icon"></i>
                        <span class="hide-menu">Customers</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <i data-feather="credit-card" class="feather-icon"></i>
                        <span class="hide-menu">Payments</span>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level">
                        <li class="sidebar-item">
                            <a href="{{ route('admin.payments.index') }}" class="sidebar-link">
                                <i class="mdi mdi-cash-multiple"></i>
                                <span class="hide-menu">All Payments</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('admin.payments.pending-quotations') }}" class="sidebar-link">
                                <i class="mdi mdi-clock-alert"></i>
                                <span class="hide-menu">Pending Quotations</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="list-divider"></li>
                <li class="nav-small-cap"><span class="hide-menu">Master Data</span></li>
                <li class="sidebar-item">
                    <a class="sidebar-link sidebar-link" href="{{ route('admin.countries.index') }}" aria-expanded="false">
                        <i data-feather="globe" class="feather-icon"></i>
                        <span class="hide-menu">Countries</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link sidebar-link" href="{{ route('admin.purposes.index') }}" aria-expanded="false">
                        <i data-feather="target" class="feather-icon"></i>
                        <span class="hide-menu">Purposes</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link sidebar-link" href="{{ route('admin.lead-statuses.index') }}" aria-expanded="false">
                        <i data-feather="check-circle" class="feather-icon"></i>
                        <span class="hide-menu">Lead Statuses</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link sidebar-link" href="{{ route('admin.lead-sources.index') }}" aria-expanded="false">
                        <i data-feather="link" class="feather-icon"></i>
                        <span class="hide-menu">Lead Sources</span>
                    </a>
                </li>
                <li class="list-divider"></li>
                <li class="nav-small-cap"><span class="hide-menu">User Management</span></li>
                <li class="sidebar-item">
                    <a class="sidebar-link sidebar-link" href="{{ route('admin.telecallers.index') }}" aria-expanded="false">
                        <i data-feather="phone" class="feather-icon"></i>
                        <span class="hide-menu">Telecallers</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link sidebar-link" href="{{ route('admin.managers.index') }}" aria-expanded="false">
                        <i data-feather="user-check" class="feather-icon"></i>
                        <span class="hide-menu">Managers</span>
                    </a>
                </li>
                <li class="list-divider"></li>
                <li class="nav-small-cap"><span class="hide-menu">Settings</span></li>
                <li class="sidebar-item">
                    <a class="sidebar-link sidebar-link" href="{{ route('admin.settings.index') }}" aria-expanded="false">
                        <i data-feather="settings" class="feather-icon"></i>
                        <span class="hide-menu">Settings</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
