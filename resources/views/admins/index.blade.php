@extends('layouts.app')
@section('title')
    {{ __('messages.admin.admin') }}
@endsection
@push('css')
    @livewireStyles
    <link href="{{ asset('assets/css/summernote.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css"/>
@endpush
@section('content')
    <section class="section">
        <div class="section-header flex-wrap">
            <h1 class="mr-3">{{ __('messages.admins') }}</h1>
            <div class="section-header-breadcrumb my-sm-0 my-1">
                <a href="{{ route('admins.create') }}" class="btn btn-primary form-btn">{{ __('messages.faq.add') }}
                    <i class="fas fa-plus"></i></a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @include('admins.table')
                </div>
            </div>
        </div>
        @include('admins.templates.templates')
    </section>
@endsection
@push('scripts')
    @livewireScripts
    <script>
        let adminUrl = "{{ route('admins.index') }}"
    </script>
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ asset('assets/js/summernote.min.js') }}"></script>
    <script src="{{mix('assets/js/users/admins.js')}}"></script>
    <script src="{{mix('assets/js/users/users.js')}}"></script>
@endpush
