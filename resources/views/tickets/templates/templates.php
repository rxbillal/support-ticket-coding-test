<script id="agentActionTemplate" type="text/x-jsrender">
   <a  title="<?php echo __('messages.common.edit') ?>" class="btn btn-warning action-btn edit-btn" href="{{:url}}">
            <i class="fa fa-edit"></i>
   </a>
   <a  title="<?php echo __('messages.common.delete') ?>" class="btn btn-danger action-btn delete-btn" data-id="{{:id}}" href="#">
            <i class="fa fa-trash"></i>
   </a>  

</script>

<script id="ticketStatusActionTemplate" type="text/x-jsrender">
{{if !isTicketClosed}}
    <div class="dropdown d-inline mr-2">
        <button class="btn btn-{{:statusColor}} dropdown-toggle badge ticket-application-status" type="button" id="actionDropDown"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{:statusName}}</button>
        <div class="dropdown-menu">
            {{if statusCode == '<?php echo \App\Models\Ticket::STATUS_OPEN ?>'}}
            <a class="dropdown-item action-pause change-status" href="#" data-id="{{:id}}"
               data-status="<?php echo \App\Models\Ticket::STATUS_IN_PROGRESS ?>
    "><?php echo __('messages.common.in_progreess') ?></a>
            <a class="dropdown-item action-close change-status" href="#" data-id="{{:id}}"
               data-status="<?php echo \App\Models\Ticket::STATUS_CLOSED ?>"><?php echo __('messages.common.closed') ?></a>
            {{else statusCode == '<?php echo \App\Models\Ticket::STATUS_IN_PROGRESS ?>'}}
            <a class="dropdown-item action-open change-status" href="#" data-id="{{:id}}"
               data-status="<?php echo \App\Models\Ticket::STATUS_OPEN ?>"><?php echo __('messages.common.open') ?></a>
            <a class="dropdown-item action-close change-status" href="#" data-id="{{:id}}"
               data-status="<?php echo \App\Models\Ticket::STATUS_CLOSED ?>"><?php echo __('messages.common.closed') ?></a>
            {{/if}}
        </div>
    </div>
{{else}}
    <button class="btn btn-danger mr-1 badge ticket-application-status"><?php echo __('messages.common.closed') ?></button>
{{/if}}



</script>

<script id="ticketReplyTemplate" type="text/x-jsrender">
   <div class="jumbotron jumbotron-fluid ticket-reply" data-remove-id="{{:id}}">
      <div class="d-flex">
          <div>
              <img class="reply-user-img" width="50px" height="50px" src="{{:photoUrl}}">
          </div>
          <div class="ml-3 flex-1">
              <p class="mb-0">
                  <span class="reply-user-name">{{:userName}}</span><?php echo ' '.__('messages.common.replied') ?>
                  <span class="float-right ticket-action-btn">
                      <a href="javascript:void(0)" class="edit-reply text-warning" data-id="{{:id}}"><i class="mr-2 fa fa-edit"></i></a>
                      <a href="javascript:void(0)" class="del-reply text-danger" data-id="{{:id}}"><i class="fa fa-trash"></i></a>
                  </span>
              </p>
              <p class="mb-0 replyTime-{{:id}}">{{:updatedAt}}</p>
              <span class="reply-description description-{{:id}}">{{:description}}</span>
              
              <div id="editTicketReply-{{:id}}" class="d-none editReplyBox">
                  <div class="editReplyContainer" id="editReply-{{:id}}"></div>
                  <div class="text-left mt-3">
                      <button class="btn btn-primary" id="editTicketReply" data-loading-text="<span class='spinner-border spinner-border-sm'></span> Processing..."><?php echo __('messages.common.save') ?></button>
                       <a href="javascript:void(0)" class="btn btn-secondary text-dark cancelEditReply" id="cancelEditReply" data-id="{{:id}}"><?php echo __('messages.common.cancel') ?></a>
                  </div>
              </div>
          </div>
      </div>
  </div>




</script>
