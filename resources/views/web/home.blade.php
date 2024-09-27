@extends('web.app')
@section('title')
    {{ __('messages.common.home') }}
@endsection
@push('css')
    <link href="{{ asset('assets/css/landing-page-style.css') }}" rel="stylesheet"/>
    <link href="{{ asset('theme-assets/css/animate.css') }}" rel="stylesheet"/>
@endpush
@section('content')
    <div class="scrolltop {{ auth()->check() ? 'scrollTop-sm' : '' }}">
        <div class="scroll icon"><i class="fa fa-rocket text-primary" aria-hidden="true"></i></div>
    </div>
    <div id="particles-js" class="particles-banner-section"></div>
    <div class="row justify-content-center no-gutters pt-5 banner-height">
        <div class="col-xl-8 col-lg-10 col-md-8 col-10 header-container">
            <div class="row">
                <div class="col-lg-6 header-img-section d-none d-md-block ">
                    <img class="wow fadeInUp" data-wow-duration="1s" src="{{ asset('theme-assets/img/header.png') }}"
                         alt="">
                </div>
                <div class="col-lg-5 offset-md-1 header-title-section align-items-center justify-content-center d-flex">
                    <form action="{{ route('public.tickets') }}" method="get" id="search-form">
                        <p data-wow-duration="1s"
                           class="header-subtitle wow text-lg-left text-center fadeInRight">{{ __('messages.web.how_can_we_help_you') }}</p>
                        <h1 data-wow-duration="2s" class="wow fadeInRight header-title text-lg-left text-center">
                            {{ __('messages.web.welcome_to') }} {{ $settings['application_name'] }}
                        </h1>
                        <div data-wow-duration="2.5s" class="search-container wow fadeInRight">
                            {{ Form::text('search', null, ['id' => 'publicTicketsSearch', 'class' => 'form-control', 'required', 'placeholder'=> __('messages.web.search_public_ticket').'...', 'autocomplete' => 'off']) }}
                            <button type="submit"
                                    class="btn btn-primary landing-btn">{{ __('messages.ticket.search_ticket') }}</button>
                            <div id="publicTicketsSearchResults" class="position-absolute w-100"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @if(count($categories))
    <div class="container wow fadeInUp">
        <h2 class="text-center text-primary mt-5 mb-5 margin-t-10">{{ __('messages.category.categories') }}</h2>
        <div class="row justify-content-center">
            @php
                $inStyle = 'style';
                $style = 'border-top: 2px solid';
                $styleBackground = 'background: ';
            @endphp
            @foreach($categories as $key => $category)
                <div class="col-12 col-md-6 col-lg-4 wow fadeInUp" data-wow-duration="1s">
                    <div class="card card-primary" {{$inStyle}}="{{$style}} {{ getNewColor($key) }}">
                    <a href="{{ route('public.tickets',['category' => $category->name]) }}"
                       class="text-decoration-none">
                        <div class="card-header">
                            <h4>{{ $category->name }}</h4>
                        </div>
                    </a>
                    <div class="card-body categories-count" {{$inStyle}}="{{ $styleBackground }}{{ getNewColor($key) }}"
                    >
                    <h6 class="m-0">{{ $category->ticket_count }}</h6>
                </div>
        </div>
    </div>
    @endforeach
    </div>
    <div class="d-flex justify-content-center">
        <a href="{{ route('categories-list') }}"
           class="btn btn-primary mt-3">{{ __('messages.category.browse_all_categories') }}</a>
    </div>
    </div>
    @endif
    {{--  Public Tickets section  --}}
    @if(!$publicTickets->isEmpty())
        <div class="container wow fadeInUp">
            <h2 class="text-center text-primary mt-4 mb-4">{{ __('messages.ticket.public_tickets') }}</h2>
            <div class="row justify-content-center">
                @foreach($publicTickets as $ticket)
                    <a href="{{ route('web.ticket.view', $ticket->ticket_id) }}"
                       class="col-md-12 col-lg-6 text-decoration-none ticketZIndex mt-2"
                       target="_blank">
                        <div class="support-public-tickets mb-1 p-2 position-relative">
                            <div
                                    class="ticketsContainer float-right ticketsContainer-{{ \App\Models\Ticket::STATUS_COLOR[$ticket->status] }}">
                                {{ \App\Models\Ticket::STATUS[$ticket->status] }}
                            </div>
                            <div class="mt-2 text-muted ticket-created-date">
                                <i class="far fa-clock"></i>
                                {{ \Carbon\Carbon::parse($ticket->created_at)->diffForHumans() }}
                            </div>
                            <div class="d-flex">
                                <div class="user-avatar">
                                    <img
                                            src="{{ $ticket->user ? $ticket->user->photoUrl : asset('assets/img/infyom-logo.png') }}"
                                            class="is-ticket-avatar" alt="img"/>
                                </div>
                                <div class="row ml-3 public-ticket-section">
                                    <div class="col-12">
                                        <div class="ticket-subject-text text-primary">{{ $ticket->title }}</div>
                                        <div class="text-muted">
                                            {{ __('messages.common.created_by') }}
                                            : {{ $ticket->user ?  $ticket->user->name : __('messages.common.n/a') }}
                                        </div>
                                        <div class="mt-2 text-muted">
                                            <i class="fas fa-stream"></i>
                                            {{ $ticket->category ?  $ticket->category->name : __('messages.common.n/a') }}
                                        </div>
                                        <div class="mt-2 text-muted">
                                            @if($ticket->replay_count > 0)
                                                <i class="far fa-comment-alt">
                                                </i> {{ $ticket->replay_count }} {{ __('messages.common.comments') }}
                                            @else
                                                <span>&nbsp;</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="d-flex justify-content-center">
                <a href="{{ route('public.tickets') }}"
                   class="btn btn-primary mt-3">{{ __('messages.ticket.browse_all_public_tickets') }}</a>
            </div>
        </div>
    @endif
@endsection
@push('scripts')
    <script src="{{ asset('theme-assets/js/wow.min.js') }}"></script>
    <script src="{{ asset('theme-assets/js/particlesjs/particles.js') }}"></script>
    <script src="{{ asset('theme-assets/js/particlesjs/app.js') }}"></script>
    <script>
        new WOW().init();
        let publicTicketSearchUrl = "{{ route('get.public.tickets') }}";
        $(window).scroll(function () {
            if ($(this).scrollTop() > 50) {
                $('.scrolltop:hidden').stop(true, true).slideDown('slow');
            } else {
                $('.scrolltop').stop(true, true).slideUp('slow');
            }
        });
        $(document).ready(function () {
            $(document).on('click', '.scrolltop', function () {
                $('html,body').animate({ scrollTop: $('#app').offset().top }, '1000');
                return false;
            });
        });
    </script>
    <script src="{{ mix('assets/js/web/public-tickets-autocomplete.js') }}"></script>
@endpush
