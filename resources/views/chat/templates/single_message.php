<script id="tmplSingleMessage" type="text/x-jsrender">
<div  id="send-receive-direction" class="chat-conversation__sender" data-message_id="{{:randomMsgId}}">
    <div class="chat-conversation__avatar">
        <img src="{{:senderImg}}" alt="" class="img-fluid conversation-user-img">
    </div>
    <div class="chat-conversation__menu d-flex align-items-center">
           <div class="dropdown btn-group hide-ele msg-options">
                <i class="fa fa-ellipsis-v " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                </i>
                <div class="dropdown-menu">
                    <a class="dropdown-item msg-delete-icon" href="#"><?php echo __('messages.chats.delete_message') ?></a>
                    <a class="dropdown-item msg-delete-for-everyone" href="#"><?php echo __('messages.chats.delete_for_everyone') ?></a>
                     <a class="dropdown-item open-msg-info" data-message-id="{{:randomMsgId}}" >
                        <?php echo __('messages.chats.info') ?>
                     </a>
                </div>
            </div>                           
    </div>
    <div class="chat-conversation__bubble clearfix">
        <div class="chat-conversation__bubble-text message">
            {{:message}}
        </div>
        <div class="chat-container__time text-nowrap chat-time">{{:time}}</div>
        <div class="chat-container__read-status position-absolute">
            <i class="fa fa-check" aria-hidden="true"></i>
        </div>
    </div>

</div>




</script>
