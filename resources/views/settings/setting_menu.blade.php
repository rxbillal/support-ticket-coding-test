<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body px-0">
                <ul class="nav nav-pills flex-column">
                    <li class="">
                        <a href="{{ route('settings.index', ['section' => 'general']) }}"
                           class="nav-link {{ (isset($sectionName) && $sectionName == 'general') ? 'active' : ''}}">
                            {{ __('messages.general') }}
                        </a>
                    </li>
                    <li class="">
                        <a href="{{ route('settings.index', ['section' => 'social-settings']) }}"
                           class="nav-link {{ (isset($sectionName) && $sectionName == 'social-settings') ? 'active' : ''}}">
                            {{ __('messages.social_settings') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        @yield('section')
    </div>
</div>

