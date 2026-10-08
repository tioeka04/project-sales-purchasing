<div class="col-md-3 left_col">
    <div class="left_col scroll-view">
        <div class="navbar nav_title" style="border: 0;">
            <a href="{{ route('admin.dashboard') }}" class="site_title">
                <i class="fa fa-shopping-cart"></i> <span>{{ config('app.name') }}</span>
            </a>
        </div>
        <div class="clearfix"></div>

        <div class="profile clearfix">
            <div class="profile_info">
                <span>{{ __('Welcome') }},</span>
                <h2>{{ auth()->user()->name }}</h2>
            </div>
        </div>
        <br>

        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
            <div class="menu_section">
                <h3>{{ __('General') }}</h3>
                <ul class="nav side-menu">
                    <li @class(['active' => request()->routeIs('admin.dashboard')])>
                        <a href="{{ route('admin.dashboard') }}"><i class="fa fa-home"></i> {{ __('Dashboard') }}</a>
                    </li>
                </ul>
            </div>

            @canany(['product.view', 'supplier.view', 'customer.view'])
                <div class="menu_section">
                    <h3>{{ __('Master Data') }}</h3>
                    <ul class="nav side-menu">
                        @can('product.view')
                            <li @class(['active' => request()->routeIs('admin.master.products.*')])>
                                <a href="{{ route('admin.master.products.index') }}"><i class="fa fa-cubes"></i> {{ __('Products') }}</a>
                            </li>
                        @endcan
                        @can('supplier.view')
                            <li @class(['active' => request()->routeIs('admin.master.suppliers.*')])>
                                <a href="{{ route('admin.master.suppliers.index') }}"><i class="fa fa-truck"></i> {{ __('Suppliers') }}</a>
                            </li>
                        @endcan
                        @can('customer.view')
                            <li @class(['active' => request()->routeIs('admin.master.customers.*')])>
                                <a href="{{ route('admin.master.customers.index') }}"><i class="fa fa-users"></i> {{ __('Customers') }}</a>
                            </li>
                        @endcan
                    </ul>
                </div>
            @endcanany

            @can('purchase-order.view')
                <div class="menu_section">
                    <h3>{{ __('Purchasing') }}</h3>
                    <ul class="nav side-menu">
                        <li @class(['active' => request()->routeIs('admin.purchasing.purchase-orders.*')])>
                            <a href="{{ route('admin.purchasing.purchase-orders.index') }}"><i class="fa fa-shopping-cart"></i> {{ __('Purchase Orders') }}</a>
                        </li>
                    </ul>
                </div>
            @endcan

            @can('sales-order.view')
                <div class="menu_section">
                    <h3>{{ __('Sales') }}</h3>
                    <ul class="nav side-menu">
                        <li @class(['active' => request()->routeIs('admin.sales.sales-orders.*')])>
                            <a href="{{ route('admin.sales.sales-orders.index') }}"><i class="fa fa-money"></i> {{ __('Sales Orders') }}</a>
                        </li>
                    </ul>
                </div>
            @endcan

            @canany(['user.manage', 'role.manage'])
                <div class="menu_section">
                    <h3>{{ __('Settings') }}</h3>
                    <ul class="nav side-menu">
                        @can('user.manage')
                            <li @class(['active' => request()->routeIs('admin.settings.users.*')])>
                                <a href="{{ route('admin.settings.users.index') }}"><i class="fa fa-user"></i> {{ __('Users') }}</a>
                            </li>
                        @endcan
                        @can('role.manage')
                            <li @class(['active' => request()->routeIs('admin.settings.roles.*')])>
                                <a href="{{ route('admin.settings.roles.index') }}"><i class="fa fa-lock"></i> {{ __('Roles & Permissions') }}</a>
                            </li>
                        @endcan
                    </ul>
                </div>
            @endcanany
        </div>
    </div>
</div>
