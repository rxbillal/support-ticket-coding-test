@extends('web.app')
@section('title')
    {{ __('messages.ticket.ticket_details') }}
@endsection
@push('css')
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/css/summernote.min.css') }}" rel="stylesheet" type="text/css"/>
@endpush
@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-12 col-md-12">
            <div class="card card-primary shadow">
                <div class="card-header">
                    <h4>{{ __('messages.ticket.ticket_details') }}</h4>
                    <a href="{{ route('web.search_ticket_form') }}"
                       class="btn btn-primary ml-auto">{{ __('messages.common.back') }}</a>
                </div>
                <div class="card-body mt-0 pt-0">
                    <div class="tickets mt-3">
                        <div class="ticket-content w-100">
                            <div class="ticket-header">
                                <div class="ticket-sender-picture img-shadow">
                                    <img src="{{ asset('theme-assets/img/avatar-5.png') }}" class="object-fit-cover" alt="image">
                                </div>
                                <div class="ticket-detail">
                                    <div class="ticket-title">
                                        <h4>{{$ticket->title}}</h4>
                                    </div>
                                    <div class="ticket-info">
                                        <div class="font-weight-600">{{ $ticket->title }}</div>
                                        <div class="bullet"></div>
                                        <div class="text-primary font-weight-600">{{ $ticket->created_at->diffForHumans() }}</div>
                                        <span class="ml-5 badge badge-{{ \App\Models\Ticket::STATUS_COLOR[$ticket->status] }}">
                                            {{ \App\Models\Ticket::STATUS[$ticket->status] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-1">
                                    <span class="font-weight-600">{{ __('messages.common.email') }}</span>
                                </div>
                                <div class="col-11">
                                    <div class="font-weight-600 h6">{{ $ticket->email }}</div>
                                </div>
                                <div class="col-1">
                                    <span class="font-weight-600">{{ __('messages.ticket.ticket_number') }}</span>
                                </div>
                                <div class="col-11">
                                    <div class="font-weight-600 h6">{{ $ticket->ticket_id }}</div>
                                </div>
                            </div>
                            <div class="ticket-description mt-3">
                                <p>
                                    {!! $ticket->description !!}
                                </p>
                                <div class="gallery">
                                    @foreach(range(1,5) as $index)
                                        <a class="gallery-item" data-title="Image {{$index}}"
                                           data-image="{{ asset('theme-assets/img/img01.jpg') }}">
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                            <div class="ticket-divider"></div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('assets/js/summernote.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ mix('assets/js/tickets/create_edit.js')}}"></script>
@endpush
