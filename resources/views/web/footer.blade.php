<div class="footer-section mt-4">
    @php
        if(!isset($settings)){
        $settings = \App\Models\Setting::all()->pluck('value', 'key')->toArray();
    }
    $style = 'style';
    $backgroundImage = 'background-image';
    $footerImage1 = asset('theme-assets/img/customer-support-footer.png');
    $footerImage2 = asset('theme-assets/img/customer-support-footer(2).jpg');
    @endphp
    <div class="justify-content-center footer-container">
        <div class="footer-img-left d-none d-md-block"
        {{ $style }}="{{ $backgroundImage }}: url('{{ $footerImage1 }}');"></div>
    <div class="footer-img-right d-none d-md-block"
    {{ $style }}="{{ $backgroundImage }}: url('{{ $footerImage2 }}');"></div>
<div class="row justify-content-around">
    <!-- Footer Column 1 -->
    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 footer-logo">
        <img src="{{ isset($settings) ? asset($settings['logo']) : asset(getSettingValue('logo')) ?? asset('assets/img/infyom-logo.png') }}"
             alt="{{ __('messages.logo_not_found') }}">
        <p class="mt-3">
            {{ __('messages.footer.all_right_reserved') }} &copy; {{ date('Y') }} {{ $settings['application_name'] }}</p>
        <div class="footer-social-media-icons-section">
            @if(!empty($settings['facebook_url']))
                <a href="{{ $settings['facebook_url'] }}" class="text-decoration-none" target="_blank">
                            <i class="fab fa-facebook-square footer-social-media-icon"></i>
                        </a>
                    @endif
                    @if(!empty($settings['twitter_url']))
                        <a href="{{ $settings['twitter_url'] }}" class="text-decoration-none" target="_blank">
                            <i class="fab fa-twitter-square footer-social-media-icon"></i>
                        </a>
                    @endif
                    @if(!empty($settings['linkedIn_url']))
                        <a href="{{ $settings['linkedIn_url'] }}" class="text-decoration-none" target="_blank">
                            <i class="fab fa-linkedin footer-social-media-icon"></i>
                        </a>
                    @endif
                </div>
            </div>
            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 text-md-left text-center ">
                <div class="footer-subsection-2-1">
                    <h3 class="footer-subsection-title">{{ __('messages.setting.about_us') }}</h3>
                    <p class="footer-subsection-text">{{ $settings['about_us'] }}</p>
                </div>
            </div>
            <!-- Footer Column 2 -->
            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 text-md-left text-center ">
                <h3 class="footer-subsection-title">{{ __('messages.setting.contact_us') }}</h3>
                <p class="footer-subsection-list">
                    {{ $settings['address'] }}
                </p>
                <p class="mb-2">
                    {{ __('messages.common.email').':' }}
                    <a href="mailto:{{ $settings['email'] }}"
                       class="mb-2  text-decoration-none"> {{ $settings['email'] }}</a>
                </p>
                <p class="mb-0">
                    {{ __('messages.common.phone').':' }}
                    <a href="tel:{{ '+'.$settings['region_code'].' '.$settings['phone'] }}"
                       class="text-decoration-none"> {{ '+'.$settings['region_code'].' '.$settings['phone'] }}</a>
                </p>
            </div>
        </div>
    </div>
</div>

