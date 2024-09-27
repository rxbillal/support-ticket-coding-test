@extends('layouts.app')
@section('title')
    {{ __('messages.agent.agents') }}
@endsection
@push('css')
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('assets/css/users.css') }}">
    @livewireStyles
    {{--    @notifyCss--}}
@endpush
@section('content')
    <section class="section">
        <div class="section-header flex-wrap">
            <h1 class="mr-3">{{ __('messages.agent.agents') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('user.create') }}" class="btn btn-primary form-btn my-sm-0 my-1">
                    {{ __('messages.common.add') }}
                    <i class="fas fa-plus"></i></a>
            </div>
        </div>
        <div class="section-body">
            {{--            @include('notify::messages')--}}
            @include('flash::message')
            <div class="card">
                <div class="card-body">
                    @livewire('users')
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script>
        let userUrl = "{{ route('user.index') }}"
    </script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ mix('assets/js/users/users.js') }}"></script>
    @notifyJs
@endpush

