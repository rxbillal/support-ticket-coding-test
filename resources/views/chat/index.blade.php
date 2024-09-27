@extends('layouts.app')
@section('title')
    {{ __('messages.conversations') }}
@endsection
@push('css')
    {{--    <link rel="stylesheet" href="{{ asset('assets/css/coreui.min.css') }}">--}}
    <link rel="stylesheet" href="{{ asset('assets/icheck/skins/all.css') }}">
    <link rel="stylesheet" href="{{ asset('theme-assets/css/dropzone.css') }}">
    <link rel="stylesheet" href="{{ asset('theme-assets/css/ekko-lightbox.css') }}">
    <link rel="stylesheet" href="{{ mix('assets/css/video-js.css') }}">
    <link rel="stylesheet" href="{{ mix('assets/css/new-conversation.css') }}">
    <link rel="stylesheet" href="{{ asset('theme-assets/css/emojionearea.min.css') }}">
    <link rel="stylesheet" href="{{ mix('assets/css/chat.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin_theme.css') }}">
    @livewireStyles
    <style>
        .select2-search--dropdown .select2-search__field {
            width: 100% !important;
        }
    </style>
@endpush
@section('content')
    <div class="page-container remove-scroll p-md-3 p-0">
        <div class="chat-container chat">
            <div class="chat__inner">
                <!-- left section of chat area (chat person selection area) -->
                <div class="chat__people-wrapper chat__people-wrapper--responsive">
                    <div class="chat__people-wrapper-header">
                        <span class="h3 mb-0">{{ __('messages.conversations') }}</span>
                        <div class="d-flex chat__people-wrapper-btn-group">
                            <i class="nav-icon fa fa-bars align-top chat__people-wrapper-bar"></i>
                            <div class="chat__people-wrapper-button" data-toggle="modal"
                                 data-target="#addNewChat">
                                <i class="nav-icon " data-toggle="tooltip" data-placement="bottom"
                                   title="{{ __('messages.new_conversation') }}">
                                    <i class="fas fa-comment-medical add-chat-icon text-primary"></i></i>
                            </div>
                        </div>
                    </div>
                    <div class="chat__search-wrapper">
                        <div class="chat__search clearfix chat__search--responsive">
                            <i class="fa fa-search"></i>
                            <input type="search" placeholder="{{ __('messages.search') }}" class="chat__search-input"
                                   id="searchUserInput" autocomplete="off">
                            <i class="fa fa-search d-lg-none chat__search-responsive-icon"></i>
                        </div>
                    </div>
                    <ul class="nav nav-tabs chat__tab-nav" id="chatTabs">
                        <li class="nav-item">
                            <a data-toggle="tab" id="activeChatTab" class="nav-link active" href="#chatPeopleBody">
                                {{ __('messages.chats.active_chat') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a data-toggle="tab" id="archiveChatTab" class="nav-link show-overflow-ellipsis"
                               href="#archivePeopleBody">
                                {{ __('messages.chats.archive_chat') }}
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content chat__tab-content">
                        <div class="chat__people-body tab-pane fade in active show" id="chatPeopleBody">
                            <div id="infyLoader" class="chat-infy-loader chat__people-body-loader">
                                @include('partials.infy-loader')
                            </div>
                            <div class="text-center no-conversation">
                                <div class="chat__no-conversation">
                                    <div class="text-center"><i class="fa fa-2x fa-commenting-o" aria-hidden="true"></i>
                                    </div>
                                    <div class="no-chat-message d-none"> {{ __('messages.no_conversation_found') }}</div>
                                </div>
                            </div>
                            <div class="text-center no-conversation-yet">
                                <div class="chat__no-conversation">
                                    <div class="text-center"><i class="fa fa-2x fa-commenting-o" aria-hidden="true"></i>
                                    </div>
                                    <div class="no-chat-message d-none">{{ __('messages.no_conversation_added_yet') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="chat__people-body tab-pane fade in active" id="archivePeopleBody">
                            <div class="text-center no-archive-conversation">
                                <div class="chat__no-archive-conversation">
                                    <div class="text-center"><i class="fa fa-2x fa-commenting-o" aria-hidden="true"></i>
                                    </div>
                                    {{ __('messages.no_conversation_found') }}
                                </div>
                            </div>
                            <div class="text-center no-archive-conversation-yet">
                                <div class="chat__no-archive-conversation">
                                    <div class="text-center"><i class="fa fa-2x fa-commenting-o" aria-hidden="true"></i>
                                    </div>
                                    {{ __('messages.no_conversation_added_yet') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/ left section of chat area -->


                <!-- right section of chat area (chat conversation area)-->
                <div class="chat__area-wrapper">
                    @include('chat.no-chat')
                </div>
                <!--/ right section of chat area-->

                <!-- profile section (chat profile section)-->
            @include('chat.chat_profile')
            @include('chat.msg_info')
            <!--/ profile section -->
            </div>
        </div>
        <!-- Modal -->
        <div id="addNewChat" class="modal fade" role="dialog" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <!-- Modal content-->
                <div class="modal-content modal-new-conversation">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title" id="modalTitle">{{ __('messages.chats.new_conversations') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            &times;
                        </button>
                    </div>
                    <div class="modal-body">
                        @livewire('search-users', ['blockUserIds' => $blockUserIds])
                    </div>
                </div>
            </div>
        </div>

    </div>
    @include('chat.templates.conversation-template')
    @include('chat.templates.message')
    @include('chat.templates.no-messages-yet')
    @include('chat.templates.no-conversation')
    @include('chat.templates.user_details')
    @include('chat.templates.blocked_users_list')
    @include('chat.templates.add_chat_users_list')
    @include('chat.templates.badge_message_template')
    @include('chat.templates.member_options')
    @include('chat.templates.single_message')
    @include('chat.templates.contact_template')
    @include('chat.templates.conversations_list')
    @include('chat.templates.common_templates')
    @include('chat.templates.conversation-request')
    @include('chat.copyImageModal')
@endsection
@push('scripts')

    <script>
        let setLastSeenURL = '{{url('update-last-seen')}}';
        let pusherKey = '{{ config('broadcasting.connections.pusher.key') }}';
        let pusherCluster = '{{ config('broadcasting.connections.pusher.options.cluster') }}';
        let messageDeleteTime = '{{ config('configurable.delete_message_time') }}';
        let changePasswordURL = '{{ url('change-password') }}';
        let baseURL = '{{ url('/') }}';
        let conversationsURL = '{{ route('conversations') }}';
        let notifications = JSON.parse(JSON.stringify({!! json_encode(getNotifications()) !!}));
        let loggedInUserStatus = '{!! Auth::user()->userStatus !!}';
        if (loggedInUserStatus != '') {
            loggedInUserStatus = JSON.parse(JSON.stringify({!! Auth::user()->userStatus !!}));
        }
        let isloggedInUserAdmin = '{{ Auth::user()->hasRole('Admin') }}';

        let userURL = '{{url('users')}}/';
        let userListURL = '{{url('users-list')}}';
        let conversationListURL = '{{url('conversations-list')}}';
        let archiveConversationListURL = '{{url('archive-conversations')}}';
        let chatSelected = false;
        let sendMessageURL = '{{route('conversations.store')}}';
        let fileUploadURL = '{{route('file-upload')}}';
        let imageUploadURL = '{{route('image-upload')}}';
        let csrfToken = '{{csrf_token()}}';
        let authUserName = '{{ Auth::user()->name }}';
        let readMessageURL = '{{url('read-message')}}';
        let authImgURL = '{{Auth::user()->photo_url}}';
        let deleteConversationUrl = '{{url('conversations')}}/';
        let deleteMessageUrl = '{{url('conversations/message')}}/';
        let getUsers = '{{url('get-users')}}';
        let groupsList = '{{url('groups')}}';
        let appName = '{{ getAppName() }}';
        let conversationId = '{{ $conversationId }}';
        let sendChatReqURL = '{{route('send-chat-request')}}';
        let acceptChatReqURL = '{{route('accept-chat-request')}}';
        let declineChatReqURL = '{{route('decline-chat-request')}}';
        let reportUserURL = '{{route('report-user.store')}}';
        /** Icons URL */
        let pdfURL = '{{ asset('assets/icons/pdf.png') }}';
        let xlsURL = '{{ asset('assets/icons/xls.png') }}';
        let textURL = '{{ asset('assets/icons/text.png') }}';
        let docsURL = '{{ asset('assets/icons/docs.png') }}';
        let videoURL = '{{ asset('assets/icons/video.png') }}';
        let youtubeURL = '{{ asset('assets/icons/youtube.png') }}';
        let audioURL = '{{ asset('assets/icons/audio.png') }}';
        let isUTCTimezone = '{{(config('app.timezone') == 'UTC') ? 1  :0 }}';
        let timeZone = '{{config('app.timezone')}}';
        let newConversationsLabel= '{{ __('messages.chats.new_conversations') }}';
        let assignToAgentLabel= '{{ __('messages.chats.assign_to_agent') }}';
    </script>

    <!--custom js-->
    <script src="{{ asset('js/dropzone.min.js') }}"></script>
    <script src="{{ asset('js/ekko-lightbox.min.js') }}"></script>
    <script src="{{ mix('assets/js/video.min.js') }}"></script>
    <script src="{{ asset('assets/icheck/icheck.min.js') }}"></script>
    <script src="{{ asset('theme-assets/js/emojionearea.js') }}"></script>
    <script src="{{ asset('assets/js/emojione.min.js') }}"></script>
    <script src="{{ mix('assets/js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ mix('assets/js/app.js') }}"></script>
    <script src="{{ mix('assets/js/notification.js') }}"></script>
    <script src="{{ mix('assets/js/set-user-on-off.js') }}"></script>
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')

    <script src="{{ mix('assets/js/chat.js') }}"></script>
@endpush
