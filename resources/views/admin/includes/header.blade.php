<header class="dashboard-header-box">

    <div class="dashboard-left">


    </div>
    <div class="dashboard-right">
        <div class="header-icon-right">
            <a href="/">
                <div class="header-icon-item">
                    <i class="ti ti-home"></i>
                </div>
            </a>
            <div class="header-icon-item">
                <i class="ti ti-search"></i>
            </div>
            <div class="header-icon-item notification-wrapper" id="notifWrapper">
                <i class="ti ti-bell"></i>

                <span class="notif-badge">3</span>

                <div class="notif-dropdown" id="notifDropdown">
                    <div class="notify-item-head">Notifications</div>
                    <div class="notify-box">
                        <div class="notif-item">Payment successful</div>
                        <div class="notif-item">New user registered</div>
                        <div class="notif-item">Payment successful</div>
                        <div class="notif-item">New user registered</div>
                        <div class="notif-item">Payment successful</div>
                        <div class="notif-item">New user registered</div>
                        <div class="notif-item">Payment successful</div>
                        <div class="notif-item">New user registered</div>
                        <div class="notif-item">Payment successful</div>
                        <div class="notif-item">New user registered</div>
                        <div class="notif-item">Payment successful</div>
                        <div class="notif-item">New user registered</div>
                        <div class="notif-item">Payment successful</div>
                        <div class="notif-item">New user registered</div>
                        <div class="notif-item">Payment successful</div>
                        <div class="notif-item">New user registered</div>
                        <div class="notif-item">Payment successful</div>
                        <div class="notif-item">New user registered</div>
                    </div>
                </div>
            </div>

            <div class="profile-dropdown">
                <button class="profile-btn" id="profileBtn">
                    {{ Auth::user()->name[0] }}
                </button>
                <div class="profile-menu" id="profileMenu">
                    <div class="profile-version">
                        Hi, {{ Auth::user()->name }}
                    </div>
                    <a href="#"><i class="ti ti-user"></i> My Account</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"><i class="ti ti-power"></i> Logout</button>
                    </form>
                </div>

            </div>
        </div>


    </div>

</header>