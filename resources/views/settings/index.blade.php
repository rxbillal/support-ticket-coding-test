@extends('layouts.app')
@section('title')
    {{ __('messages.settings') }}
@endsection
@push('css')
    <link href="{{ asset('assets/css/summernote.min.css') }}" rel="stylesheet" type="text/css"/>
@endpush
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.settings') }}</h1>
        </div>
        <div class="section-body">
            @include('flash::message')
            <div class="alert alert-danger mb-2 d-none" id="validationErrorsBox"></div>
            <div class="card">
                <div class="card-body py-0 mt-2">
                    @include("settings.setting_menu")
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script src="{{ asset('assets/js/summernote.min.js') }}"></script>
    <script src="{{ mix('assets/js/settings/settings.js') }}"></script>
@endpush
