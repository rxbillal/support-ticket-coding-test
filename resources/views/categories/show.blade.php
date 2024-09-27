@extends('layouts.app')
@section('title')
    {{ __('messages.category.category_details') }}
@endsection
@push('css')
    @livewireStyles
    <link rel="stylesheet" href="{{ asset('assets/css/category.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/nano.min.css') }}">
    <link href="{{ asset('assets/css/ticket.css') }}" rel="stylesheet" type="text/css"/>
@endpush
@section('content')
    <section class="section">
        <div class="section-header sm-section-p flex-wrap">
            <h1 class="mr-3">{{ $category->name.' '.__('messages.user.details') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="javascript:void(0)" data-id="{{ $category->id }}"
                   class="btn btn-warning form-btn float-right mr-2 edit-btn">{{ __('messages.category.edit_category') }}</a>
                <a href="{{ route('category.index') }}"
                   class="btn btn-primary form-btn float-right my-sm-0 my-1">{{__('messages.common.back')}}</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @livewire('tickets',['category' => $category])
                </div>
            </div>
            <div id="taskEditModal">
                @include('tickets.edit_assignee_modal')
            </div>
        </div>
        @include('categories.edit_modal')
    </section>
@endsection
@push('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script>
        let categoryUrl = "{{ route('category.index') }}"
        let ticketUrl = '{{ url('admin/tickets') }}/'
        let activeStatus = '{{ \App\Models\Ticket::STATUS_ACTIVE }}'
    </script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ mix('assets/js/tickets/tickets.js') }}"></script>
    <script src="{{ asset('assets/js/pickr.min.js') }}"></script>
    <script src="{{ mix('assets/js/categories/show_category.js') }}"></script>
@endpush
