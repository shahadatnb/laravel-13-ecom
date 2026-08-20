<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
        <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="https://ui-avatars.com/api/?name=Admin&background=3c8dbc&color=fff&size=80" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="{{ route('admin.profile') }}" class="d-block">{{ Auth::user()->name }}</a>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('admin.orders*') ? 'menu-open' : '' }}">
                    <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Orders</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('admin.inventory*','admin.stock*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('admin.inventory*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-warehouse"></i>
                        <p>
                            Inventory
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.stock.index') }}" class="nav-link {{ request()->routeIs('admin.stock.index') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-cubes"></i>
                                <p>Stock Management</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.inventory.index') }}" class="nav-link {{ request()->routeIs('admin.inventory.index') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-cubes"></i>
                                <p>Stock Levels</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.inventory.warehouses') }}" class="nav-link {{ request()->routeIs('admin.inventory.warehouses*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-building"></i>
                                <p>Warehouses</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.inventory.transfers') }}" class="nav-link {{ request()->routeIs('admin.inventory.transfers*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-exchange-alt"></i>
                                <p>Transfers</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.inventory.ledger') }}" class="nav-link {{ request()->routeIs('admin.inventory.ledger') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p>Ledger</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item {{ request()->routeIs('admin.coupons*') ? 'menu-open' : '' }}">
                    <a href="{{ route('admin.coupons.index') }}" class="nav-link {{ request()->routeIs('admin.coupons*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-ticket-alt"></i>
                        <p>Coupons</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('admin.delivery-zones*') ? 'menu-open' : '' }}">
                    <a href="{{ route('admin.delivery-zones.index') }}" class="nav-link {{ request()->routeIs('admin.delivery-zones*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-truck"></i>
                        <p>Delivery Zones</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('admin.product.*', 'admin.category.*', 'admin.brand.*', 'admin.product-attribute.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('admin.product.*', 'admin.category.*', 'admin.brand.*', 'admin.product-attribute.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>
                            Products
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.product.index') }}" class="nav-link {{ request()->routeIs('admin.product.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-cube"></i>
                                <p>Products</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.category.index') }}" class="nav-link {{ request()->routeIs('admin.category.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-list"></i>
                                <p>Categories</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.brand.index') }}" class="nav-link {{ request()->routeIs('admin.brand.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tags"></i>
                                <p>Brands</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.attribute.index') }}" class="nav-link {{ request()->routeIs('admin.product-attribute.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-list-alt"></i>
                                <p>Attributes</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.product.import-export') }}" class="nav-link {{ request()->routeIs('admin.product.import-export') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-file-import"></i>
                                <p>Import / Export</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item {{ request()->routeIs('admin.customers*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('admin.customers*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-tie"></i>
                        <p>
                            Customers
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.customers.index') }}" class="nav-link {{ request()->routeIs('admin.customers.index') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-list"></i>
                                <p>All Customers</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.customers.create') }}" class="nav-link {{ request()->routeIs('admin.customers.create') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-plus-circle"></i>
                                <p>Add Customer</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item {{ request()->routeIs('admin.media*') ? 'menu-open' : '' }}">
                    <a href="{{ route('admin.media.index') }}" class="nav-link {{ request()->routeIs('admin.media*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-photo-video"></i>
                        <p>Media Library</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('admin.pages*') ? 'menu-open' : '' }}">
                    <a href="{{ route('admin.pages.index') }}" class="nav-link {{ request()->routeIs('admin.pages*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>Pages</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.profile') }}" class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user"></i>
                        <p>My Profile</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('admin.settings.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>
                            Settings
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.settings.hero-slides') }}" class="nav-link {{ request()->routeIs('admin.settings.hero-slides*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-images"></i>
                                <p>Hero Slides</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.settings.site-settings') }}" class="nav-link {{ request()->routeIs('admin.settings.site-settings*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-globe"></i>
                                <p>Site Settings</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.settings.roles.index') }}" class="nav-link {{ request()->routeIs('admin.settings.roles*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-shield-alt"></i>
                                <p>Roles & Permissions</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.settings.permissions.index') }}" class="nav-link {{ request()->routeIs('admin.settings.permissions*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-key"></i>
                                <p>Permissions</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.settings.users.index') }}" class="nav-link {{ request()->routeIs('admin.settings.users*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Users</p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</aside>
