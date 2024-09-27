<script id="getChatRequestTmpl" type="text/x-jsrender">
<div class="request__wrapper">
    <div class="request__header">
        <div class="profile-image">
            <img src="{{:photo_url}}">
        </div>
    </div>
    {{if ~checkReqAlreadyDeclined(chat_req)}}
        <div class="request__content">
            <h3 class="request__content-title"><?php echo __('messages.chats.chat_request_declined') ?></h3>
        </div>
    {{else}}
        <div class="request__content">
            <h3 class="request__content-title"><?php echo __('messages.chats.private_conversations') ?> {{:name}}.</h3>
            <span class="text-muted mt-3">{{:name}} <?php echo __('messages.chats.wants_to_chat') ?></span>
        </div>
        <div class="request__message">
            <h5 class="text-muted"><?php echo __('messages.chats.join_private_conversations') ?></h5>
            <div class="request__buttons">
                <a class="decline-btn" id="declineChatReq" data-id={{:chat_req_id}}><?php echo __('messages.common.decline') ?></a>
                <a class="accept-btn" id="acceptChatReq" data-id={{:chat_req_id}}><?php echo __('messages.chats.accept') ?></a>
            </div>
        </div>
    {{/if}}
</div>




</script>

<script id="sendRequestTmpl" type="text/x-jsrender">
<div class="request__wrapper">
    <div class="request__header">
        <div class="profile-image">
            <img src="{{:photo_url}}">
        </div>
    </div>
    {{if ~checkReqAlreadySent(chat_req)}}
        <div class="request__content">
            <h3 class="request__content-title"><?php echo __('messages.chats.send_request_to_user') ?></h3>
        </div>
    {{else}}
        <div class="request__content">
            <h3 class="request__content-title"><?php echo __('messages.chats.private_conversations') ?> {{:name}}.</h3>
        </div>
        <div class="send__request__message text-center">
            <h5 class="text-muted"><?php echo __('messages.chats.start_conversation_with') ?>
   {{:name}} <?php __('messages.chats.now') ?></h5>
            <input type="text" placeholder="Message..." id="chatRequestMessage-{{:id}}">
            <div class="mt-5 text-center">
                <a id="sendChatRequest" class="send-request-btn" data-id={{:id}}><?php echo __('messages.chats.send_chat_request') ?></a>
            </div>
        </div>
    {{/if}}
</div>




</script>
