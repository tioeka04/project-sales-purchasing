<div class="top_nav">
    <div class="nav_menu">
        <nav>
            <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
            </div>
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn btn-link" style="padding: 15px; color: #5A738E;">
                            <i class="fa fa-sign-out"></i> {{ __('Log Out') }} ({{ auth()->user()->name }})
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</div>
