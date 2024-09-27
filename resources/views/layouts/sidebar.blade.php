<aside id="sidebar-wrapper">
    <div class="sidebar-brand h-auto lh-unset mt-4 custom-flex">
        <img src="{{ asset($settings['logo']) }}" width="70px" class="navbar-brand-full" alt=""/>&nbsp;&nbsp;
        <a href="{{ url('/') }}" class="pl-2 text-wrap text-break w-75">{{ $settings['application_name'] }}</a>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
        <div class="d-flex justify-content-center align-items-center h-100 w-100">
            <a href="{{ url('/') }}" class="small-sidebar-text">
                <img class="navbar-brand-full" src="{{ asset('assets/img/infyom-logo.png') }}"
                     alt="{{config('app.name')}}"/>
            </a>
        </div>
    </div>
    <ul class="sidebar-menu mt-3">
        @role('Agent')
        <li class="{{ Request::is('agent/dashboard*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('agent.dashboard') }}"><i class="fas fa fa-digital-tachograph"></i>
                <span>{{ __('messages.dashboard') }}</span></a>
        </li>
        @endrole
        @role('Admin')
        <li class="{{ Request::is('admin/dashboard*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="fas fa fa-digital-tachograph"></i>
                <span>{{ __('messages.dashboard') }}</span></a>
        </li>
        <li class="{{ (Request::is('admin/admins*')) ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admins.index') }}">
                <i class="fas fa-user-tie"></i> <span>{{ __('messages.admins') }}</span>
            </a>
        </li>

        <li class="{{ Request::is('admin/agents*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('agent.index') }}"><i class="fas fa-user"></i>
                <span>{{ __('messages.agent.agents') }}</span></a>
        </li>

        <li class="{{ Request::is('admin/customers*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('customer.index') }}"><i class="fas fa-user-tag"></i>
                <span>{{ __('messages.customer.customers') }}</span></a>
        </li>

        <li class="{{ Request::is('admin/categories*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('category.index') }}"><i class="fas fa-th-list"></i>
                <span>{{ __('messages.category.categories') }}</span></a>
        </li>

        <li class="{{ Request::is('admin/tickets*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('ticket.index') }}"><i class="fas fa-ticket-alt"></i>
                <span>{{ __('messages.ticket.tickets') }}</span></a>
        </li>
        @endrole

        @role('Agent')
        <li class="{{ Request::is('agent/tickets*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('agent.ticket.index') }}"><i class="fas fa-ticket-alt"></i>
                <span>{{ __('messages.ticket.tickets') }}</span></a>
        </li>
        @endrole
        <li class="{{ Request::is('conversations*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('conversations') }}"><i class="far fa-comments"></i>
                <span>{{ __('messages.conversations') }}</span>
                <span class="badge badge-pill badge-light conversation-badge" id="sidebar-message-count">{{ getConversationCount() }}</span>
            </a>
        </li>
        @role('Admin')
        <li class="side-menus {{ Request::is('admin/faqs*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('faqs.index') }}">
                <i class="fas fa-question-circle"></i>
                <span> {{ __('messages.faq.faqs') }}</span>
            </a>
        </li>
        <li class="side-menus {{ Request::is('admin/translation-manager*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('translation-manager.index') }}">
                <i class="fas fa-language"></i>
                <span>{{ __('messages.placeholder.translation') }}</span>
            </a>
        </li>
        <li class="side-menus {{ Request::is('admin/settings*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('settings.index') }}">
                <i class="fas fa-cog"></i>
                <span>{{ __('messages.settings') }}</span>
            </a>
        </li>
        @endrole
    </ul>
</aside>
