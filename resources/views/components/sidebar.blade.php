<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link">
                <i class="icon-grid menu-icon"></i>
                <span class="menu-title">{{ Auth::user()->name }}</span>
            </a>
        </li>
        <li class="nav-item {{ Request::is('dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <i class="icon-grid menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item {{ Request::is('posts*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('posts') }}">
                <i class="icon-folder menu-icon"></i>
                <span class="menu-title">Posts</span>
            </a>
        </li>
        @if (auth()->user()->hasRole('Admin'))
            <li class="nav-item {{ Request::is('settings/*') ? 'active' : '' }}">
                <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false"
                    aria-controls="ui-basic">
                    <i class="icon-layout menu-icon"></i>
                    <span class="menu-title">Menu Settings</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-basic">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item {{ Request::is('settings/permissions*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('permissions') }}">Permissions</a>
                        </li>
                        <li class="nav-item {{ Request::is('settings/roles*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('roles') }}">Roles</a>
                        </li>
                        <li class="nav-item {{ Request::is('settings/users*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('users') }}">Users</a>
                        </li>
                    </ul>
                </div>
            </li>
        @endif
    </ul>
</nav>
