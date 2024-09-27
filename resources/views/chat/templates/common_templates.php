<script id="tmplChatSendArea" type="text/x-jsrender">
<div class="chat__area-text">
    <div class="w-100 flex-1 chat__area-form ">
        <input type="text" id="textMessage" class="txtMessage" placeholder="<?php echo __('messages.chats.type_message').'...' ?>">
    </div>
    <div class="flex-1 d-flex chat__area-btn-group">
        <button class="chat__area-media-btn mr-2 btn"
                data-target="#fileUpload"
                id="chat-media-paperclip"
                data-toggle="modal">
            <i class="fa fa-paperclip" aria-hidden="true"></i>
        </button>
        <button type="button" id="btnSend" class="btn chat__area-send-btn chat__area-send-btn--disable">
            <i class="far fa-paper-plane"></i>
        </button>
    </div>
</div>




</script>

<script id="tmplUserNewStatus" type="text/x-jsrender">
<i class="nav-icon user-status-icon" data-toggle="tooltip" data-placement="top" title="{{:status}}" data-original-title="{{:status}}">
    {{:emoji}}
</i>




</script>

<script id="tmplNewMsgIndicator" type="text/x-jsrender">
<div class="chat__msg-day-divider position-relative">
    <span class="chat__msg-day-new-msg position-absolute"><?php echo __('messages.chats.new_message') ?></span>
</div>




</script>

<script id="tmplBlockMsgText" type="text/x-jsrender">
<div class="d-flex justify-content-center blocked-message-text">
    <span class="chat__msg-day-title"><?php echo __('messages.chats.user_block') ?></span>
</div>



</script>

<script id="tmplBlockByMsgText" type="text/x-jsrender">
<div class="d-flex justify-content-center blocked-message-text">
    <span class="chat__msg-day-title"><?php echo __('messages.chats.user_block') ?></span>
</div>



</script>

<script id="tmplHiddenTxtMsg" type="text/x-jsrender">
<input type="hidden" id="textMessage" class="hdn-text-message d-none">



</script>

<script id="tmplCloseGroupIcon" type="text/x-jsrender">
<i class="fa fa-lock closed-group-badge" data-toggle="tooltip" data-placement="top" title="The admin only can send messages into the group."></i>


</script>

<script id="tmplPrivateGroupIcon" type="text/x-jsrender">
<i class="fa fa-shield private-group-badge" data-toggle="tooltip" data-placement="top" title="The admin only can add or remove members from the group."> </i>



</script>

<script id="tmplToday" type="text/x-jsrender">
    <div class='chat__msg-day-divider d-flex justify-content-center'>
    <span class='chat__msg-day-title' ><?php echo __('messages.today') ?></span>
    </div>
</script>

<script id="tmplLinkPreview" type="text/x-jsrender">
<div class="">
    {{if message.length}}
        <p class="mb-1 preview-message pb-1">{{:message}}</p>
    {{/if}}
    {{if urlDetails.image}}
        <figure class="figure-img">
            <a href="{{:urlDetails.image}}" data-fancybox="gallery" data-toggle="lightbox" data-gallery="example-gallery" data-src="{{:urlDetails.image}}">
                <img src="{{:urlDetails.image}}" class="link-preview-image">
            </a>
        </figure>
    {{/if}}
    <h4>{{:urlDetails.title}}</h4>
    {{if urlDetails.description}}
        <p>{{:urlDetails.description}}</p>
    {{/if}}
    <p class="mb-0"><a href="{{:urlDetails.url}}" target="_blank">{{:urlDetails.url}}</a></p>
</div>






</script>

<script id="copyPastImgTmplt" type="text/x-jsrender">
    <div class="m-2 copy-img-ele">
        <img src="{{:url}}" class="img-thumbnail">
        <a class="remove-img copy-img-ele__remove-icon">&times;</a>
    </div>




</script>
