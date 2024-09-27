<script id="tmplConversation" type="text/x-jsrender">
    <div class="chat-header">
        <div class="chat__area-header position-relative">
            <div class="d-flex justify-content-between align-items-center flex-1">
                <input type="hidden" id="toId" value="{{:toId}}">
                {{if fromId != ''}}
                <input type="hidden" id="fromId" value="{{:fromId}}">
                {{/if}}
                <input type="hidden" id="chatType" value="{{:user.id}}">

                <div class="d-flex">
                    <div class="chat__area-header-avatar">
                        <img src="{{:user.photo_url}}" alt="<?php echo __('messages.person_image') ?>" class="img-fluid chat-header-img">
                    </div>
                    <div class="pl-3 mb-2">
                        <h5 class="my-0 chat__area-title contact-title">{{>user.name}}
                        <span class="contact-title-status">
                        </span>
                        </h5>

                        <div class="typing position-relative {{if user.is_blocked}} d-none {{/if}}" >
                            {{if user.is_online}} <?php echo __('messages.online'); ?>
   {{else}} <?php echo __('messages.chats.last_seen_at'); ?>: {{:lastSeenTime}}{{/if}}
<!--                            <span class="chat__area-header-status"></span>-->

                            <span class="pl-3"><?php __('messages.online'); ?></span>

                        </div>
                        {{if user.assign == null && user.is_system == 0 && isloggedInUserAdmin }}
                               <a class="text-gray h6 font-weight-bold" data-toggle="modal" data-target="#assignChat" id="notAssignUserId" data-id="{{:user.id}}" href="#"><?php echo ' '.__('messages.ticket.assign_to_agent') ?></a>
                        {{/if}}
                       <span id="assignAgentNameLabel">
                           {{if user.assign != null && isloggedInUserAdmin}}<?php echo ' '.__('messages.ticket.assigned_to').': ' ?>
                           {{:user.assign.agent.name}}
                           {{/if}}
                       </span>
                        
                    </div>

                </div>

                <div class="chat__area-action">
                    <!-- setting view -->
                <div class="chat__area-icon open-profile-menu" data-toggle="tooltip" data-placement="bottom"
                                   title="<?php echo ' '.__('messages.settings') ?>">
                    <i class="fa fa-cog setting-icon" aria-hidden="true"></i>
                </div>
                </div>
                <div class="cursor-pointer d-xl-none"
                     id="dropdownMenuButton"  aria-expanded="false">
                    <i class="fa fa-bars open-profile-menu" aria-hidden="true"></i>
                </div>
            </div>
        </div>
        <div class="loading-message chat__area-header-loader d-none">
            <svg width="150px" height="75px" viewBox="0 0 187.3 93.7"
            preserveAspectRatio="xMidYMid meet">
            <path stroke="#00c6ff" id="outline" fill="none" stroke-width="5" stroke-linecap="round"
            stroke-linejoin="round" stroke-miterlimit="10"
            d="M93.9,46.4c9.3,9.5,13.8,17.9,23.5,17.9s17.5-7.8,17.5-17.5s-7.8-17.6-17.5-17.5c-9.7,0.1-13.3,7.2-22.1,17.1 -8.9,8.8-15.7,17.9-25.4,17.9s-17.5-7.8-17.5-17.5s7.8-17.5,17.5-17.5S86.2,38.6,93.9,46.4z"/>
            <path id="outline-bg" opacity="0.05" fill="none" stroke="#f5981c" stroke-width="5"
            stroke-linecap="round"
            stroke-linejoin="round" stroke-miterlimit="10"
            d="M93.9,46.4c9.3,9.5,13.8,17.9,23.5,17.9s17.5-7.8,17.5-17.5s-7.8-17.6-17.5-17.5c-9.7,0.1-13.3,7.2-22.1,17.1 -8.9,8.8-15.7,17.9-25.4,17.9s-17.5-7.8-17.5-17.5s7.8-17.5,17.5-17.5S86.2,38.6,93.9,46.4z"/>
            </svg>
        </div>
        <div class="chat-conversation" id="conversation-container"></div>
    </div>





</script>
