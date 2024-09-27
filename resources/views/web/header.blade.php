<a href="{{ route('web.home') }}" class="navbar-brand offset-xl-1 offset-0">
    <img class="navbar-img" src="{{ isset($settings) ? asset($settings['logo']) : asset(getSettingValue('logo')) }}"
         alt="{{ __('messages.logo_not_found') }}"/>
</a>
<button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <i class="fas fa-bars"></i>
</button>
<div class="navbar-collapse collapse" id="navbarSupportedContent">
    <ul class="navbar-nav navbar-right ml-auto align-items-center">
        <li class="nav-item {{ Request::is('/', 'categories-list*') ? 'active' : '' }}">
            <a href="{{ route('web.home') }}" class="nav-link">
                <i class="fas fa-home pr-1"></i>
                <span>{{ __('messages.common.home') }}</span>
            </a>
        </li>
        <li class="nav-item {{ Request::is('tickets*', 'ticket/*', 'ticket*') ? 'active' : '' }} ">
            <a href="{{ route('public.tickets') }}" class="nav-link">
                <i class="fas fa-ticket-alt pr-1"></i>
                <span>{{ __('messages.ticket.public_tickets') }}</span>
            </a>
        </li>
        <li class="nav-item {{ Request::is('submit-ticket*') ? 'active' : '' }}">
            <a href="{{ route('web.submit_ticket') }}" class="nav-link">
                <i class="fas fa-plus-square pr-1"></i>
                <span>{{ __('messages.ticket.submit_ticket') }}</span>
            </a>
        </li>
        <li class="nav-item {{ Request::is('search-ticket*', 'get-ticket*') ? 'active' : '' }}">
            <a href="{{ route('web.search_ticket_form') }}" class="nav-link">
                <i class="fas fa-search pr-1"></i>
                <span>{{ __('messages.ticket.search_ticket') }}</span>
            </a>
        </li>
        <li class="nav-item {{ Request::is('faqs*') ? 'active' : '' }} ">
            <a href="{{ route('web.faqs') }}" class="nav-link ">
                <i class="fas fa-question-circle pr-1"></i>
                <span>{{ __('messages.faq.faqs') }}</span>
            </a>
        </li>
        @if(Auth::user())
            <li class="dropdown">
                <a href="#" data-toggle="dropdown"
                   class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                    <img src="{{ getLoggedInUser()->photo_url }}"
                         class="rounded-circle mr-1 thumbnail-rounded user-thumbnail ">
                    <div class="d-sm-none d-lg-inline-block">
                        {{ __('messages.common.hi') }}, {{ getLoggedInUser()->name}}</div>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    @role('Admin')
                    <a href="{{ route('admin.dashboard') }}" class="dropdown-item has-icon text-primary">
                        <i class="fas fa fa-digital-tachograph"></i>{{ __('messages.dashboard') }}
                    </a>
                    @endrole
                    @role('Agent')
                    <a href="{{ route('agent.dashboard') }}" class="dropdown-item has-icon text-primary">
                        <i class="fas fa fa-digital-tachograph"></i>{{ __('messages.dashboard') }}
                    </a>
                    @endrole
                    @role('Customer')
                    <a href="{{ route('customer.dashboard') }}" class="dropdown-item has-icon text-primary">
                        <i class="fas fa fa-digital-tachograph"></i>{{ __('messages.dashboard') }}
                    </a>
                    @endrole
                    <a href="{{ url('logout') }}" class="dropdown-item has-icon text-danger"
                       onclick="event.preventDefault(); localStorage.clear();  document.getElementById('logout-form').submit();">
                        <i class="fa fa-sign-out-alt"></i>{{ __('messages.user.logout') }}
                    </a>
                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" class="d-none">
                        {{ csrf_field() }}
                    </form>
                </div>
            </li>
        @else
            <li class="nav-item {{ Request::is('register*') ? 'active' : '' }}">
                <a href="{{ route('register') }}" class="nav-link">
                    <i class="fas fa-user-plus pr-1"></i>
                    <span>{{ __('messages.common.register') }}</span>
                </a>
            </li>
            <li class="nav-item {{ Request::is('login*') ? 'active' : '' }}">
                <a href="{{ route('login') }}" class="nav-link">
                    <i class="fas fa-sign-in-alt pr-1"></i>
                    <span>{{ __('messages.common.login') }}</span>
                </a>
            </li>
        @endif
    </ul>
</div>

