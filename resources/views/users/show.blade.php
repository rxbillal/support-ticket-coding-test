@extends('layouts.app')
@section('title')
    {{ $isAgent ? __('messages.agent.agent_details') : __('messages.customer.customer_details') }}
@endsection
@push('css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css"/>
@endpush
@section('content')
    <section class="section">
        <div class="section-header flex-wrap">
            <h1 class="mr-3"> {{ $user->name }} {{ __('messages.user.details')  }}</h1>
            <div class="section-header-breadcrumb flex-md-grow-0 my-sm-0 my-1">
                <a href="{{ $isAgent ? route('agent.edit',$user->id) : route('customer.edit',$user->id) }}"
                   class="btn btn-warning form-btn  mr-2">{{ $isAgent ? __('messages.agent.edit_agent') : __('messages.customer.edit_customer') }}</a>
                <a href="{{ $isAgent ? route('agent.index') : route('customer.index') }}"
                   class="btn btn-primary ml-md-0 ml-auto form-btn">{{__('messages.common.back')}}</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="user" role="tabpanel" aria-labelledby="user-tab">
                            @include('users.show_fields')
                        </div>
                        <div class="mt-4 position-relative ">
                            <div class="dataTable_custom_filter_category">
                                {{ Form::select('category_id', $categories, null, ['id' => 'category-filter', 'class' => 'form-control', 'placeholder' => __('messages.admin_dashboard.select_category')]) }}
                            </div>
                            <div class="dataTable_custom_filter">
                                {{ Form::select('status_id', $statusArray, \App\Models\Ticket::STATUS_OPEN, ['id' => 'status-filter', 'class' => 'form-control', 'placeholder' => __('messages.user.select_status')]) }}
                            </div>
                            @include('users.ticket_table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        let ticketByUserUrl = "{{ route('user.ticket',$user->id) }}";
        let ticketUrl = "{{ route('ticket.index') }}";
        let statusArray = JSON.parse('@json($statusArray)');
        let statusColorArray = JSON.parse('@json($statusColorArray)');
    </script>
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/user_ticket/user_ticket.js') }}"></script>
@endpush
