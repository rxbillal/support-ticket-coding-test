<div class="fabs">
    <div class="chat" id="chatDivision">
        <div class="chat_header">
            <div class="chat_option">
                <div class="header_img">
                    <img src="{{ asset('theme-assets/img/avatar-1.png') }}">
                </div>
                <span id="chat_head" class="position-absolute pt-2">{{ __('messages.agent.agent')."" }}</span>
                <button type="button" class="close close-chat" id="close-chat" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
        <div class="msg_chat d-none">
            <div id="chat_fullscreen" class="chat-conversation chat_converse">
                {{--                <h5 class="text-center" id="no_chat_msg">{{ __('messages.no_reply_available') }}</h5>--}}
            </div>
            <div class="fab_field">
                <input type="text" id="chatSend" name="chat_message" placeholder="{{ __('messages.web.type_message') }}"
                       class="chat_field chat_message ">
                <button class="btn btn-danger btn-sm my-2 d-block mx-auto end-chat-button" id="endChatButton">
                    <span>{{ __('messages.chats.end_chat') }}</span></button>
            </div>
        </div>
        <div class="msg_form d-block">
            <div id="chat_fullscreen" class="chat-conversation chat_converse">
                <form class="pt-4" name="chat_form" id="chatForm" autocomplete="off">
                    <div class="form-group row mb-3">
                        <div class="col-sm-12 col-md-12">
                            <div class="input-group input-group-merge input-group-alternative">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                </div>
                                <input type="email" id="chat_email" placeholder="{{ __('messages.web.enter_mail') }}"
                                       name="email"
                                       class="form-control" autofocus="" required>
                            </div>
                            <div class="invalid-feedback d-block e_error">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12">
                            <div class="input-group input-group-merge input-group-alternative">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user-alt"></i></span>
                                </div>
                                <input type="text" id="chat_name" placeholder="{{ __('messages.web.enter_name') }}"
                                       name="name"
                                       class="form-control" autofocus="" required>
                            </div>
                            <div class="invalid-feedback d-block e_error">
                            </div>
                        </div>
                    </div>
                    <div class="row d-flex justify-content-center">
                        <div class="col-sm-12 col-md-7 text-center">
                            <button class="btn btn-primary btn-sm" id="chat_frm_submit" type="submit">
                                <span>{{ __('messages.start_chat') }}</span></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @if(!Auth::check())
        <a id="prime" class="fab" data-toggle="tooltip" data-placement="top" title="{{ __('messages.start_chat') }}"><i
                    class="prime far fa-comments text-white ml-0"></i></a>
    @endif
</div>
