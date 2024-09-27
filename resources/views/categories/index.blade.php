@extends('layouts.app')
@section('title')
    {{ __('messages.category.categories') }}
@endsection
@push('css')
    @livewireStyles
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('assets/css/nano.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/category.css') }}">
@endpush
@section('content')
    <section class="section">
        <div class="section-header flex-wrap">
            <h1 class="mr-3">{{ __('messages.category.categories') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="#" class="btn btn-primary form-btn addModal my-sm-0 my-1">
                    {{ __('messages.common.add') }}
                    <i class="fas fa-plus"></i></a>
            </div>
        </div>
        <div class="section-body">
            @include('flash::message')
            <div class="card">
                <div class="card-body">
                    @livewire('categories')
                </div>
            </div>
        </div>
        @include('categories.templates.templates')
        @include('categories.add_modal')
        @include('categories.edit_modal')
    </section>
@endsection
@push('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script>
        let categoryUrl = "{{ route('category.index') }}"
        let categorySaveUrl = "{{ route('category.store') }}"
    </script>
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/iziToast.min.js') }}"></script>
    <script src="{{ asset('assets/js/pickr.min.js') }}"></script>
    <script src="{{ mix('assets/js/categories/categories.js') }}"></script>
@endpush



