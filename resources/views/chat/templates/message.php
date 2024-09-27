<script id="tmplMessage" type="text/x-jsrender">
        <div id="send-receive-direction" class="{{:className}} message-{{:data.id}}" data-message_id="{{:data.id}}">
            <div class="chat-conversation__avatar">
            {{if senderIsAdmin}}
                <span class="highlighter" data-toggle="tooltip" title="Admin"><img src="./assets/images/crown.svg"></span>
            {{/if}}
             {{if isReceiver}}
                <img src="{{:senderImage}}" alt="" title="{{:senderName}}" class="img-fluid conversation-user-img user-chat-image-{{:data.sender.id}}">
             {{else}}
              <img src="{{:authImage}}" alt="" class="img-fluid conversation-user-img">
             {{/if}}
            </div>
            {{if !isReceiver}}
                <div class="chat-conversation__menu d-flex align-items-center">
                    <div class="dropdown btn-group hide-ele msg-options">
                        <i class="fa fa-ellipsis-v " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                        <div class="dropdown-menu">
                            {{if allowToDelete}}
                                <a class="dropdown-item msg-delete-icon" href="#"><?php echo __('messages.chats.delete_message') ?></a>
                            {{/if}}
                            {{if deleteMsgForEveryone}}
                                <a class="dropdown-item msg-delete-for-everyone" href="#"><?php echo __('messages.chats.delete_for_everyone') ?></a>
                            {{/if}}
                             <a class="dropdown-item open-msg-info" data-message-id="{{:data.id}}">
                                <?php echo __('messages.chats.info') ?>
                             </a>
                        </div>
                    </div>
                </div>
            {{/if}}
            <div class="chat-conversation__bubble clearfix {{if data.message_type !== 0 &&
    isReceiver}} bubble-border-green {{/if}}" >
                <div class="chat-conversation__bubble-text message">
                    {{:~displayMessage(data)}}
                </div>
                <div class="chat-container__time text-nowrap chat-time">{{:~getChatMagTimeInConversation(
        data.created_at)}}</div>
                <div class="chat-container__read-status position-absolute {{if isReceiver}} d-none {{else}} {{:readUnread}} {{/if}}">
                    <i class="fa fa-check" aria-hidden="true"></i>
                </div>  
            </div>
         {{if isReceiver}}
        {{/if}}
        </div>



</script>

<script id="groupMsgReadUnreadInfo" type="text/x-jsrender">
    <div class="chat__person-box" data-id="{{:user.user_id}}" id="user-{{:user.user_id}}">
        <div class="position-relative chat__person-box-status-wrapper">
            <div class="chat__person-box-avtar">
                <img src="{{:user.photo_url}}" alt="person image" class="user-avatar-img">
            </div>
        </div>
        <div class="chat__person-box-detail">
            <h5 class="mb-1 chat__person-box-name contact-name">{{:user.name}}</h5>
            <p class="mb-0 chat-message">
                {{if ~checkReadAtDate(read_at) }}
                    <span><i class="fa fa-check mx-0 chat__person-box--read" aria-hidden="true"></i>&nbsp;<span class="read_at">{{:~getCalenderFormatForLastSeen(read_at)}}</span></span>
                {{/if}}
            </p>
        </div>
    </div>


</script>
<script id="groupMsgReadUnreadMessage" type="text/x-jsrender">
<div class="chat-conversation__sender message-{{:data.id}} mt-0" data-message_id="{{:data.id}}">
    <div class="chat-conversation__bubble clearfix {{if data.message_type !== 0}} bubble-border-green {{/if}}" >
        <div class="chat-conversation__bubble-text message">
                {{:~displayMessage(data)}}
        </div>
        <div class="chat-container__time text-nowrap chat-time">{{:~getChatMagTimeInConversation(data.created_at)}}</div>
        <div class="chat-container__read-status position-absolute>
            <i class="fa fa-check" aria-hidden="true"></i>
        </div>  
    </div>
</div>



</script>
<script id="singleMessageReadInfoTmpl" type="text/x-jsrender">
    <h6 class="msg-info__column-title"><span class="group-users-count"></span>&nbsp;<?php echo __('messages.chats.read') ?></h6>
    <div class="pl-1" id="msg-read-at-{{:id}}">{{if status}}{{:~getCalenderFormatForLastSeen(
        updated_at)}} {{else}} - {{/if}}</div>
    <div class="msg-info__divider my-2" id="single-msg-divider"></div>
    <h6 class="msg-info__column-title"><span class="group-users-count"></span>&nbsp;<?php echo __('messages.chats.delivered') ?></h6>
    <div class="pl-1">{{:~getCalenderFormatForLastSeen(created_at)}}</div>



</script>
