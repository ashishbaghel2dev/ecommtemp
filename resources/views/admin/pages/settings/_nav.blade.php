<div class="settings-tabs" aria-label="Settings sections">
    <a href="{{ route('settings.general') }}" class="{{ request()->routeIs('settings.general') ? 'active' : '' }}">
        <i class="ti ti-cube"></i>
        General
    </a>
    <a href="{{ route('settings.theme') }}" class="{{ request()->routeIs('settings.theme') ? 'active' : '' }}">
        <i class="ti ti-palette"></i>
        Theme
    </a>
    <a href="{{ route('settings.costs') }}" class="{{ request()->routeIs('settings.costs') ? 'active' : '' }}">
        <i class="ti ti-receipt-tax"></i>
        Charges
    </a>
    <a href="{{ route('settings.search') }}" class="{{ request()->routeIs('settings.search') ? 'active' : '' }}">
        <i class="ti ti-search"></i>
        Search Console
    </a>
    <a href="{{ route('settings.seo') }}" class="{{ request()->routeIs('settings.seo') ? 'active' : '' }}">
        <i class="ti ti-world-search"></i>
        SEO Pages
    </a>
</div>
