<script id="tmplConversationsList" type="text/x-jsrender">
    <div class="contact-area">
        <div class="chat__person-box" data-id="{{:contactId}}" id="user-{{:contactId}}" is-customer-chat="{{:isCustomerChat}}">
            <div class="position-relative chat__person-box-status-wrapper">
                {{if showStatus}}<div class="chat__person-box-status {{if is_online}} chat__person-box-status--online {{else}} chat__person-box-status--offline{{/if}}"></div>{{/if}}
                <div class="chat__person-box-avtar chat__person-box-avtar--active">
                    <img src="{{:contactDetail.photo_url}}" alt="<?php echo __('messages.person_image') ?>"
                         class="user-avatar-img">
                </div>
            </div>
            <div class="chat__person-box-detail">
                <div class="mb-1 chat__person-box-name contact-name d-flex">
                    <h5 class="mb-0 text-truncate">{{>contactDetail.name}}</h5>
                    <span class="badge badge-pill badge-primary chat-role-badge ml-2">{{:contactDetail.roles[0].name}}</span>
                </div>
                <p class="mb-0 chat-message">{{if !~getDraftMessage(contactId)}}{{:~getMessageByItsTypeForChatList(
        contact.message, contact.message_type, contact.file_name)}}{{else}}{{:~getDraftMessage(contactId) }}{{/if}}</p>
            </div>
            <div class="chat__person-box-msg-time">
                <div class="chat__person-box-time">{{:~getLocalDate(contact.created_at)}}</div>
                {{if contact.to_id == loggedInUserId}}
                <div class="chat__person-box-count {{if contact.unread_count <=
    0}} d-none {{/if}}">{{:contact.unread_count}}</div>
                {{/if}}
                <div class="dropdown">
                    <div class="chat-item-menu text-right pr-2" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-ellipsis-v hide-ele text-logo-color"></i>
                    </div>
                   <div class="dropdown-menu dropdown-menu-right more-btn-conversation-item">
                       <a class="dropdown-item text-center chat__person-box-delete more-delete-option">
                            <?php echo __('messages.common.delete') ?>
                        </a>
                        <a class="dropdown-item text-center chat__person-box-archive">
                            <?php echo __('messages.chats.archive_chat') ?>
                        </a>
                   </div>
                </div>
            </div>
        </div>
    </div>




</script>
