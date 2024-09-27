<script id="ticketReplyTemplate" type="text/x-jsrender">
   <div class="jumbotron jumbotron-fluid ticket-reply" data-remove-id="{{:id}}">
      <div class="row no-gutters">
          <div class="col-xl-1 col-lg-2 col-md-2 col-sm-12 col-12 text-center">
              <img class="reply-user-img" width="50px" height="50px" src="{{:photoUrl}}">
          </div>
          <div class="col-xl-11 col-lg-10 col-md-10 col-sm-12 col-10 pl-3">
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
