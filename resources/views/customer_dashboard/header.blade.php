<form class="form-inline mr-auto" action="#">
    <ul class="navbar-nav mr-3">
        <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
    </ul>
</form>
@php
    $notifications = getNotification();
@endphp
<ul class="navbar-nav navbar-right">
    <li class="dropdown dropdown-list-toggle">
        <a href="#" data-toggle="dropdown" title="{{ __('messages.notification.notifications') }}"
            class="nav-link notification-toggle nav-link-lg {{ count($notifications) > 0 ? 'beep' : '' }}">
            <i class="far fa-bell"></i>
        </a>
        <div class="dropdown-menu dropdown-list dropdown-menu-right" id="notification">
            <div class="dropdown-header"><span id="header-notification-counter" class="{{ count($notifications) == 0 ? 'd-none' : '' }}">{{ count($notifications) }}</span>{{ ' '.__('messages.notification.notifications') }}
                <div class="float-right">
                    @if(count($notifications) > 0)
                        <a href="#" id="readAllNotification" class="text-decoration-none2">{{ __('messages.notification.mark_all_as_read') }}
                    @endif
                </div>
            </div>
            <div class="dropdown-list-content dropdown-list-icons notification-content">
                @if(count($notifications) > 0)
                    @foreach($notifications as $notification)
                        <a href="#" data-id="{{ $notification->id }}" class="dropdown-item dropdown-item-unread readNotification" id="readNotification">
                            <div class="dropdown-item-icon bg-primary text-white">
                                <i class="{{ getNotificationIcon($notification->type) }}"></i>
                            </div>
                            <div class="dropdown-item-desc text-dark notification-title" style="width: 100%;">
                                {{ $notification->title }}
                                <div class="">
                                    <span class="notification-for-text text-gray">{{ $notification->description }}</span>
                                </div>
                                <div class="float-right">
                                    <small class="notification-time">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </a>
                    @endforeach
                @else
                    <div class="empty-state" data-height="250" style="height: 400px;">
                        <div class="empty-state-icon">
                            <i class="fas fa-question"></i>
                        </div>
                        <h2>{{ __('messages.notification.empty_notifications') }}</h2>
                    </div>
                @endif
                <div class="empty-state d-none" data-height="250" style="height: 400px;">
                    <div class="empty-state-icon">
                        <i class="fas fa-question"></i>
                    </div>
                    <h2>{{ __('messages.notification.empty_notifications') }}</h2>
                </div>
            </div>
        </div>
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
                <a class="dropdown-item has-icon editProfileModal" href="#" data-id="{{ getLoggedInUserId() }}">
                    <i class="fa fa-user"></i>{{ __('messages.user.edit_profile') }}
                    <a class="dropdown-item has-icon emailNotificationSetting show-overflow-ellipsis"
                       href="#">
                        <i class="fa fa-inbox"> </i>{{ __('messages.email_setting.email_setting') }}
                    </a>
                    <a class="dropdown-item has-icon changePasswordModal show-overflow-ellipsis"
                       href="#" data-id="{{ getLoggedInUserId() }}">
                        <i class="fa fa-lock"> </i>{{ __('messages.user.change_password') }}
                    </a>
                    <a class="dropdown-item show-overflow-ellipsis" href="#" data-toggle="modal"
                       data-id="{{ getLoggedInUserId() }}"
                       data-target="#changeLanguageModal"><i
                                class="fa fa-language mr-2"></i>{{ __('messages.user_language.change_language') }}</a>
                    <a href="{{ url('logout') }}" class="dropdown-item has-icon text-danger show-overflow-ellipsis"
                       onclick="event.preventDefault(); localStorage.clear();  document.getElementById('logout-form').submit();">
                        <i class="fa fa-sign-out-alt"></i>{{ __('messages.user.logout') }}
                    </a>
                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" class="d-none">
                        {{ csrf_field() }}
                    </form>
                </a>
            </div>
        </li>
    @else
        <li class="dropdown"><a href="#" data-toggle="dropdown"
                                class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                <div class="d-sm-none d-lg-inline-block">{{ __('messages.common.hello') }}</div>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-title">
                    {{ __('messages.common.login') }} / {{ __('messages.common.register') }}
                </div>
                <a href="{{ route('login') }}" class="dropdown-item has-icon">
                    <i class="fas fa-sign-in-alt"></i> {{ __('messages.common.login') }}
                </a>
                <div class="dropdown-divider"></div>
            </div>
        </li>
    @endif
</ul>
