<aside id="sidebar-wrapper">
    <div class="sidebar-brand">
        <img src="{{ asset($settings['logo']) }}" width="70px"
             class="navbar-brand-full" alt=""/>&nbsp;&nbsp;
        <a href="{{ url('/') }}">{{ $settings['application_name'] }}</a>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
        <a href="{{ url('/') }}" class="small-sidebar-text">
            <img class="navbar-brand-full" src="{{ asset('assets/img/infyom-logo.png') }}"
                 alt="{{config('app.name')}}"/>
        </a>
    </div>
    <ul class="sidebar-menu mt-3">
        <li class="{{ Request::is('customer/dashboard*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('customer.dashboard') }}"><i class="fas fa fa-digital-tachograph"></i>
                <span>{{ __('messages.dashboard') }}</span></a>
        </li>
        <li class="{{ Request::is('customer/ticket*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('customer.myTicket') }}"><i class="fas fa-ticket-alt"></i>
                <span>{{ __('messages.my_tickets') }}</span></a>
        </li>
    </ul>
</aside>
