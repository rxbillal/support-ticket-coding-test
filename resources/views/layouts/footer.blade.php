<div class="d-flex justify-content-between">
    <p>
        {{ __('messages.footer.all_right_reserved') }} &copy; {{ date('Y') }}
        <a href="{{ url('/') }}" class="text-decoration-none text-primary">
            {{ $settings['application_name'] }}
        </a>
    </p>
        <div>{{ getCurrentVersion() }}</div>
    </div>
