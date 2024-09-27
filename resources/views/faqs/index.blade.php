@extends('layouts.app')
@section('title')
    {{ __('messages.faq.faqs') }}
@endsection
@push('css')
    @livewireStyles
    <link href="{{ asset('assets/css/summernote.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css"/>
@endpush
@push('page_css')
    <link rel="stylesheet" href="{{ asset('assets/css/faqs.css') }}">
@endpush
@section('content')
    <section class="section">
        <div class="section-header flex-wrap">
            <h1 class="mr-3">{{ __('messages.faq.faqs') }}</h1>
            <div class="section-header-breadcrumb my-sm-0 my-1">
                <a href="#" class="btn btn-primary form-btn addFaqModal">{{ __('messages.faq.add') }}
                    <i class="fas fa-plus"></i></a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @include('faqs.table')
                </div>
            </div>
        </div>
        @include('faqs.templates.templates')
        @include('faqs.add_modal')
        @include('faqs.edit_modal')
        @include('faqs.show_modal')
    </section>
@endsection
@push('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script>
        let faqUrl = "{{ route('faqs.index') }}"
    </script>
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ asset('assets/js/summernote.min.js') }}"></script>
    <script src="{{mix('assets/js/faqs/faqs.js')}}"></script>
@endpush
