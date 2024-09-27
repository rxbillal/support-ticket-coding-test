<script id="tmplToday" type="text/x-jsrender">
<div class="chat__msg-day-divider d-flex justify-content-center">
    <span class="chat__msg-day-title">Today</span>
</div>


</script>

<script id="tmplSingleMessage" type="text/x-jsrender">
<div id="send-receive-direction" class="chat-conversation__sender" data-message_id="{{:randomMsgId}}">
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

<script id="tmplMessage" type="text/x-jsrender">
    <div id="send-receive-direction" class="{{:className}} message-{{:data.id}}" data-message_id="{{:data.id}}">
        <div class="chat-conversation__bubble clearfix {{if data.message_type !== 0 && isReceiver}} bubble-border-green {{/if}}" >
            <div class="chat-conversation__bubble-text message">
                {{:~displayMessage(data)}}
            </div>
            <div class="chat-container__time text-nowrap chat-time">{{:~getChatMagTimeInConversation(data.created_at)}}</div>
            <div class="chat-container__read-status position-absolute {{if isReceiver}} d-none {{else}} {{:readUnread}} {{/if}}">
                <i class="fa fa-check" aria-hidden="true"></i>
            </div>  
        </div>
     {{if isReceiver}}
    {{/if}}
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
