@php
    /** @var $replay \App\Models\TicketReplay */
@endphp
<div class="jumbotron jumbotron-fluid ticket-reply"
     data-remove-id="{{$replay->id}}">
    <div class="d-flex">
        <div>
            <img class="reply-user-img"
                 src="{{ $replay->user->photo_url }}" alt="">
        </div>
        <div class="ml-3 flex-1">
            <p class="mb-0">
                <span class="reply-user-name">{{ $replay->user->name }}</span>
                @php
                    $isActionShow = $isAction && (getLoggedInUserId() == $replay->user->id || getLoggedInUserId() == $adminRoleId) && $ticket->status != \App\Models\Ticket::STATUS_CLOSED
                @endphp
                @if($isActionShow)
                    <span class="float-right ticket-action-btn">
                        <a href="javascript:void(0)"
                           class="edit-reply text-warning"
                           title="{{ __('messages.common.edit') }}"
                           data-id="{{ $replay->id }}"><i
                                    class="mr-2 fa fa-edit"></i></a>
                        <a href="javascript:void(0)"
                           class="del-reply text-danger"
                           title="{{ __('messages.common.delete') }}"
                           data-id="{{ $replay->id }}"><i
                                    class="fa fa-trash"></i></a>
                    </span>
                @endif
            </p>
            <p class="mb-0 ticket-reply-time replyTime-{{ $replay->id }}">
                {{ $replay->updated_at->timezone('Asia/Kolkata')->isoFormat('Do MMMM, YYYY hh:mm A') }}
            </p>
            <span class="mt-3 reply-description description-{{ $replay->id }}">{!! $replay->description !!}</span>
            <div id="editTicketReply-{{ $replay->id }}"
                 class="d-none mb-3 editReplyBox">
                <div class="editReplyContainer"
                     id="editReply-{{ $replay->id }}"></div>
                <div class="text-left mt-3">
                    <button class="btn btn-primary"
                            id="editTicketReply"
                            data-loading-text="<span class='spinner-border spinner-border-sm'></span> {{__('messages.placeholder.processing')}}">{{ __('messages.common.save') }}</button>
                    <button type="button" id="editAttachmentBtn" data-toggle="modal"
                            data-target="#editAttachment" class="btn btn-info btn-icon custom-ticket-btn">
                        <i class="fas fa-paperclip"></i>
                        {{ __('messages.common.add').' '.__('messages.ticket.attachments') }}
                    </button>
                    <a href="javascript:void(0)"
                       class="btn btn-secondary text-dark cancelEditReply"
                       id="cancelEditReply"
                       data-id="{{ $replay->id }}">{{__('messages.common.cancel')}}</a>
                </div>
            </div>
            <div class="{{ $replay->id }}-attachment-main-div">
                @if($replay->media->count())
                    <div class="reply-attached-files" id="{{ $replay->id }}-attachment-div">
                        <label class="ml-3 text-muted">{{ __('messages.ticket.attachments') }}:</label>
                        @php
                            /** @var $media \Spatie\MediaLibrary\Models\Media */
                        @endphp
                        @foreach($replay->media as $media)
                            <div class="ml-3 mb-1">
                                <a target="_blank"
                                   href="{{ $media->getFullUrl() }}"
                                   class="text-muted">{{ substr($media->file_name, 0, 15) .'...' }}</a>
                                @if($isActionShow)
                                    <a href="javascript:void(0)"
                                       data-media-id="{{ $media->id }}"
                                       class="remove-attached-file text-muted ml-1">
                                        <i class="far fa-times-circle"></i>
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
