<script id="tmplUserDetails" type="text/x-jsrender">
    <div class="chat-profile__header">
        <span class="chat-profile__about"><?php echo trans('messages.about') ?></span>
        <i class="fa fa-times chat-profile__close-btn"></i>
    </div>
    <div class="chat-profile__person chat-profile__person--active mb-2">
        <div class="chat-profile__avatar">
            <img src="{{:photo_url}}" alt="" class="img-fluid user-about-image">
        </div>
    </div>
    {{if (is_online && !is_blocked)}}
    <div class="chat-profile__person-status my-3 text-capitalize">
            <?php echo __('messages.online') ?>
    </div>
    {{else}}

        {{if (!is_online && !is_blocked)}}
        <div class="chat-profile__person-last-seen">
            {{if last_seen !== '' && last_seen !== null}}
                <?php echo __('messages.chats.last_seen_at') ?> {{:~getCalenderFormatForLastSeen(last_seen)}}
            {{else}}
                <?php echo __('messages.chats.last_seen_at') ?>: <?php echo __('messages.chats.never') ?>
            {{/if}}
        </div>
        {{/if}}

    {{/if}}

    <div class="user-profile-data">
        <div class="chat-profile__divider"></div>
        <div class="chat-profile__column">
            <h6 class="chat-profile__column-title"><?php echo trans('messages.bio') ?></h6>
            <p class="chat-profile__column-title-detail text-muted mb-0 user-about">
                 {{if about}}
                    {{:about}}
                {{else}}
                    <?php echo __('messages.chats.no_bio_added_yet') ?>
                {{/if}}
            </p>
        </div>
            <div class="chat-profile__divider"></div>
            <div class="chat-profile__column">
                <h6 class="chat-profile__column-title"><?php echo trans('messages.phone') ?></h6>
                <p class="chat-profile__column-title-detail text-muted mb-0 user-phone">
                    {{if phone}}
                        {{:phone}}
                    {{else}}
                        <?php echo __('messages.chats.no_phone_added_yet') ?>
                    {{/if}}
                </p>
            </div>
            <div class="chat-profile__divider"></div>
            <div class="chat-profile__column">
                <h6 class="chat-profile__column-title"><?php echo trans('messages.common.email') ?></h6>
                <p class="chat-profile__column-title-detail text-muted mb-0 user-email">{{:email}}</p>
            </div>
    </div>
    <div class="group-profile-data">
        <div class="chat-profile__divider"></div>
    <input type="hidden" id="senderId" value={{:id}}>
    <!-- profile media and mute block section -->
    <div class="chat-profile__column chat-profile__column--media">
        <h6 class="chat-profile__column-title"><?php echo trans('messages.media') ?></h6>
        <div class="chat-profile__media-container">
           {{if media && media.length}}
                {{for media}}
                      {{:~prepareMedia(#data)}}
                {{/for}}
        {{else}}
            <span class="no-photo-found text-muted"><?php echo __('messages.chats.no_media_found') ?></span>
        {{/if}}
        </div>
    </div>
<!--    {{if !is_super_admin}}-->
<!--    <div class="chat-profile__column">-->
<!--        {{if is_blocked_by_auth_user}}-->
<!--        <div class="switch-checkbox chat-profile__switch-checkbox">-->
<!--            <input type="checkbox" id="switch" class="block-unblock-user-switch" checked/><label for="switch" class="mb-0 mr-2">Toggle</label>-->
<!--            <span class="chat-profile__column-title-detail text-muted mb-0 block-unblock-span">Unblock</span>-->
<!--        </div>-->
<!--        {{else}}-->
<!--              <div class="switch-checkbox chat-profile__switch-checkbox">-->
<!--            <input type="checkbox" id="switch" class="block-unblock-user-switch"/><label for="switch" class="mb-0 mr-2">Toggle</label>-->
<!--            <span class="chat-profile__column-title-detail text-muted mb-0 block-unblock-span">Block</span>-->
<!--        </div>-->
<!--        {{/if}}-->
<!--    </div>-->
<!--    <div class="chat-profile__divider"></div>-->
<!--   -->
<!--    {{/if}}-->
</div>



</script>
