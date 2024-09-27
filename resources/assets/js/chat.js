// import './create_group_livewire';

let chatSendArea = $.templates('#tmplChatSendArea').render();
let newMessageIndicator = $.templates('#tmplNewMsgIndicator').render();
let blockedMessageText = $.templates('#tmplBlockMsgText').render();
let blockedByMessageText = $.templates('#tmplBlockByMsgText').render();
let hdnTextMessage = $.templates('#tmplHiddenTxtMsg').render();
let totalUnreadConversations = 0;
let isCustomerChat = 0;

let unreadMessageIds = [];
let readMessageFunctionInterval = false;
let readedMessageIds = [];
let newConversationStartedUserIds = [];
let blockedUsersList = [];
// $.each(blockedUsersListObj, (index, val) => {
//     blockedUsersList.push(val);
// });

// we are loading 8000 messages at a time, so when real time messages are occurs,
// scroll is moved to bottom and then after?msg_id=123 api is calling which should be not called
// so this variable prevent that behaviour, and not call that api when realtime messages are coming
let callAfterAPI = true;
let callBeforeAPI = false;

let noConversationYet = false;
let noArchiveConversationYet = false;

let noConversationEle = $('.no-conversation');
let noArchiveConversationEle = $('.no-archive-conversation');
let noConversationYetEle = $('.no-conversation-yet');
let noArchiveConversationYetEle = $('.no-archive-conversation-yet');
noConversationEle.hide();
noArchiveConversationEle.hide();
noConversationYetEle.hide();
noArchiveConversationYetEle.hide();
noConversationEle.removeClass('d-none');

$(document).ready(function () {
    'use strict';
    const mediaTypeImage = 1;
    const mediaTypePdf = 2;
    const mediaTypeDocx = 3;
    const mediaTypeVoice = 4;
    const mediaTypeVideo = 5;
    const youtubeUrl = 6;
    const mediaTypeTxt = 7;
    const mediaTypeXls = 8;

    let selectedContactId = '';
    let selectedContactImg = '';
    let timer = null;
    let startTyping = true;
    let lastMessageIdForScroll = '';
    let limit = 20;
    let shouldCallApiTop = true;
    let shouldCallApiBottom = true;
    let placeCaret = true;
    let scrollAtLastMsg = false;
    let isAllMessagesRead = false;
    let appendGroupMessagesAtLast = true;
    let previousDate = [];
    let isDraftMsg = 0;
    let conversationMessages = [];
    let currentConversationGroupId = '';

    let chatPeopleBodyEle = $('#chatPeopleBody');
    let archivePeopleBodyEle = $('#archivePeopleBody');
    let noMsgesYet = $('.chat__not-selected');
    let membersCountArr = [];

    $('.no-chat-message').toggleClass('d-none ');

    $('#myTab a').on('hidden.bs.tab', function () {
    });

    $('input[type="radio"]').iCheck({
        checkboxClass: 'icheckbox_square',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%', // optional
    });

    //add a event listener to the window that calls preventDefault() on all dragover and drop events.
    window.addEventListener('dragover', function (e) {
        e = e || event;
        e.preventDefault();
    }, false);
    window.addEventListener('drop', function (e) {
        e = e || event;
        e.preventDefault();
    }, false);

    window.isInBlockedList = function (userId) {
        if (($.inArray(userId, blockedUsersList) != -1)) {
            return false;
        }

        return true;
    };

    $.ajax({
        type: 'GET',
        url: conversationListURL,
        success: function (data) {
            if (data.success) {
                $('.chat-infy-loader').hide();
                let latestConversations = data.data.conversations;
                if (latestConversations.length === 0) {
                    noConversationYetEle.show();
                    noConversationYet = true;
                }
                chatPeopleBodyEle.
                    append(latestConversations.map(prepareContacts).join(''));

                searchUsers();
                // loadTooltip();
            }
        },
        error: function (error) {
            console.log(error);
        },
    });

    //GET Archive conversations list
    $.ajax({
        type: 'GET',
        url: archiveConversationListURL,
        success: function (data) {
            if (data.success) {
                let archiveConversations = data.data.conversations;
                if (archiveConversations.length === 0) {
                    noArchiveConversationYetEle.show();
                    noArchiveConversationYet = true;
                }
                archivePeopleBodyEle.
                    append(archiveConversations.map(prepareContacts).join(''));

                archivePeopleBodyEle.find('.chat__person-box-archive').
                    each(function () {
                        $(this).text(Lang.get('messages.chats.un_archive'));
                    });

                searchUsers();
            }
        },
        error: function (error) {
            console.log(error);
        },
    });

    // bind click for recent chat user select
    let latestSelectedUser;
    let textMessageEle = $('#textMessage');

    $(document).on('click', '.chat__person-box', function (e) {
        isCustomerChat = $(this).attr('is-customer-chat');
        callBeforeAPI = false;
        $('.chat-conversation').html('');
        noConversationEle.hide();
        scrollAtLastMsg = false;
        callAfterAPI = true;
        previousDate = [];
        isDraftMsg = 0;
        $('.chat__person-box').removeClass('chat__person-box--active');
        $(this).addClass('chat__person-box--active');
        selectedContactId = $(e.currentTarget).data('id');
        let lastDraftMsg = getLocalStorageItem('user_' + selectedContactId);
        let urlDetail = userURL + selectedContactId + '/conversation';
        $.ajax({
            type: 'GET',
            url: urlDetail,
            data: {
                isCustomerChat: isCustomerChat,
            },
            success: function (data) {
                shouldCallApiTop = true;
                shouldCallApiBottom = true;
                callBeforeAPI = true;
                let lastMsg = data.data.conversations[data.data.conversations.length -
                1];
                lastMessageIdForScroll = (data.data.conversations.length !== 0)
                    ? lastMsg.id
                    : 0;
                latestSelectedUser = $('.chat__person-box--active').data('id');
                //put this (latestSelectedUser == data.user.id) condition bcz if responce come little late and user has already switch to another user than it shows data blink
                let userObject = data.data.user;
                if (data.success && latestSelectedUser === userObject.id) {
                    let conversations = data.data.conversations.reverse();
                    selectedContactId = userObject.id;
                    selectedContactImg = userObject.photo_url;
                    $('#user-' + selectedContactId).
                        find('.user-avatar-img').
                        attr('src', selectedContactImg);
                    let chatHeader = prepareChatHeader(userObject,
                        conversations);

                    let conversation = chatHeader;
                    /** Do not show chat input box if user is blocked */
                    if (!userObject.hasOwnProperty('is_blocked') ||
                        !userObject.is_blocked) {
                        conversation += chatSendArea;
                    } else {
                        conversation += hdnTextMessage;
                    }

                    // When User click on groups members then perform below action
                    if ($('.chat__people-body').
                        find('.chat__person-box--active').length <= 0) {
                        var isUserElePresent = $('.chat__people-body').
                            find('#user-' + userObject.id).length;
                        if (!isUserElePresent) {
                            let newUserEle = prepareNewConversation(
                                userObject.id,
                                userObject.name,
                                '',
                                userObject.photo_url,
                                userObject.is_online,
                                0,
                            );
                            chatPeopleBodyEle.prepend(newUserEle);
                            $('#user-' + userObject.id).
                                addClass('chat__person-box--active');
                        }
                    }

                    if (userObject.hasOwnProperty('is_super_admin') &&
                        userObject.is_super_admin) {
                        $('.chat-profile__switch-checkbox').addClass('d-none');
                    } else {
                        $('.chat-profile__switch-checkbox').
                            removeClass('d-none');
                    }

                    $('.chat__area-wrapper').html(conversation);

                    if (!userObject.hasOwnProperty('is_blocked') ||
                        userObject.is_blocked) {
                        $('.contact-title-status').hide();
                        $('.contact-status').hide();
                    }
                    if (
                        userObject.is_req_send_receive) {
                        let chatRequest = data.data.chat_request;
                        let chatReqObj = userObject;
                        chatReqObj.chat_req = chatRequest;
                        if (chatRequest.from_id == loggedInUserId) {
                            setSendChatReqTemplate(chatReqObj);
                            $('.chat__area-text').addClass('d-none');
                            $('.typing').addClass('d-none');
                            return false;
                        } else {
                            userObject.chat_req_id = chatRequest.id;
                            setReceiveChatReqTemplate(chatReqObj);
                            $('.chat__area-text').addClass('d-none');
                            $('.typing').addClass('d-none');
                            return false;
                        }
                    }
                    noConversationEle.hide();
                    let firstUnreadEle = $('.chat-conversation .unread').
                        first();
                    firstUnreadEle.before(newMessageIndicator);
                    fireReadMessageEvent(conversations);
                    if (firstUnreadEle.length > 0) {
                        scrollAtEle(firstUnreadEle);
                    } else {
                        shouldCallApiBottom = false;
                        let lastEle = $('.chat-conversation').children().last();
                        scrollAtEle(lastEle);
                    }
                    (data.data.conversations.length === 0)
                        ? addNoMessagesIndicator()
                        : '';
                    setUserProfileData(userObject, data.data.media);
                    getMessageByScroll();
                    setOpenProfileEvent();
                    setOpenMessageSearchEvent();
                    setOpenMsgInfoEvent();

                    textMessageEle = $('#textMessage');
                    textMessageEle.emojioneArea({
                        recentEmojis: false,
                        saveEmojisAs: 'shortname',
                        autocomplete: true,
                        textcomplete: {
                            maxCount: 15,
                            placement: 'top',
                        },
                        events: {
                            focus: function (editor) {
                                placeCaretAtEnd(editor[0]);
                            },
                        },
                    });
                    if (lastDraftMsg !== '' && lastDraftMsg !== null) {
                        isDraftMsg = 1;
                        textMessageEle[0].emojioneArea.setText(lastDraftMsg);
                        removeLocalStorageItem('user_' + selectedContactId);
                        $('#btnSend').
                            removeClass('chat__area-send-btn--disable');
                    }
                    textMessageEle.data('emojioneArea').setFocus();
                    sendMessage();

                    let groupMembersInfo = [];
                    conversationMessages = [];
                    currentConversationGroupId = '';
                    prepareMessageReadInfo(data.data.conversations, []);
                }

                if (getLocalStorageItem('reply')) {
                    let replyData = JSON.parse(getLocalStorageItem('reply'));
                    if (replyData.user_id == selectedContactId) {
                        prepareReplyBox(replyData);
                    }
                }

                if (data.data.user.is_blocked_by_auth_user) {
                    $('.chat__area-wrapper').append(blockedMessageText);
                } else if (data.data.user.is_blocked &&
                    !data.data.user.is_blocked_by_auth_user) {
                    $('.chat__area-wrapper').append(blockedMessageText);
                    $('.blocked-message-text span').
                        text('You are blocked by this user.');
                }
                
                if(totalUnreadConversations > 0){
                    updateUnreadMessageCount(1);
                } else {
                    updateUnreadMessageCount(0);
                }
                
                readNotificationWhenOpenChatWindow(selectedContactId);
                loadTooltip();
            },
            error: function (error) {
                console.log(error);
            },
        });

        $('.chat__area-wrapper').on('dragover', function (event) {
            event.preventDefault();
            event.stopPropagation();
            $('#fileUpload').modal('show');
        });
    });

    window.closeDropDown = function () {
        $(document).on('click', '.chat-profile', function () {
            let dropDownEle = $('#nav-group-members').
                find('[aria-expanded=\'true\']');
            if (dropDownEle.length) {
                dropDownEle.trigger('click');
            }
        });
    };

    window.prepareMessageReadInfo = function (messages, members) {
        $.each(members, function (index, val) {
            if (val.id != loggedInUserId) {
                let data = {
                    'user_id': val.id,
                    'name': val.name,
                    'photo_url': val.photo_url,
                };
                groupMembersInfo[val.id] = data;
            }
        });
        $.each(messages, function (index, val) {
            conversationMessages.push(val);
        });
    };

    window.checkReqAlreadySent = function (chatReqObj) {
        if (chatReqObj) {
            return (chatReqObj.status == 0) ? true : false;
        }
        return false;
    };

    window.checkReqAlreadyDeclined = function (chatReqObj) {
        if (chatReqObj) {
            return (chatReqObj.status == 2) ? true : false;
        }
        return false;
    };

    window.setSendChatReqTemplate = function (user) {
        let template = $.templates('#sendRequestTmpl');
        let myHelpers = {
            checkReqAlreadySent: checkReqAlreadySent,
        };
        let htmlOutput = template.render(user, myHelpers);

        $('#conversation-container').html(htmlOutput);
    };

    window.setReceiveChatReqTemplate = function (user) {
        let template = $.templates('#getChatRequestTmpl');
        let myHelpers = {
            checkReqAlreadyDeclined: checkReqAlreadyDeclined,
        };
        let htmlOutput = template.render(user, myHelpers);

        $('#conversation-container').html(htmlOutput);
    };

    $(document).on('click', '#sendChatRequest', function (e) {
        let userId = $(this).data('id');
        let data = {
            'to_id': userId,
            'message': $('#chatRequestMessage-' + userId).val(),
        };
        $.ajax({
            type: 'POST',
            url: sendChatReqURL,
            data: data,
            success: function (data) {
                if (data.success) {
                    displayToastr('success', 'success', data.message);
                    $('#chatRequestMessage-' + userId).val('');
                    $('.request__content-title').
                        text('You have send request to this user.');
                    $('.send__request__message').hide();
                }
            },
            error: function (error) {
                displayToastr('error', 'error', error.responseJSON.message);
            },
        });
    });

    $(document).on('click', '#acceptChatReq', function (e) {
        let chatReqId = $(this).data('id');
        let data = {
            'id': chatReqId,
        };
        $.ajax({
            type: 'POST',
            url: acceptChatReqURL,
            data: data,
            success: function (data) {
                if (data.success) {
                    displayToastr('success', 'success', data.message);
                    $('#user-' + data.data.from_id).trigger('click');
                }
            },
            error: function (error) {
                displayToastr('error', 'error', error.responseJSON.message);
            },
        });
    });

    $(document).on('click', '#declineChatReq', function (e) {
        let chatReqId = $(this).data('id');
        let data = {
            'id': chatReqId,
        };
        $.ajax({
            type: 'POST',
            url: declineChatReqURL,
            data: data,
            success: function (data) {
                if (data.success) {
                    displayToastr('success', 'success', data.message);
                    $('#user-' + data.data.from_id).
                        find('.chat__person-box-count').
                        addClass('d-none').
                        text(0);
                    $('#user-' + data.data.from_id).trigger('click');
                }
            },
            error: function (error) {
                displayToastr('error', 'error', error.responseJSON.message);
            },
        });
    });

    function placeCaretAtEnd (el) {
        if (!placeCaret) {
            return;
        }

        if (typeof window.getSelection != 'undefined'
            && typeof document.createRange != 'undefined') {
            var range = document.createRange();
            range.selectNodeContents(el);
            range.collapse(false);
            var sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
        } else if (typeof document.body.createTextRange != 'undefined') {
            var textRange = document.body.createTextRange();
            textRange.moveToElementText(el);
            textRange.collapse(false);
            textRange.select();
        }
    }

    $(document).on('click', '[data-toggle="lightbox"]', function (event) {
        event.preventDefault();
        $(this).ekkoLightbox();
    });

    $(document).on('click', '[data-toggle="media"]', function (event) {
        event.preventDefault();
        $(this).ekkoLightbox();
    });

    $('body').tooltip({
        selector: '.profile-media',
        placement: 'top',
        boundary: 'window',
        trigger: 'hover',
    });

    function setUserProfileData (user, media = []) {
        $('.chat-profile').empty();

        let template = $.templates('#tmplUserDetails');
        let myHelpers = {
            prepareMedia: prepareMedia,
            getCalenderFormatForLastSeen: getCalenderFormatForLastSeen,
            disabledIfReported: disabledIfReported,
        };
        user.media = media;

        let htmlOutput = template.render(user, myHelpers);

        $('.chat-profile').html(htmlOutput);
    }

    window.disabledIfReported = function (reportedUser) {
        if (reportedUser == null) {
            return '';
        }
        return 'disabled';
    };

    $(document).on('click', '#open-report-user-modal', function () {
        let userId = $(this).attr('data-id');
        $('#reportUserId').val(userId);
        $('#reportUserNote').val('');
        $('#reportUserValidationErrorsBox').hide().text('');
        $('#reportUserModal').modal('show');
    });

    $(document).on('keyup', '#reportUserNote', function () {
        $('#reportUserValidationErrorsBox').hide().text('');
    });

    $(document).on('click', '#reportUser', function () {
        let loadingButton = $(this);
        let userId = $('#reportUserId').val();
        let reportUserNote = $('#reportUserNote').val();
        if (reportUserNote === '') {
            $('#reportUserValidationErrorsBox').
                show().
                text('The notes field is required.');
            return false;
        }
        loadingButton.button('loading');
        let data = {
            'reported_to': userId,
            'notes': reportUserNote,
        };
        $.ajax({
            type: 'POST',
            url: reportUserURL,
            data: data,
            success: function (data) {
                $('#open-report-user-modal').prop('disabled', true)
                displayToastr(Lang.get('messages.success_message.success'),
                    'success', data.message)
                $('#reportUserModal').modal('hide')
            },
            error: function (data) {
                $('#reportUserValidationErrorsBox').
                    show().
                    text(data.responseJSON.message);
            },
            complete: function () {
                loadingButton.button('reset');
            },
        });
    });

    window.displaySideMedia = function (mediaObj) {
        $('.no-photo-found').hide();
        let mediaHtml = prepareMedia(mediaObj);
        $('.chat-profile__media-container').append(mediaHtml);
        $('[data-toggle="tooltip"]').tooltip();
    };

    // move chat conversation to last messages
    function scrollToLastMessage (isScrollLast = true) {
        scrollAtLastMsg = true;
        let chatConversation = $('.chat-conversation');
        let height = chatConversation.prop('scrollHeight');
        if (!isScrollLast) {
            height = height / 2;
        }
        chatConversation.scrollTop(height);
    }

    window.prepareMedia = function (data) {
        if (data.message_type === mediaTypeImage) {
            return imageRendererInSideMedia(data.message, data.id);
        } else if (data.message_type === mediaTypePdf) {
            return sideMediaRenderer(data.message, data.file_name,
                'fa-file-pdf-o', data.id);
        } else if (data.message_type === mediaTypeDocx) {
            return sideMediaRenderer(data.message, data.file_name,
                'fa-file-word-o', data.id);
        } else if (data.message_type === mediaTypeVideo) {
            return sideMediaRenderer(data.message, data.file_name,
                'fa-file-video-o', data.id);
        } else if (data.message_type === youtubeUrl) {
            return sideMediaRenderer(data.message, data.message,
                'fa fa-youtube-play', data.id);
        } else if (data.message_type === mediaTypeTxt) {
            return sideMediaRenderer(data.message, data.file_name,
                'fa-file-text-o', data.id);
        } else if (data.message_type === mediaTypeXls) {
            return sideMediaRenderer(data.message, data.file_name,
                'fa-file-excel-o', data.id);
        } else if (data.message_type === mediaTypeVoice) {
            return sideMediaRenderer(data.message, data.file_name,
                'fa-file-audio-o', data.id);
        } else {
            return '';
        }
    };

    function pluck (objs, name) {
        var sol = [];
        for (var i in objs) {
            if (objs[i].hasOwnProperty(name)) {
                // console.log(objs[i][name]);
                sol.push(objs[i][name]);
            }
        }
        return sol;
    }

    // move chat conversation to last messages
    function scrollToLastMessage (isScrollLast = true) {
        scrollAtLastMsg = true;
        let chatConversation = $('.chat-conversation');
        let height = chatConversation.prop('scrollHeight');
        if (!isScrollLast) {
            height = height / 2;
        }
        chatConversation.scrollTop(height);
    }

    window.scrollTop = function () {
        let ele = $('.chat-conversation');
        ele.scrollTop(50);
    };

    window.scrollAtEle = function (element) {
        if (element.length > 0) {
            let ele = $('.chat-conversation');
            let position = element.position();
            ele.scrollTop(position.top - 100);
        }
    };

    let chatProfileEle = $('.chat-profile');

    function setOpenProfileEvent () {
        $('.open-profile-menu, .chat-profile__close-btn').
            on('click', function (e) {
                profileToggle();
                if ($(this).parents('.dropdown-menu').length) {
                    $(this).parents('.dropdown-menu').removeClass('show');
                }
                //added this bcz after this, it will not consider document click event and will not close profile
                e.stopPropagation();
            });
    }

    $(document).on('click', '.ekko-lightbox', function (e) {
        // using this when we click anywhere (outside if image modal also), it will not consider document click event so profile and conversation-list sidebar will remain as it was before opening image
        // $(document).on('click', '.ekko-lightbox-nav-overlay', function (e) { // if want to do same with only arraow of image than use this class
        e.stopPropagation();
    });

    window.profileToggle = function () {
        if (chatProfileEle.hasClass('chat-profile--active')) {
            chatProfileEle.
                removeClass('chat-profile--active').
                addClass('chat-profile--out');
            setTimeout(() => {
                chatProfileEle.toggle();
            }, 300);
        } else {
            closeMsgInfo();
            chatProfileEle.
                addClass('chat-profile--active').
                removeClass('chat-profile--out').
                toggle();
        }
    };

    window.closeProfileInfo = function () {
        if (chatProfileEle.hasClass('chat-profile--active')) {
            chatProfileEle.
                removeClass('chat-profile--active').
                addClass('chat-profile--out');
            chatProfileEle.toggle();
        }
    };

    let msgInfoEle = $('.msg-info');
    window.setOpenMsgInfoEvent = function () {
        $('.open-msg-info').on('click', function (e) {
            let messageId = $(this).attr('data-message-id');
            setReadByMessageInfo(messageId);
            openMsgInfo();
            e.stopPropagation();
        });
        $('.msg-info__close-btn').on('click', function (e) {
            closeMsgInfo();
            e.stopPropagation();
        });
    };

    window.openMsgInfo = function () {
        closeProfileInfo();
        if (!msgInfoEle.hasClass('msg-info--active')) {
            msgInfoEle.
                addClass('msg-info--active').
                removeClass('msg-info--out').
                show();
        }
    };

    window.closeMsgInfo = function () {
        if (msgInfoEle.hasClass('msg-info--active')) {
            msgInfoEle.
                removeClass('msg-info--active').
                addClass('msg-info--out');
            msgInfoEle.hide();
        }
    };

    window.setReadByContactsInfo = function (messageId) {
        let messageInfo = getMsgInfoFromconversationMessages(messageId);
        if (messageInfo !== null) {
            showMessageInMessageInfo(messageInfo);
            showConversationInfo(messageInfo);
        }
    };

    window.setReadByMessageInfo = function (messageId) {
        let messageInfo = getMsgInfoFromconversationMessages(messageId);
        if (messageInfo !== null) {
            showSingleConversationInfo(messageInfo);
        }
    };

    window.checkReadAtDate = function (readAt) {
        return (readAt == null || readAt == '' || readAt ==
            '0000-00-00 00:00:00') ? false : true;
    };

    window.prepareReadByContactsInfoHtml = function (readByUsers) {
        let template = $.templates('#groupMsgReadUnreadInfo');
        let helpers = {
            getCalenderFormatForLastSeen: getCalenderFormatForLastSeen,
            checkReadAtDate: checkReadAtDate,
        };
        return template.render(readByUsers, helpers);
    };

    let remainingUsersEle = $('#remaining-users');
    let remainingUsersListEle = $('#remaining-users-list');
    let remainingUsersSectionEle = $('#remaining-users-section');
    let remainingUsersDividerEle = $('#remaining-users-divider');
    let readByUsersEle = $('#read-by-users');
    let readByUsersDivider = $('#read-by-users-divider');
    let readByUsersSection = $('#read-by-users-section');
    let singleMsgDivider = $('#single-msg-divider');
    let singleMsgSection = $('#single-msg-section');

    window.showConversationInfo = function (messageInfo) {
        let readByUsers = [];
        let remainingUsers = [];
        if (messageInfo !== null) {
            showMessageInMessageInfo(messageInfo);
            $.each(messageInfo.read_by, function (index, val) {
                val.user = groupMembersInfo[val.user_id];
                if (val.read_at != null) {
                    readByUsers.push(val);
                } else {
                    remainingUsers.push(val);
                }
            });
        }

        resetReadByUsers();
        if (remainingUsers.length > 0) {
            remainingUsersEle.text(remainingUsers.length + ' remaining').
                attr('data-remaining_count', remainingUsers.length);
            let remainingUsersHtml = prepareReadByContactsInfoHtml(
                remainingUsers);
            remainingUsersListEle.html(remainingUsersHtml);
            remainingUsersDividerEle.show();
            remainingUsersSectionEle.show();
        }

        readByUsersEle.attr('class',
            'message-' + messageInfo.id + '-read-by-users');
        readByUsersEle.html('');
        if (readByUsers.length > 0) {
            let readByUsersHtml = prepareReadByContactsInfoHtml(readByUsers);
            readByUsersEle.html(readByUsersHtml);
            readByUsersDivider.show();
            readByUsersSection.show();
        }
    };

    window.showSingleConversationInfo = function (messageInfo) {
        if (messageInfo !== null) {
            remainingUsersDividerEle.show();
            readByUsersDivider.show();
            singleMsgSection.show().html('');
            showMessageInMessageInfo(messageInfo);

            let helpers = {
                getCalenderFormatForLastSeen: getCalenderFormatForLastSeen,
            };
            let template = $.templates('#singleMessageReadInfoTmpl');
            let msgHtml = template.render(messageInfo, helpers);
            $('#single-msg-section').html(msgHtml);
        }
        resetSingleMessageReadByInfo();
    };

    window.resetSingleMessageReadByInfo = function () {
        remainingUsersSectionEle.hide();
        readByUsersSection.hide();
    };

    window.resetReadByUsers = function () {
        remainingUsersEle.text('').attr('data-remaining_count', 0);
        remainingUsersListEle.html('');
        remainingUsersDividerEle.hide();
        remainingUsersSectionEle.hide();

        readByUsersEle.html('');
        readByUsersDivider.hide();
        readByUsersSection.hide();

        singleMsgDivider.hide();
        singleMsgSection.hide().html('');
    };

    window.getMsgInfoFromconversationMessages = function (messageId) {
        let messageInfo = null;
        $.each(conversationMessages, function (index, val) {
            if (val.id == messageId) {
                messageInfo = val;
                return false;
            }
        });

        return messageInfo;
    };

    window.showMessageInMessageInfo = function (messageInfo) {
        let templateData = {};
        let helpers = {
            displayMessage: displayMessage,
            getChatMagTimeInConversation: getChatMagTimeInConversation,
        };
        let template = $.templates('#groupMsgReadUnreadMessage');
        templateData.data = messageInfo;
        templateData.loggedInUserId = loggedInUserId;
        let msgHtml = template.render(templateData, helpers);
        $('#msg-info-container-msg').html(msgHtml);
    };

    $(document).on('click', function () {
        //by clicking anywhere in document profile or chat side bar if any will present than it will close
        if (chatProfileEle.hasClass('chat-profile--active')) {
            chatProfileEle.
                removeClass('chat-profile--active').
                addClass('chat-profile--out');
            setTimeout(() => {
                chatProfileEle.toggle();
            }, 300);
        }

        $('.chat__people-wrapper-bar').
            addClass('fa-bars').
            removeClass('fa-times');
        $('.chat__people-wrapper').
            addClass('chat__people-wrapper--responsive');
    });

    $(document).on('click', '.chat-profile', function (e) {
        //to prevent click event of this class bcz by click of this class profile does not close
        e.stopPropagation();
    });

    $(document).on('click', '.chat__people-wrapper', function (e) {
        //to prevent click event of this class bcz by click of this class chat side bar does not close
        e.stopPropagation();
    });

    window.setOpenMessageSearchEvent = function () {
        // search msg opacity transition
        $(document).on('focus', '.chat__area-action-search-input', function () {
            $('.chat__search--conversation').css({ 'opacity': '1' });
        });

        // search msg opacity transition
        $('.chat__area-action-search-input').on('blur', function () {
            $('.chat__search--conversation').css({ 'opacity': '.5' });
        });

        // responsive search bar
        $('.open-search-bar').on('click', function () {
            $('.chat__area-action').addClass('chat__area-action--open');
            // height increased
            $('.chat__area-header').addClass('chat__area-header--active');
            $('.chat__area-action-search-input').focus();
        });

        // close search bar
        $('.chat__area-action-search-close').on('click', function () {
            $('.chat__area-action').removeClass('chat__area-action--open');
            // height initial
            $('.chat__area-header').removeClass('chat__area-header--active');
        });
    };

    //responsive chat side bar
    $('.chat__people-wrapper-bar').on('click', function (e) {
        $(this).toggleClass('fa-bars fa-times');
        $('.chat__people-wrapper').
            toggleClass('chat__people-wrapper--responsive');
        //added this bcz after this, it will not consider document click event and will not close chat side bar
        e.stopPropagation();
    });

    // responsive serach icon
    $('.chat__search-responsive-icon').on('click', function () {
        $('.chat__people-wrapper').
            removeClass('chat__people-wrapper--responsive');
        $('.chat__people-wrapper-bar').toggleClass('fa-bars fa-times');
        $('.chat__search-input').focus();
    });

    // hamburger menu
    $('#nav-icon3').on('click', function () {
        $(this).toggleClass('open');
        $('.chat__people-wrapper').
            toggleClass('chat__people-wrapper--responsive');
    });

    $('.chat__chat-contact-item').on('click', function () {
        $('.chat__chat-contact .chat__chat-contact-item').
            removeClass('chat__chat-contact--active');
        $(this).addClass('chat__chat-contact--active');
    });

    // init tooltip
    $('[data-toggle="tooltip"]').tooltip();

    let data = '';

    window.checkIsArchiveChat = function () {
        return $('.chat__person-box--active').
            parents('#archivePeopleBody').length;
    };

    window.moveConversationFromArchiveToActiveChat = function () {
        let chatEle = $('.chat__person-box--active');
        chatEle.find('.chat__person-box-archive').
            text(Lang.get('messages.chats.archive_chat'));
        $('#chatPeopleBody').prepend(chatEle);
        $('#archivePeopleBody').
            find('.chat__person-box--active').
            remove();
        makeActiveChatTabActive();
        setNoConversationYet();
    };

    function sendMessage () {
        let previousInput = '';
        let newInput = '';
        let toId = $('#toId').val();
        let fromId = $('#fromId').val();
        let isArchiveChat = checkIsArchiveChat();
        data = {
            'to_id': toId,
            '_token': csrfToken,
            'is_archive_chat': isArchiveChat,
        };
        if (fromId != undefined && fromId !== '') {
            data.from_id = fromId;
            data.send_by = loggedInUserId;
        }
        $('#btnSend').on('click', function () {
            let message = textMessageEle[0].emojioneArea.getText().trim();
            if (message === '') {
                return false;
            }
            $(this).addClass('chat__area-send-btn--disable');
            data.message = message;
            storeMessage(data);
            resetForm();
            removeLocalStorageItem('user_' + selectedContactId);
        });

        textMessageEle[0].emojioneArea.on('keyup emojibtn.click',
            function (btn, event) {
                let keyCode = event.keyCode || event.which;
                if (event.type == 'keyup' && keyCode !== 9) {
                    textMessageEle[0].emojioneArea.hidePicker();
                }
                let message = textMessageEle[0].emojioneArea.getText().trim();
                newInput = message;
                if (event && event.which === 13) {
                    if (newInput !== previousInput && !isDraftMsg) {
                        previousInput = newInput;
                        return false;
                    }
                    if (message.length === 0) {
                        $('#btnSend').addClass('chat__area-send-btn--disable');
                        return;
                    } else {
                        $('#btnSend').
                            removeClass('chat__area-send-btn--disable');
                        isTyping();
                    }
                    $('#btnSend').addClass('chat__area-send-btn--disable');
                    data.message = message;
                    storeMessage(data);
                    resetForm();
                    isDraftMsg = 0;
                    removeLocalStorageItem('user_' + selectedContactId);

                    return true;
                }
                isDraftMsg = 0;
                if (message.length === 0) {
                    $('#btnSend').addClass('chat__area-send-btn--disable');
                } else {
                    $('#btnSend').removeClass('chat__area-send-btn--disable');
                    isTyping();
                }
                previousInput = newInput;
            });

        textMessageEle[0].emojioneArea.on('blur', function (btn, event) {
            let message = textMessageEle[0].emojioneArea.getText().trim();
            if (message.length > 0) {
                setLocalStorageItem('user_' + toId, message);
            }
        });
    }

    function loadEojiArea () {
        textMessageEle = $('#textMessage');
        textMessageEle.emojioneArea({
            saveEmojisAs: 'shortname',
            autocomplete: true,
            textcomplete: {
                maxCount: 15,
                placement: 'top',
            },
            events: {
                focus: function focus (editor) {
                    placeCaretAtEnd(editor[0]);
                },
            },
        });

        textMessageEle.data('emojioneArea').setFocus();
    }

    window.storeMessage = function (reqData) {
        var Filter = require('bad-words'),
            filter = new Filter();

        reqData.message = filter.clean(reqData.message);

        let messageType = 0;
        if ($('.chat__text-preview').length > 0) {
            messageType = $('.chat__text-preview').data('message-type');
        }
        let randomMsgId = null;
        if (messageType == 0 && !reqData.file_name) {
            randomMsgId = addMessage(reqData);
        }
        let isArchiveChat = checkIsArchiveChat();
        reqData.is_archive_chat = isArchiveChat;
        if (isArchiveChat) {
            moveConversationFromArchiveToActiveChat();
        }

        $.ajax({
            type: 'POST',
            url: sendMessageURL,
            data: reqData,
            success: function (data) {
                reqData.reply_to = '';
                if (data.success === true) {
                    fireAddNewContactIdEvent(reqData.to_id);
                    let messageData = data.data.message;
                    conversationMessages.push(data.data.message);
                    $('.msg-options').
                        find('[data-message-id=' + randomMsgId + ']').
                        attr('data-message-id', messageData.id);
                    $('.msg-options').
                        find('[data-message-type=' + randomMsgId + ']').
                        attr('data-message-type', messageData.message_type);

                    let msgSetInWindow = false;
                    if (messageType != 0) {
                        msgSetInWindow = true;
                        setSentOrReceivedMessage(messageData);
                    }
                    if (messageData.message_type === 0) {
                        $('.chat-conversation').
                            find('[data-message_id=' + randomMsgId + ']').
                            addClass('message-' + messageData.id).
                            attr('data-message_id', messageData.id);
                        updateMessageInSenderwindow(messageData);

                        if (messageData.url_details) {
                            $('.message-' + messageData.id).
                                find('.message').
                                empty();
                            $('.message-' + messageData.id).
                                find('.chat-conversation__bubble.clearfix').
                                addClass('max-width-35');
                            $('.message-' + messageData.id).
                                find('.message').
                                append(displayMessage(messageData));
                        }
                    } else {
                        if (messageData.message_type === 6) {
                            $('.chat-conversation').
                                find('[data-message_id=' + randomMsgId + ']').
                                remove();
                        }
                        if (!msgSetInWindow) {
                            setSentOrReceivedMessage(messageData);
                        }
                    }

                    let toUserEle = chatPeopleBodyEle.find(
                        '#user-' + reqData.to_id);
                    addUserToTopOfConversation(reqData.to_id, toUserEle);
                    setOpenMsgInfoEvent();
                }
                let unreadMessageCount = getSelectedUserUnreadMsgCount(
                    reqData.to_id,
                );
                if (unreadMessageCount > 0) {
                    scrollToLastMessage();
                }
            },
            error: function (error) {
                reqData.reply_to = ''
                displayToastr(Lang.get('messages.error_message.error'), 'error',
                    error.responseJSON.message)
                console.log(error)
                $('#btnSend').removeClass('chat__area-send-btn--disable');
            },
        });
    };

    window.fireAddNewContactIdEvent = function (userId) {
        if ($.inArray(userId, newConversationStartedUserIds) != -1) {
            window.livewire.emit('addNewContactId', userId);
            newConversationStartedUserIds = jQuery.grep(
                newConversationStartedUserIds, function (value) {
                    return value != userId;
                });
        }
    };

    function addMessage (data) {
        let messageData = data;
        messageData.message = data.message.replace(/(<([^>]+)>)/ig, '');
        let currentTime = moment().tz(timeZone).format('hh:mma');
        if (isUTCTimezone == '1') {
            currentTime = getLocalDate(moment().utc());
        }
        messageData.time = currentTime;
        messageData.senderName = authUserName;
        messageData.senderImg = authImgURL;
        messageData.message = getMessageByItsTypeForChatList(
            messageData.message, 0);
        let randomMsgId = Math.floor(Math.random() * 6) + Date.now();
        messageData.randomMsgId = randomMsgId;

        if ($('.chat__text-preview').length > 0) {
            messageData.receiverName = $('.chat__text-preview').
                find('.reply-to-user').
                text();
        } else {
            messageData.receiverName = '';
        }

        let template = $.templates('#tmplSingleMessage');
        let htmlOutput = template.render(messageData);
        let getLastTime = $('.chat__person-box--active').
            find('.chat__person-box-time').text();

        // Append today's timeline
        let todaysTimeLine = $.templates('#tmplToday');
        if (getLastTime == 'Yesterday' || moment(getLastTime).isValid()) {
            $('.chat__person-box--active').
                find('.chat__person-box-time').text(messageData.time);
            $('.chat-conversation').append(todaysTimeLine);
        } else if (!getLastTime) {
            $('.chat-conversation').append(todaysTimeLine);
            $('.chat__person-box--active').
                find('.chat__person-box-time').
                text(getLocalDate(moment().utc()));
        }

        $('.chat-conversation').append(htmlOutput);
        $('.chat__not-selected').hide();
        scrollToLastMessage();
        setOpenMsgInfoEvent();

        return randomMsgId;
    }

    window.getSelectedUserUnreadMsgCount = function (userId) {
        return $('#user-' + userId).find('.chat__person-box-count').text();
    };

    window.setSentOrReceivedMessage = function (message) {
        let recentChatMessage = prepareChatConversation(message, false);

        let chatConversationEle = $('.chat-conversation');
        let needToScroll = false;
        displaySideMedia(message);

        if (message.from_id == loggedInUserId) {
            //at sender side
            if (isAllMessagesRead) {
                chatConversationEle.append(recentChatMessage);
            }
            scrollToLastMessage();
        } else {
            if (isAllMessagesRead || appendGroupMessagesAtLast) {
                let needToScrollCondition = add(chatConversationEle.scrollTop(),
                        chatConversationEle.innerHeight()) >=
                    (chatConversationEle[0].scrollHeight - 3);
                needToScroll = needToScrollCondition ? true : false;
                chatConversationEle.append(recentChatMessage);
                if (needToScroll) {
                    scrollToLastMessage();
                    // fireReadMessageEvent([message]);
                } else {
                    let unreadMessageCountEle = $(
                        '#user-' + latestSelectedUser).
                        find('.chat__person-box-count');
                    let unreadMessageCount = unreadMessageCountEle.text();
                    unreadMessageCount = add(unreadMessageCount, 1);
                    unreadMessageCountEle.text(unreadMessageCount);
                    unreadMessageCountEle.removeClass('d-none');
                    let newMsgBadge = chatConversationEle.find(
                        '.chat__msg-day-new-msg');
                    if (newMsgBadge.length === 0) {
                        let firstUnreadEle = $('.message-' + message.id);
                        firstUnreadEle.before(newMessageIndicator);
                    }
                }
            }
        }

        $('.chat__not-selected').hide();
        noConversationEle.hide();

        updateMessageInSenderwindow(message);

        //update in reciever's conversation
        let userEle = chatPeopleBodyEle.find('#user-' + message.from_id);
        userEle.find('.chat-message').
            html(getMessageByItsTypeForChatList(message.message,
                message.message_type, message.file_name));
        userEle.find('.chat__person-box-time').
            text(getLocalDate(message.created_at));

        loadTooltip();
    };

    window.updateMessageInSenderwindow = function (message) {
        //update in logged in user's conversation
        let userEle = chatPeopleBodyEle.find('#user-' + message.to_id);
        userEle.find('.chat-message').
            html(getMessageByItsTypeForChatList(message.message,
                message.message_type, message.file_name));
        userEle.find('.chat__person-box-time').
            text(getLocalDate(message.created_at));
    };

    function resetForm () {
        textMessageEle[0].emojioneArea.setText('');
    }

    function add (num1, num2) {
        return parseInt(num1) + parseInt(num2);
    }

    function messageReadAfter5Seconds () {
        let newIds = [];
        // prepare unique array of unread messages
        $.each(unreadMessageIds, function (i, v) {
            if ($.inArray(v, newIds) === -1 &&
                $.inArray(v, readedMessageIds) === -1) {
                newIds.push(v);
            }
        });

        readedMessageIds = $.merge(readedMessageIds, newIds);

        if (newIds.length <= 0) {
            return;
        }

        unreadMessageIds = []; // make unread message ids empty
        let senderId = $('.chat__person-box--active').data('id');

        $.ajax({
            type: 'post',
            url: readMessageURL,
            data: {
                ids: newIds,
                '_token': csrfToken,
            },
            success: function (data) {
                $.each(newIds, function (index, value) {
                    $('.message-' + value).removeClass('unread');
                });
                if (data.success == true) {
                    let remainingUnread = data.data.remainingUnread;

                    // TODO: do not update unread messages count when chat window is  open
                    let UnreadCountEle = $('#user-' + senderId).
                        find('.chat__person-box-count');
                    let totalUnread =  UnreadCountEle.text();
                    UnreadCountEle.text(remainingUnread);
                    let sidebarMessageCount = $('#sidebar-message-count').text();
                    if (sidebarMessageCount > 0) {
                        $('#sidebar-message-count').html(parseInt($('#sidebar-message-count').html()) - totalUnread);
                    }

                    if (remainingUnread > 0) {
                        isAllMessagesRead = false;
                        // TODO : do not scroll again and again
                        // Scroll to minor top from bottom so don't need to again scroll bottom for load new messages
                        // $('.chat-conversation').scrollTop(
                        //     $('.chat-conversation').scrollTop() - 100);
                    } else {
                        UnreadCountEle.text(0).addClass('d-none');
                        setTimeout(function () {
                            $('.chat__msg-day-new-msg').parent().remove();
                        }, 20000);
                        scrollAtLastMsg = false;
                        isAllMessagesRead = true;
                    }
                }
            },
        });
    }

    window.getUnreadMessageIds = function (conversations) {
        let ids = [];
        $.each(conversations, function (index, conversation) {
            if (conversation.to_id == loggedInUserId && !conversation.status) {
                ids.push(conversation.id);
            }
        });

        return ids;
    };

    window.getUnreadGroupMessageIds = function (conversations, limit = 10) {
        let ids = [];
        let count = 1;
        if (conversations.length > limit) {
            return ids;
        }

        $.each(conversations, function (index, conversation) {
            if (count <= limit &&
                $('.message-' + conversation.id).hasClass('unread')) {
                ids.push(conversation.id);
            }
            count += 1;
        });

        return ids;
    };

    window.fireReadMessageEvent = function (conversations) {
        let unreadMessageIds = getUnreadMessageIds(conversations);
        fireReadMessageEventUsingIds(unreadMessageIds);
    };

    window.fireReadMessageEventUsingIds = function (ids) {
        if (ids.length > 0) {
            // Store unread message ids into global variables
            unreadMessageIds = $.merge(unreadMessageIds, ids);

            // Now call the read message looping function which will check unread message at each 5 seconds
            if (!readMessageFunctionInterval) {
                let interval = setInterval(messageReadAfter5Seconds, 5000); // readMessageFunctionInterval = true;
                readMessageFunctionInterval = true;
            }
        } else {
            isAllMessagesRead = true;
        }
    };

    window.getMessageByItsTypeForChatList = function (
        message, message_type, file_name = '') {
        if (message_type === mediaTypeImage) {
            return '<i class="fa fa-camera" aria-hidden="true"></i>' + ' Photo';
        } else if (message_type === mediaTypePdf) {
            return '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>' + ' ' +
                file_name;
        } else if (message_type === mediaTypeDocx) {
            return '<i class="fa fa-file-word-o" aria-hidden="true"></i>' +
                ' ' + file_name;
        } else if (message_type === mediaTypeVoice) {
            return '<i class="fa fa-file-audio-o" aria-hidden="true"></i>' +
                ' ' + file_name;
        } else if (message_type === mediaTypeVideo) {
            return '<i class="fa fa-file-video-o" aria-hidden="true"></i>' +
                ' ' + file_name;
        } else if (message_type === mediaTypeTxt) {
            return '<i class="fa fa-file-text-o" aria-hidden="true"></i>' +
                ' ' + file_name;
        } else if (message_type === mediaTypeXls) {
            return '<i class="fa fa-file-excel-o" aria-hidden="true"></i>' +
                ' ' + file_name;
        } else {
            return emojione.shortnameToImage(message);
        }
    };

    window.getDraftMessage = function (contactId) {
        let lastDraftMsg = getLocalStorageItem('user_' + contactId);

        return (lastDraftMsg != null)
            ? '<i class="fa fa-pencil" aria-hidden="true"></i> ' +
            getMessageByItsTypeForChatList(lastDraftMsg, 0)
            : false;
    };

    window.prepareContacts = function (contact) {
        let contactDetail = contact.user;
        let contactId = contact.user_id;
        let showStatus = true;
        let showUserStatus;
        let isChatByCustomer = 0;

        if (contactDetail.is_system == 0) {
            isChatByCustomer = 1;
        }
        if (contact.send_by == loggedInUserId) {
            contactDetail = contact.receiver;
            contactId = contact.receiver.id;
            isChatByCustomer = 1;
        }
        let template = $.templates('#tmplConversationsList');
        let helpers = {
            getMessageByItsTypeForChatList: getMessageByItsTypeForChatList,
            getLocalDate: getLocalDate,
            getDraftMessage: getDraftMessage,
        };

        let data = {
            showStatus: showStatus,
            showUserStatus: showUserStatus,
            contactId: contactId,
            contact: contact,
            contactDetail: contactDetail,
            is_online: contact.user.is_online,
            isCustomerChat: isChatByCustomer,
            loggedInUserId: loggedInUserId,
        };
        let contactElementHtml = template.render(data, helpers);

        if (contact.unread_count > 0) {
            totalUnreadConversations += 1;
            updateUnreadMessageCount(0);
        }

        return contactElementHtml;
    };

    //add latest messaged user to top of conversation
    window.addUserToTopOfConversation = function (userId, userEle) {
        chatPeopleBodyEle.remove('#user-' + userId);
        chatPeopleBodyEle.prepend(userEle);
    };

    window.Echo.private(`user.${loggedInUserId}`).
        listen('UserEvent', (e) => {
            if (e.type === 1) { // block-unblock user event
                blockUnblockUserEvent(e);
            } else if (e.type === 2) { // new user-to-user message arrived
                newMessageArrived(e);
                window.livewire.emit('clearSearchUsers');
            } else if (e.type === 4) { // private message read
                privateMessageReadByUser(e);
            } else if (e.type === 5) { // message deleted for everyone
                messageDeletedForEveryone(e);
            } else if (e.type === 6 && (getCurrentUserId() === e.owner_id)) {
                readNotificationWhenChatWindowOpen(e.id,
                    '#owner-' + e.owner_id);
            } else if (e.type === 7) {
                newMessageArrived(e);
            }
        });
    
    function messageDeletedForEveryone (e) {
        let messageEle = $('.message-' + e.id);
        removeTimeline(messageEle);
        updateMessageOnReceiverDrawer(e.previousMessage, messageEle, e);

        if (chatPeopleBodyEle.find('#user-' + e.from_id).length) {
            // if chat window is open
            $('.message-' + e.id).remove();
        }
    }

    function updateMessageOnReceiverDrawer (
        previousMessage, messageEle, delMsgInfo) {
        let userEle = $(document).find('#user-' + delMsgInfo.from_id);
        if (previousMessage != null && messageEle.nextAll(
            '#send-receive-direction:first').length === 0) {
            let chatPersonBox = userEle;
            let oldMsgCount = userEle.find('.chat__person-box-count').text();
            oldMsgCount = parseInt(oldMsgCount);

            if (oldMsgCount > 1) {
                let msgCount = oldMsgCount - 1;
                chatPersonBox.find('.chat__person-box-count').html(msgCount);
            } else {
                chatPersonBox.find('.chat__person-box-count').
                    html(0).
                    removeClass('d-none').
                    addClass('d-none');
            }

            chatPersonBox.find('.chat-message').
                html(getMessageByItsTypeForChatList(
                    previousMessage.message,
                    previousMessage.message_type,
                    previousMessage.file_name));
            chatPersonBox.find('.chat__person-box-time').
                text(getLocalDate(previousMessage.created_at));
        } else if (delMsgInfo.previousMessage == null && userEle.length > 0) {
            userEle.find('.chat-message').html('');
            userEle.find('.chat__person-box-count').text(0).addClass('d-none');
        }
        checkAllMsgAndShowNoMsgYet();
    }

    function privateMessageReadByUser (e) {

        let readClass = 'chat-container__read-status--read';
        let unreadClass = 'chat-container__read-status--unread';
        $.each(e.ids, function (i, v) {
            $('.message-' + v).
                find('.chat-container__read-status').
                removeClass(unreadClass).
                addClass(readClass);

            updateReadMessageInfo(v, e.user_id);
        });
    }

    window.updateReadMessageInfo = function (messageId, userId) {
        if (selectedContactId != userId) {
            return false;
        }
        $.each(conversationMessages, function (index, messageInfo) {
            if (messageInfo.id == messageId) {
                messageInfo.status = 1;
                messageInfo.updated_at = moment.utc().
                    format('YYYY-MM-DD hh:mm:ss');
                let messageReadAtEle = $('#msg-read-at-' + messageId);
                if (messageReadAtEle.length > 0) {
                    messageReadAtEle.text(
                        getCalenderFormatForLastSeen(messageInfo.updated_at));
                }
                return false;
            }
        });
    };

    function blockUnblockUserEvent (e) {
        let currentUserId = $('.chat__person-box--active').data('id');
        if (loggedInUserId != e.blockedTo.id) {
            return;
        }

        if (!e.isBlocked && currentUserId == e.blockedBy.id) {
            $('.typing').show();
            $('#user-' + currentUserId).
                find('.chat__person-box-status').
                show();
            $('.chat-profile__person-status').show();
            $('.chat__area-wrapper').append(chatSendArea);
            $('.hdn-text-message').remove();
            $('.blocked-message-text').remove();
            loadEojiArea();
            sendMessage();
            removeValueFromArray(blockedUsersList, currentUserId);
        } else if (e.isBlocked) {
            if (currentUserId == e.blockedBy.id) {
                $('.chat__area-text').remove();
                $('.blocked-message-text').remove();
                $('.typing').hide();
                $('.chat__area-wrapper').
                    append(blockedByMessageText);
            }

            $('#user-' + currentUserId).
                find('.chat__person-box-status').
                hide();
            $('.chat-profile__person-status').hide();
            blockedUsersList.push(e.blockedBy.id);
        }
    }

    function newMessageArrived (e) {
        let isArchiveChat = archivePeopleBodyEle.find(
            '#user-' + e.from_id).length;
        if ($.inArray(selectedContactId, [e.from_id, e.to_id]) >= 0) {
            fireReadMessageEventUsingIds([e.id]);
            callAfterAPI = false;
            moveConversationFromArchiveToActiveChat();
            //already chat window is open whoes message has arrive
            setSentOrReceivedMessage(e);
            let fromUser = chatPeopleBodyEle.find('#user-' + e.from_id);
            let toUser = chatPeopleBodyEle.find('#user-' + e.to_id);
            if (fromUser.length) {
                addUserToTopOfConversation(e.from_id, fromUser);
            } else if (toUser.length) {
                addUserToTopOfConversation(e.to_id, toUser);
            }
            moveConversationFromArchiveToActiveChat();
        } else if (chatPeopleBodyEle.find('#user-' + e.from_id).length) {
            //chat window is not open so update message count
            if (!e.status) {
                $('#sidebar-message-count').html(parseInt($('#sidebar-message-count').html()) + 1);
            }
            let userEle = chatPeopleBodyEle.find('#user-' + e.from_id);
            let oldMsgCount = userEle.find('.chat__person-box-count').
                text();
            oldMsgCount = (isNaN(oldMsgCount) || oldMsgCount === '')
                ? 0
                : oldMsgCount;
            if (oldMsgCount == 0) {
                totalUnreadConversations += 1;
                updateUnreadMessageCount(0);
            }
            let newMsgCount = add(oldMsgCount, 1);
            userEle.find('.chat__person-box-count').removeClass('d-none');
            userEle.find('.chat__person-box-count').
                text(newMsgCount).
                show();
            userEle.find('.chat-message').
                html(getMessageByItsTypeForChatList(e.message,
                    e.message_type, e.file_name));
            userEle.find('.chat__person-box-time').
                text(getLocalDate(e.created_at));
            addUserToTopOfConversation(e.from_id, userEle);
        } else {
            if (!e.status) {
                totalUnreadConversations += 1;
                updateUnreadMessageCount(0);
            }
            //user not exist in chat-list so start new conversation
            let isChatByCustomer = 0;
            if (e.send_by == loggedInUserId) {
                isChatByCustomer = 1;
            }
            let newUserEle = prepareNewConversation(e.from_id, htmlSpecialCharsDecode(e.sender.name), e, e.sender.photo_url, '', isChatByCustomer);
            chatPeopleBodyEle.prepend(newUserEle);
            let userEle = chatPeopleBodyEle.find('#user-' + e.from_id);
            userEle.find('.chat__person-box-status').
                removeClass('chat__person-box-status--offline').
                addClass('chat__person-box-status--online');
            if (e.status) {
                userEle.find('.chat__person-box-count').
                    text(0).
                    show();
                userEle.find('.chat__person-box-count').addClass('d-none');
            }
            noConversationEle.hide();
            noConversationYetEle.hide();
            archivePeopleBodyEle.find('#user-' + e.from_id).remove();
        }
        if (isArchiveChat) {
            makeConversationArchiveWhenMessageArrive(e.from_id);
            makeActiveChatTabActive();
            showNoArchiveConversationEle();
        }
    }

    window.makeConversationArchiveWhenMessageArrive = function (userId) {
        $.ajax({
            type: 'get',
            url: deleteConversationUrl + userId + '/archive-chat',
            success: function () {
                showNoArchiveConversationEle();
            },
        });
    };

    window.Echo.private(`chat`).
        listenForWhisper(`start-typing.${loggedInUserId}`, (e) => {
            if (latestSelectedUser == e.user.id) {
                let userTyping = e.user.name + ' Typing...';
                $('.typing').html(userTyping).show();
            }
        }).listenForWhisper(`stop-typing.${loggedInUserId}`, (e) => {
        if (latestSelectedUser == e.user.id) {
            $('.typing').html('online').show();
        }
    });

    Echo.join(`user-status`).here((users) => {
        setTimeout(function () {
            $.each(users, function (index, user) {
                updateUserStatus(user.id, 1);
            });
        }, 1000);
    }).joining((user) => {
        updateUserStatus(user.id, 1);
    }).leaving((user) => {
        updateUserStatus(user.id, 0);
    });

    window.prepareChatHeader = function (user, conversations) {
        /** If user is blocked then do not show last seen */
        let lastSeenTime = (user.last_seen !== null && user.last_seen != '')
            ? getCalenderFormatForLastSeen(user.last_seen)
            : Lang.get('messages.chats.never');

        let toId = false;
        let fromId = '';
        conversations.map((conversation) => {
            if (toId == false && isCustomerChat == 1 && isloggedInUserAdmin) {
                toId = conversation.receiver.id;
                fromId = conversation.sender.id;
            }
        });

        let template = $.templates('#tmplConversation');
        var $htmlOutput = $(template.render({
            user: user,
            toId: toId == false ? user.id : toId,
            fromId: fromId,
            isloggedInUserAdmin: isloggedInUserAdmin,
            lastSeenTime: lastSeenTime,
        }));

        let messageConversation = $htmlOutput.find('.chat-conversation');
        messageConversation.html('');
        if (conversations.length !== null) {
            messageConversation.html(
                conversations.map(prepareChatConversation).join(''));
        }

        return $htmlOutput.html();
    };

    window.prepareChatConversation = function (
        data, needToRemoveOldTimeline = true) {
        if (data.message_type === 9) {
            let timeLineEle = addTimeLineEle(
                data.created_at,
                needToRemoveOldTimeline,
            );

            let template = $.templates('#tmplMessageBadges');
            let helpers = { getLocalDate: getLocalDate };
            return timeLineEle + template.render(data, helpers);
        }

        if ($.inArray(needToRemoveOldTimeline, [true, false]) === -1) {
            needToRemoveOldTimeline = true;
        }

        let timeLineEle = addTimeLineEle(
            data.created_at,
            needToRemoveOldTimeline,
        );

        let isReceiver = false;
        let isSendByLoginUser = data.send_by == loggedInUserId &&
            isloggedInUserAdmin;
        let className;
        if (data.send_by != null && !isloggedInUserAdmin) {
            className = (!data.status)
                ? 'chat-conversation__receiver unread'
                : 'chat-conversation__receiver';
        } else {
            if (data.from_id == loggedInUserId || isSendByLoginUser) {
                className = 'chat-conversation__sender';
            } else {
                className = (!data.status)
                    ? 'chat-conversation__receiver unread'
                    : 'chat-conversation__receiver';
            }
        }

        let readUnread = (data.status == 1 ||
            (data.hasOwnProperty('read_by_all_count') &&
                data.read_by_all_count === 0))
            ? 'chat-container__read-status--read'
            : 'chat-container__read-status--unread';

        if (className.includes('chat-conversation__receiver')) {
            isReceiver = true;
        }

        let allowToDelete = true;
        let deleteMsgForEveryone = true;
        if (data.time_from_now_in_min > messageDeleteTime) {
            allowToDelete = false;
        }

        if (data.time_from_now_in_min > deleteMsgForEveryone) {
            deleteMsgForEveryone = false;
        }

        let templateData = {};
        let helpers = {
            displayMessage: displayMessage,
            getChatMagTimeInConversation: getChatMagTimeInConversation,
        };
        let template = $.templates('#tmplMessage');
        templateData.data = data;
        templateData.isReceiver = isReceiver;
        templateData.loggedInUserId = loggedInUserId;
        templateData.authImage = $.parseHTML(authImgURL)[0].data;
        if (data.send_by == loggedInUserId) {
            templateData.senderImage = data.sender.photo_url;
        } else {
            templateData.senderImage = (data.send_by == null)
                ? data.sender.photo_url
                : data.send_by_user.photo_url;
        }
        templateData.senderName = data.sender.name;
        templateData.senderIsAdmin = (data.send_by_user == null)
            ? 0
            : (data.send_by_user.roles[0].name == 'Admin');
        templateData.authUserName = authUserName;
        templateData.needToRemoveOldTimeline = needToRemoveOldTimeline;
        templateData.className = className;
        templateData.readUnread = readUnread;
        templateData.allowToDelete = allowToDelete;
        templateData.deleteMsgForEveryone = deleteMsgForEveryone;

        return timeLineEle + template.render(templateData, helpers);
    };

    window.displayMessage = function (data) {
        if (data.message_type === mediaTypeImage) {
            return imageRenderer(data.message);
        } else if (data.message_type === mediaTypePdf) {
            return fileRenderer(data.message, data.file_name, pdfURL);
        } else if (data.message_type === mediaTypeDocx) {
            return fileRenderer(data.message, data.file_name, docsURL);
        } else if (data.message_type === mediaTypeVideo) {
            return videoRenderer(data.message);
        } else if (data.message_type === youtubeUrl) {
            return renderYoutubeURL(data.message);
        } else if (data.message_type === mediaTypeTxt) {
            return fileRenderer(data.message, data.file_name, textURL);
        } else if (data.message_type === mediaTypeXls) {
            return fileRenderer(data.message, data.file_name,
                xlsURL);
        } else if (data.message_type === mediaTypeVoice) {
            return voiceRenderer(data.message, data.file_name);
        } else {
            if (checkYoutubeUrl(data.message) === youtubeUrl) {
                return renderMultipleYouTubeUrl(data);
            }
            if (data.url_details) {
                let records = {
                    urlDetails: data.url_details,
                    message: data.message,
                };
                return $.templates('#tmplLinkPreview').render(records);
            }

            return emojione.shortnameToImage(
                detectUrlFromTextMessage(data.message));
        }
    };

    window.checkYoutubeUrl = function (message) {
        let youtubeLink = 'youtube.com/watch?v=';
        if (message.indexOf(youtubeLink) != -1) {
            return youtubeUrl;
        }
        return 0;
    };

    window.findUrls = function (text) {
        let source = (text || '').toString();
        let urlArray = [];
        let matchArray;

        // Regular expression to find FTP, HTTP(S) and email URLs.
        let regexToken = /(((ftp|https?):\/\/)[\-\w@:%_\+.~#?,&\/\/=]+)|((mailto:)?[_.\w-]+@([\w][\w\-]+\.)+[a-zA-Z]{2,3})/g;

        // Iterate through any URLs in the text.
        while ((matchArray = regexToken.exec(source)) !== null) {
            let token = matchArray[0];
            urlArray.push(token);
        }

        return urlArray;
    };

    window.renderMultipleYouTubeUrl = function (data) {
        let messageClassName = (data.from_id != loggedInUserId)
            ? 'float-right'
            : 'float-left';
        let rendererClassName = (data.from_id != loggedInUserId)
            ? 'mr-2'
            : 'float-right ml-2';
        let urls = findUrls(data.message);
        let message = '';
        $.each(urls, (index, url) => {
            message += renderYoutubeURL(url, rendererClassName);
        });
        return message +
            '<div class="d-inline-block ' + messageClassName +
            ' mx-1" style="max-width: 500px;">' +
            data.message +
            '</div>';
    };

    window.addTimeLineEle = function (
        created_at, needToRemoveOldTimeline = true) {
        let timelineDate = getCalenderFormatForTimeLine(created_at);
        let timelineDateClass = (timelineDate.split(' ')).join('_').
            replace(',', '');
        let timeLineEle = '';
        let timeLineEleContent = '<div class="chat__msg-day-divider d-flex justify-content-center ' +
            timelineDateClass + '">\n' +
            '               <span class="chat__msg-day-title">' + timelineDate +
            '</span>\n' +
            '          </div>';

        if (timelineDate == 'Today' &&
            $('.chat-conversation').find($('.chat__msg-day-title')).text() ==
            'Today') {
            return '';
        }

        if ($.inArray(timelineDate, previousDate) === -1) {
            //only new timeline will be added
            timeLineEle = timeLineEleContent;
            previousDate.push(timelineDate);
        } else if (needToRemoveOldTimeline) {
            //new timeline will be added and old will be REMOVED
            let oldTimeLineEle = $('.chat-conversation').
                find('.' + timelineDateClass);
            if (oldTimeLineEle.length) {
                $('.' + timelineDateClass).remove();
                timeLineEle = timeLineEleContent;
            }
        }
        return timeLineEle;
    };

    window.isTyping = function () {
        let channel = Echo.private(`chat`);
        if (startTyping) {
            //fire start typing event
            channel.whisper(`start-typing.${latestSelectedUser}`, {
                user: { id: loggedInUserId, name: authUserName },
                typing: true,
            });
        }
        startTyping = false;
        clearTimeout(timer);
        timer = setTimeout(stopTyping, 1000);
    };

    window.stopTyping = function () {
        startTyping = true;
        let channel = Echo.private(`chat`);

        //fire stop typing event
        channel.whisper(`stop-typing.${latestSelectedUser}`, {
            user: { id: loggedInUserId },
            typing: true,
        });
    };

    window.setNoConversationYet = function () {
        let conversationListLength = $('#chatPeopleBody').
            find('.chat__person-box').length;
        let archiveConversationListLength = $('#archivePeopleBody').
            find('.chat__person-box').length;
        noConversationYet = (conversationListLength > 0) ? false : true;
        noArchiveConversationYet = (archiveConversationListLength > 0)
            ? false
            : true;
        if (!noConversationYet) {
            noConversationYetEle.hide();
        } else {
            noConversationYetEle.show();
        }
        if (!noArchiveConversationYet) {
            noArchiveConversationYetEle.hide();
        } else {
            noArchiveConversationYetEle.show();
        }
    };

    // user search event
    $(document).
        on('click', '#userListForChat .user-list-chat-select__list-item',
            function () {
                $('.user-list-chat-select .user-list-chat-select__list-item').
                    removeClass('user-list-chat-select__list-item--active');
                $(this).addClass('user-list-chat-select__list-item--active');
                startNewConversation();
            });
    $(document).
        on('click', '#userListForAssignChat .user-list-chat-select__list-item',
            function () {
                $('.user-list-chat-select .user-list-chat-select__list-item').
                    removeClass('user-list-chat-select__list-item--active');
                $(this).addClass('user-list-chat-select__list-item--active');
                let selectedAgentId = $(
                    '.user-list-chat-select__list-item--active input')[0].value;
                let notAssignUserId = $('#notAssignUserId').data('id');
                $.ajax({
                    type: 'POST',
                    url: 'assign-to-agent',
                    data: {
                        agentId: selectedAgentId,
                        userId: notAssignUserId,
                    },
                    success: function (data) {
                        displaySuccessMessage(data.message);
                        $('#addNewChat').modal('hide');
                        $('#notAssignUserId').hide();
                        let agentName = Lang.get('messages.ticket.assigned_to') + ': ' + data.data;
                        $('#assignAgentNameLabel').text(agentName);
                    },
                    error: function (result) {
                        displayErrorMessage(result.responseJSON.message);
                    },
                    complete: function () {
                    },
                });
            });

    $(document).on('click', '#notAssignUserId', function (e) {
        e.preventDefault();
        $('#modalTitle').text(assignToAgentLabel);
        window.livewire.emit('setIsAssignToAgent', 1);
        $('#addNewChat').modal('show');
    });

    window.startNewConversation = function () {
        let selectedUserId = $(
            '.user-list-chat-select__list-item--active input')[0].value;
        let selectedUserProfilePicUrl = $(
            '.user-list-chat-select__list-item--active img').attr('src');
        let selectedUserName = $(
            '.user-list-chat-select__list-item--active .add-user-contact-name').
            text();
        let selectedUserRole = $(
            '.user-list-chat-select__list-item--active .add-chat-user-role').
            val();
        let isCustomerChat = $('.user-list-chat-select__list-item--active input')[1].value;
        let isChatByCustomer = 0;

        if (isCustomerChat == 0) {
            isChatByCustomer = 1;
        }
        $('#addNewChat').modal('toggle');
        var isUserElePresent = $('.chat__people-body').
            find('#user-' + selectedUserId).length;
        let isUserElePresentInArchiveChat = archivePeopleBodyEle.
            find('#user-' + selectedUserId).length;
        if (isUserElePresentInArchiveChat) {
            makeArchiveChatTabActive();
        } else {
            makeActiveChatTabActive();
        }
        let selectedUserStatus = $('.user-' + selectedUserId).data('status');
        if (!isUserElePresent) {
            let newUserEle = prepareNewConversation(
                selectedUserId,
                selectedUserName,
                '',
                selectedUserProfilePicUrl,
                selectedUserStatus,
                isChatByCustomer,
                selectedUserRole,
            );
            chatPeopleBodyEle.prepend(newUserEle);
            newConversationStartedUserIds.push(selectedUserId);
        }
        $('#user-' + selectedUserId).trigger('click');
        setNoConversationYet();
    };

    window.prepareNewConversation = function (
        userId,
        name,
        messageInfo = '',
        profilePic,
        status = '',
        isCustomerChat = 0,
        userRole = null,
    ) {
        let count = 0;
        if (messageInfo !== '') {
            status = messageInfo.sender.is_online;
            count = 1;
        }

        let template = $.templates('#tmplContact');
        let helpers = {
            getMessageByItsTypeForChatList: getMessageByItsTypeForChatList,
            getLocalDate: getLocalDate,
        };

        return template.render({
            id: userId,
            name: name,
            photo_url: profilePic,
            status: status,
            messageInfo: messageInfo,
            count: count,
            isCustomerChat: isCustomerChat,
            userRole: userRole,
        }, helpers);
    };

    window.makeUserOnlineOffline = function (ele, status) {
        if (status) {
            ele.find('.chat__person-box-status').
                removeClass('chat__person-box-status--offline').
                addClass('chat__person-box-status--online');
        } else {
            ele.find('.chat__person-box-status').
                removeClass('chat__person-box-status--online').
                addClass('chat__person-box-status--offline');
        }
    };

// getting data on modal for search user
    $('#addNewChat').on('show.bs.modal', function () {
        $('.user-list-chat-select .user-list-chat-select__list-item').
            removeClass('user-list-chat-select__list-item--active');

        $('#userListForChat').children('li').removeAttr('style');
    });

    $('#addNewChat').on('hidden.bs.modal', function () {
        clearSearchOfBlockedContactsTab();
        $('#modalTitle').text(newConversationsLabel);
    });

    window.clearSearchOfBlockedContactsTab = function () {
        window.livewire.emit('setIsAssignToAgent',0);
        let searchBlockUsers = $('#searchBlockUsers').val();
        if (searchBlockUsers != '') {
            window.livewire.emit('clearSearchOfBlockedUsers');
        }
        window.livewire.emit('clearSearchUsers');
        $('#searchBlockUsers').val('');
    };

    window.prepareContactForModal = function (users, addSingleUser = false) {
        let helpers = {
            getGender: getGender,
        };
        let template = $.templates('#tmplAddChatUsersList');
        let htmlOutput = template.render(users, helpers);

        if (addSingleUser) {
            $('#userListForChat').append(htmlOutput);
        } else {
            $('#userListForChat').html(htmlOutput);
        }
    };

    window.getGender = function (gender) {
        if (gender == 1) {
            return 'male';
        }
        if (gender == 2) {
            return 'female';
        }
        return '';
    };

    window.prepareBlockedUsers = function (users, addSingleUser = false) {
        $('.no-blocked-user').hide();
        let template = $.templates('#tmplBlockedUsers');
        let htmlOutput = template.render(users);
        if (addSingleUser) {
            $('#blockedUsersList').append(htmlOutput);
        } else {
            $('#blockedUsersList').html(htmlOutput);
        }
    };

    window.searchEleSearchEvent = function (searchEle) {
        searchEle.on('search', function () {
            if (!this.value) {
                searchEle.trigger('keyup');
            }
        });
    };

    window.searchUsers = function () {
        let searchResult = [];
        let searchEle = $('#searchUserInput');
        searchEleSearchEvent(searchEle);
        searchEle.on('keyup', function () {
            searchResult = [];
            let value = $(this).val().toLowerCase();
            let activeNavTab = getActiveNavChatTabId();
            $('#' + activeNavTab + ' .chat__person-box').filter(function () {
                $(this).
                    toggle($(this).
                        find('.chat__person-box-name').
                        text().
                        toLowerCase().
                        indexOf(value) > -1);
                searchResult.push($(this).
                    find('.chat__person-box-name').
                    text().
                    toLowerCase().
                    indexOf(value));
            });
            if (activeNavTab === 'chatPeopleBody') {
                ifUserNotPresentShowNoRecordFound(searchResult,
                    'no-conversation');
            }
            if (activeNavTab === 'archivePeopleBody') {
                ifUserNotPresentShowNoRecordFound(searchResult,
                    'no-archive-conversation');
            }
        });
    };

    $(document).on('click', '#activeChatTab', function () {
        resetSearch();
    });

    $(document).on('click', '#archiveChatTab', function () {
        resetSearch();
    });

    window.resetSearch = function () {
        let searchEle = $('#searchUserInput').val('');
        searchEle.trigger('keyup');
    };

    window.getActiveNavChatTabId = function () {
        return $('.chat__tab-content').find('.active').attr('id');
    };

    window.removeValueFromArray = function (arr, arrValue) {
        for (var i = 0; i < arr.length; i++) {
            if (arr[i] === arrValue) {
                arr.splice(i, 1);
            }
        }

        return arr;
    };

    window.ifUserNotPresentShowNoRecordFound = function (
        searchResult, noConversationClassName) {
        let isUserPresent = false;
        $.each(searchResult, function (index, value) {
            if (value >= 0) {
                isUserPresent = true;
                return false;
            }
        });
        if (isUserPresent) {
            $('.' + noConversationClassName).hide();
            return false;
        } else {
            let activeTab = getActiveNavChatTabId();
            let searchInput = $('#searchUserInput').val();
            if (searchInput !== '') {
                if (activeTab === 'chatPeopleBody') {
                    noConversationYetEle.hide();
                }
                if (activeTab === 'archivePeopleBody') {
                    noArchiveConversationYetEle.hide();
                }
                $('.' + noConversationClassName).show();
            } else {
                if (noConversationYet && activeTab === 'chatPeopleBody') {
                    noConversationYetEle.show();
                }
                if (noArchiveConversationYet && activeTab ===
                    'archivePeopleBody') {
                    noArchiveConversationYetEle.show();
                }
                $('.' + noConversationClassName).hide();
            }
            return true;
        }
    };

    window.updateUserStatus = function (userID, status) {
        //recent chat-list ele
        let UserEle = chatPeopleBodyEle.find('#user-' + userID);
        //new conversation ele (in pop up)
        let newUserEle = $('.user-' + userID);
        let newUserEleParent = $('.chat-user-' + userID);

        /** Do not show user status when user is blocked */
        if ($.inArray(userID, blockedUsersList) != -1) {
            return;
        }

        if (status == 1) {
            UserEle.find('.chat__person-box-status').
                removeClass('chat__person-box-status--offline').
                addClass('chat__person-box-status--online');

            //conversation
            if ($('#toId').val() == userID) {
                $('.typing').html('online');

                //user profile
                $('.chat-profile__person-status').show().text('online');
                $('.chat-profile__person-last-seen').show().text('Online');
                // $('.chat-profile__person-last-seen').hide();
            }

            newUserEle.find('.chat__person-box-status').
                removeClass('chat__person-box-status--offline').
                addClass('chat__person-box-status--online');
            newUserEle.attr('data-status', 1);
            newUserEleParent.removeClass('online').
                removeClass('offline').
                addClass('online');
        } else {
            UserEle.find('.chat__person-box-status').
                removeClass('chat__person-box-status--online').
                addClass('chat__person-box-status--offline');
            newUserEle.find('.chat__person-box-status').
                removeClass('chat__person-box-status--online').
                addClass('chat__person-box-status--offline');
            newUserEle.attr('data-status', 0);
            newUserEleParent.removeClass('online').
                removeClass('offline').
                addClass('offline');

            let last_seen = 'last seen at: ' +
                getCalenderFormatForLastSeen(Date(), 'hh:mma', 0);
            $('.typing').html(last_seen);

            //user profile
            $('.chat-profile__person-last-seen').show().text('');
            $('.chat-profile__person-last-seen').show().text(last_seen);
            // $('.chat-profile__person-status').hide();
            $('.chat-profile__person-status').show().text('');
            $('.chat-profile__person-status').show().text(last_seen);
        }
    };

    window.getMessageByScroll = function () {
        $('.chat-conversation').on('scroll', function () {
            if ($(this).scrollTop() === 0) {
                shouldCallApiTop = (callBeforeAPI) ? true : false;
                if (shouldCallApiTop === true) {
                    let reqData = {
                        'isCustomerChat': isCustomerChat,
                        'before': lastMessageIdForScroll,
                        // 'limit': limit,
                    };
                    getOldOrNewConversation(reqData, 1, 0);
                }
            } else if ($(this).scrollTop() + $(this).innerHeight() >=
                ($(this)[0].scrollHeight - 1)) {
                let unreadIds = [];
                $('.chat-conversation .unread').each(function () {
                    unreadIds.push($(this).data('message_id'));
                });
                if (unreadIds.length > 0) {
                    fireReadMessageEventUsingIds(unreadIds);
                }

                let messageCount = $('.chat__person-box--active').
                    find('.chat__person-box-count').
                    text();
                messageCount = (isNaN(messageCount) || messageCount === '')
                    ? 0
                    : messageCount;

                if (messageCount > 0) {
                    // callAfterAPI = false, means do not load after messages when read time messages are incoming
                    shouldCallApiBottom = (callAfterAPI) ? true : false;
                }

                if (shouldCallApiBottom === true) {
                    let lastMessageIdForScrollBottom = $('.chat-conversation').
                        children().
                        last().
                        attr('data-message_id');

                    let anyNewMessages = $(
                        '.message-' + lastMessageIdForScrollBottom).next();

                    if (anyNewMessages.length > 0) {
                        return;
                    }
                    callAfterAPI = false;
                    let reqData = {
                        'after': lastMessageIdForScrollBottom,
                        'isCustomerChat': isCustomerChat,
                        // 'limit': limit,
                    };
                    getOldOrNewConversation(reqData, 0, 1);
                }
            }
        });
    };

    window.getOldOrNewConversation = function (reqData, isBefore, isAfter) {
        $('.loading-message').removeClass('d-none');
        let urlDetail = userURL + selectedContactId + '/conversation';

        $.ajax({
            type: 'GET',
            url: urlDetail,
            data: reqData,
            success: function (data) {
                let userOrGroupObj = data.data.user;
                if (data.success && latestSelectedUser === userOrGroupObj.id) {
                    let conversations = data.data.conversations;
                    $.merge(conversationMessages, conversations);
                    if (conversations.length > 0) {
                        if (isBefore) {
                            let lastMsg = data.data.conversations[data.data.conversations.length -
                            1];
                            lastMessageIdForScroll = lastMsg.id;
                            $.each(conversations,
                                function (index, conversation) {
                                    $('.chat-conversation').
                                        prepend(prepareChatConversation(
                                            conversation));
                                });

                            let scrolledAtEle = $('.message-' + reqData.before);
                            scrollAtEle(scrolledAtEle);
                            setOpenMsgInfoEvent();
                        }
                        if (isAfter) {
                            $.each(conversations,
                                function (index, conversation) {
                                    $('.chat-conversation').
                                        append(prepareChatConversation(
                                            conversation, false));
                                });
                            setOpenMsgInfoEvent();
                            fireReadMessageEvent(conversations);
                        }
                    } else {
                        if (isBefore) {
                            shouldCallApiTop = false;
                        }
                        if (isAfter) {
                            shouldCallApiBottom = false;
                        }
                    }
                    if (scrollAtLastMsg && isAfter) {
                        scrollToLastMessage();
                    }
                    $('.loading-message').addClass('d-none');
                }
            },
            error: function (error) {
                console.log(error);
            },
        });
    };

    window.imageRenderer = function (message) {
        return `<a href="${message}" data-fancybox="gallery" data-toggle="lightbox" data-gallery="example-gallery" data-src="${message}"><img src="${message}"></a>`;
    };

    window.pdfRenderer = function (message, fileName) {
        return `<div class="media-wrapper d-flex align-items-center"><i class="fa fa-file-pdf-o" aria-hidden="true"></i><a href= "${message}"  target="blank" class="item"> ${fileName}</a></div>`;
    };

    window.voiceRenderer = function (message, fileName) {
        return `<div class="media-wrapper d-flex align-items-center p-0"><audio controls><source src="${message}" type="audio/mp3">
            Your browser does not support the audio element.
        </audio></div>`;
    };

    window.docRenderer = function (message, fileName) {
        return `<div class="media-wrapper d-flex align-items-center"><i class="fa fa-file-word-o" aria-hidden="true"></i><a href="${message}"
    target="_blank">${fileName}</a></div>`;
    };

    window.txtRenderer = function (message, fileName) {
        return `<div class="media-wrapper d-flex align-items-center"><i class="fa fa-file-text-o" aria-hidden="true"></i><a href="${message}"
    target="_blank">${fileName}</a></div>`;
    };

    window.xlsRenderer = function (message, fileName) {
        return `<div class="media-wrapper d-flex align-items-center"><img class="chat-file-preview" src="${xlsURL}" /><a href="${message}"
    target="_blank">${fileName}</a></div>`;
    };

    window.fileRenderer = function (message, fileName, fileIcon) {

        if (fileIcon) {
            return `<div class="media-wrapper d-flex align-items-center"><img class="chat-file-preview" src="${fileIcon}" /><a href= "${message}"  target="blank" class="item"> ${fileName}</a></div>`;
        }

        return `<div class="media-wrapper d-flex align-items-center"><i class="fa ${fileIcon}" aria-hidden="true"></i><a href= "${message}"  target="blank" class="item"> ${fileName}</a></div>`;
    };

    window.videoRenderer = function (message) {
        return `<div class="chat-media">
                     <video id="my-video" class="video-js" controls preload="auto" width="640" height="264" data-setup=''>
                            <source src="${message}" type="video/mp4">
                            <source src="${message}" type="video/webm">
                            <p class="vjs-no-js">
                                To view this video please enable JavaScript, and consider upgrading to a web browser that
                                <a href="https://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
                            </p>
                      </video>
                </div>`;
    };

    window.renderYoutubeURL = function (url, redererClassName = '') {
        let newUrl = getYoutubeEmbedURL(url);
        return `<iframe width="246" height="246" style="border-radius:8px;" class="` +
            redererClassName + `" src="${newUrl}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen>
            </iframe>`;
    };

    window.getYoutubeEmbedURL = function (url) {
        let newUrl = url;
        let regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
        let match = url.match(regExp);
        if (match && match[2].length === 11) {
            newUrl = 'https://www.youtube.com/embed/' + match[2] + '';
        }
        return newUrl;
    };

    window.format = function (dateTime, format = 'DD-MMM-YYYY') {
        return moment(dateTime).format(format);
    };

    window.imageRendererInSideMedia = function (message, id) {
        return `<a href="${message}" data-fancybox="gallery" data-toggle="media" data-gallery="media-gallery" data-src="${message}" id="mediaProfile-${id}"><img src="${message}"></a>`;
    };

    window.sideMediaRenderer = function (message, fileName, fileIcon, id) {
        return `<div class="media-wrapper justify-content-center d-flex align-items-center profile-media" id="mediaProfile-${id}" title="${fileName}" ><a href= "${message}" target="blank" class="item"> <i class="far fa-file-pdf mx-0" aria-hidden="true" ></i></a></div>`;
    };

    window.getLocalDate = function (dateTime, format = 'hh:mma') {
        if (isUTCTimezone == '0') {
            return moment(dateTime).format(format);
        }

        return moment.utc(dateTime).local().format(format);
        // return date.calendar(null, {
        //     sameDay: format,
        //     lastDay: '[Yesterday]',
        //     lastWeek: 'M/D/YY',
        //     sameElse: 'M/D/YY',
        // });
    };

    window.getChatMagTimeInConversation = function (
        dateTime, format = 'h:mma') {

        if (isUTCTimezone == '0') {
            return moment(dateTime).format(format);
        }

        return moment.utc(dateTime).local().format(format);
    };

    window.getCalenderFormatForLastSeen = function (
        dateTime, format = 'hh:mma', needToConvertLocalDate = 1) {
        let date = (needToConvertLocalDate) ? moment.
            utc(dateTime).
            local() : moment(dateTime);
        return date.calendar(null, {
            sameDay: '[Today], ' + format,
            lastDay: '[Yesterday], ' + format,
            lastWeek: 'dddd, ' + format,
            sameElse: function () {
                if (moment().year() === moment(dateTime).year()) {
                    return 'MMM D, ' + format;
                } else {
                    return 'MMM D YYYY, ' + format;
                }
            },
        });
    };

    window.getCalenderFormatForTimeLine = function (dateTime) {
        return moment.utc(dateTime).local().calendar(null, {
            sameDay: '[Today]',
            lastDay: '[Yesterday]',
            lastWeek: 'dddd, MMM Do',
            sameElse: function () {
                if (moment().year() === moment(dateTime).year()) {
                    return 'dddd, MMM Do';
                } else {
                    return 'dddd, MMM Do YYYY';
                }
            },
        });
    };

    window.addNoMessagesIndicator = function () {
        let noMsgTemplate = $.templates('#tmplNoMessagesYet');
        let htmlOutput = noMsgTemplate.render();
        $('#conversation-container').html(htmlOutput);
    };

    window.detectUrlFromTextMessage = function (message) {
        let regex = /((http|https|ftp):\/\/[a-zа-я0-9\w?=&.\/-;#~%-]+(?![a-zа-я0-9\w\s?&.\/;#~%"=-]*>))/g;
        // Replace plain text links by hyperlinks
        return message.replace(regex,
            '<a href=\'$1\' target=\'_blank\'>$1</a>');
    };

    window.addNoConversationIndicator = function () {
        let noConversationYet = $.templates('#tmplConversationYet');
        let htmlOutput = noConversationYet.render();

        $('.chat__area-wrapper').html(htmlOutput);
    };

    window.fireSwal = function (
        icon = 'success', title = `${deleteHeading}!`,
        text = Lang.get('messages.swal_message.chat_message_delete'),
        confirmButtonColor = '#00b074',
        timer = 2000) {
        Swal.fire({
            icon: icon,
            title: title,
            text: text,
            customClass: {
                confirmButton: 'btn btn-primary btn-lg',
            },
            // confirmButtonColor: confirmButtonColor,
            confirmButtonText: Lang.get('messages.common.ok'),
            timer: timer,
        });
    };

    function deleteConversation (userId,isCustomerChat) {
        $.ajax({
            type: 'get',
            url: deleteConversationUrl + userId + '/delete',
            data: {
                isCustomerChat : isCustomerChat,
            },
            success: function (data) {
                if (data.success !== true) {
                    return false;
                }
                fireSwal('success', Lang.get('messages.common.deleted') + '!',
                    Lang.get('messages.chats.conversation_deleted') + '!');

                let triggerEventRemaining = false;
                if (latestSelectedUser === userId) {
                    let selectedUserEle = $('#user-' + latestSelectedUser).
                        parents('.contact-area');
                    if (selectedUserEle.length === 0) {
                        selectedUserEle = $('#user-' + latestSelectedUser);
                    }
                    let nextEle = selectedUserEle.nextAll(
                        '.contact-area:first').find('.chat__person-box');
                    if (nextEle.length === 0) {
                        nextEle = selectedUserEle.nextAll(
                            '.chat__person-box:first');
                    }
                    if (nextEle.length > 0) {
                        $('#user-' + nextEle.data('id')).trigger('click');
                    } else {
                        let prevEle = selectedUserEle.prevAll(
                            '.contact-area:first').find('.chat__person-box');
                        if (prevEle.length === 0) {
                            prevEle = selectedUserEle.prevAll(
                                '.chat__person-box:first');
                        }
                        if (prevEle.length) {
                            $('#user-' + prevEle.data('id')).trigger('click');
                        }
                        if (prevEle.length === 0) {
                            triggerEventRemaining = true;
                        }
                    }

                    if (nextEle.length === 0 && triggerEventRemaining) {
                        triggerEventRemaining = true;
                    }
                }
                let userEle = $('#user-' + userId).parents('.contact-area');
                if (userEle.length === 0) {
                    userEle = $('#user-' + userId);
                }
                let activeChat = userEle.parents('#chatPeopleBody');
                let archiveChat = userEle.parents('#archivePeopleBody');
                userEle.remove();
                if (triggerEventRemaining) {
                    let nextEle = $('#chatPeopleBody').
                        find('.chat__person-box:first');
                    if (nextEle.length > 0) {
                        $('#user-' + nextEle.data('id')).trigger('click');
                    }
                }
                if (activeChat.length > 0 &&
                    chatPeopleBodyEle.find('.chat__person-box').length === 0) {
                    setNoConversationYet();
                    addNoConversationIndicator();
                    selectedContactId = 0;
                }
                if (archiveChat.length > 0 &&
                    $('#archivePeopleBody').find('.chat__person-box').length ===
                    0) {
                    setNoConversationYet();
                    addNoConversationIndicator();
                    selectedContactId = 0;
                }
            },
        });
    };

    const swalDelete = Swal.mixin({
        customClass: {
            // confirmButton: 'btn btn-primary btn-lg',
            // cancelButton: 'btn btn-secondary btn-lg mr-2',
            actions: 'flex-row-reverse',
        },
        // buttonsStyling: false,
        confirmButtonColor: '#00b0b0',
        cancelButtonColor: '#C1C1C1',
    });

    $(document).on('click', '.chat__person-box-delete', function (e) {

        let chatDelEle = $(this);
        let userId = chatDelEle.parents('.chat__person-box').data('id');
        let isCustomerChat = chatDelEle.parents('.chat__person-box').attr('is-customer-chat');
        let contactName = $('#user-' + userId).
            find('.contact-name').
            text().
            toString().
            trim();

        swalDelete.fire({
            title: `${deleteHeading} !`,
            html: Lang.get('messages.chats.delete_chat') + ' "' + contactName +
                '" ?',
            icon: 'warning',
            showCancelButton: true,
            showConfirmButton: true,
            cancelButtonText: Lang.get('messages.common.no'),
            confirmButtonText: Lang.get('messages.common.yes'),
        }).then((result) => {
            if (result.value) {
                deleteConversation(userId,isCustomerChat);
            }
        });

        //here we write stopPropagation to stop ajax call of select chat after this delete call, if we not write this than select conversation call will happen
        e.stopPropagation();
    });

    $(document).on('click', '.chat__person-box-archive', function (e) {
        let chatArchiveEle = $(this);
        let userId = chatArchiveEle.parents('.chat__person-box').data('id');
        let isCustomerChat = chatArchiveEle.parents('.chat__person-box').attr('is-customer-chat');
        let isArchiveChat = $(this).parents('#archivePeopleBody').length;
        let contactName = $('#user-' + userId).find('.contact-name').text();
        let ArchiveUnarchive = (isArchiveChat) ? Lang.get('messages.chats.un_archive') : Lang.get('messages.chats.archive');

        swalDelete.fire({
            title: Lang.get('messages.chats.are_you_sure'),
            html: ArchiveUnarchive + ' ' +
                Lang.get('messages.chats.chat_with') + ' <b>' + contactName +
                '</b>&nbsp;?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: Lang.get('messages.common.yes'),
            cancelButtonText: Lang.get('messages.common.no'),
        }).then((result) => {
            if (result.value) {
                archiveConversation(userId,isCustomerChat);
            }
        });

        //here we write stopPropagation to stop ajax call of select chat after this delete call, if we not write this than select conversation call will happen
        e.stopPropagation();
    });

    window.archiveConversation = function (userId,isCustomerChat) {
        $.ajax({
            type: 'get',
            url: deleteConversationUrl + userId + '/archive-chat',
            data: {
                isCustomerChat : isCustomerChat,
            },
            success: function (data) {
                if (data.success === true) {
                    if (data.data.archived) {
                        fireSwal('success',
                            Lang.get('messages.chats.archived') + ' !',
                            Lang.get('messages.chats.conversation_archived') +
                            ' !');
                    } else {
                        fireSwal('success',
                            Lang.get('messages.chats.un_archived') + ' !',
                            Lang.get('messages.chats.conversation_unarchived') +
                            ' !');
                    }

                    let archiveConversation = $('#user-' + userId);
                    $('#user-' + userId).remove();

                    if (data.data.archived) {
                        archiveConversation.find('.chat__person-box-archive').
                            text(Lang.get('messages.chats.un_archive'));
                        archivePeopleBodyEle.append(archiveConversation);
                        makeArchiveChatTabActive();
                    } else {
                        archiveConversation.find('.chat__person-box-archive').
                            text(Lang.get('messages.chats.archive_chat'));
                        chatPeopleBodyEle.append(archiveConversation);
                        makeActiveChatTabActive();
                    }

                    setNoConversationYet();
                }
            },
        });
    };

    window.makeActiveChatTabActive = function () {
        $('.nav-item a[href="#chatPeopleBody"]').tab('show');
    };

    window.makeArchiveChatTabActive = function () {
        $('.nav-item a[href="#archivePeopleBody"]').tab('show');
    };

    window.showNoArchiveConversationEle = function () {
        if (archivePeopleBodyEle.find('.chat__person-box').length === 0) {
            noArchiveConversationEle.show();
        } else {
            noArchiveConversationEle.hide();
        }
    };

    window.showNoActiveConversationEle = function () {
        if (chatPeopleBodyEle.find('.chat__person-box').length === 0) {
            noConversationEle.show();
        } else {
            noConversationEle.hide();
        }
    };

    window.removeTimeline = function (messageEle) {
        let timeLineEle = messageEle.prev('.chat__msg-day-divider');
        if (timeLineEle.length) {
            var nextElementLength = messageEle.next().length;
            if (nextElementLength === 0) {
                timeLineEle.remove();
            }
        }
    };

    window.checkAllMsgAndShowNoMsgYet = function () {
        let conversationContainer = $('#conversation-container');
        let senderMsgLength = conversationContainer.find(
            '.chat-conversation__sender').length;
        let receiverMsgLength = conversationContainer.find(
            '.chat-conversation__receiver').length;
        let badgeMessages = $('.chat-conversation').
            find('#message-badges').length;
        if (senderMsgLength === 0 && receiverMsgLength === 0 &&
            badgeMessages === 0) {
            let chatPersonBox = $('.chat__person-box--active');
            conversationContainer.html('');
            noMsgesYet.show();
            addNoMessagesIndicator();
            chatPersonBox.find('.chat-message').text('');
            chatPersonBox.find('.chat__person-box-time').text('');
        }
    };

    window.deleteMsgForEveryone = function (messageId, previousMessageId) {
        $.ajax({
            type: 'post',
            url: '/conversations/' + messageId + '/delete',
            data: { 'previousMessageId': previousMessageId },
            success: function (data) {
                if (data.success === true) {
                    let previousMessage = data.data.previousMessage
                    fireSwal('success',
                        Lang.get('messages.swal_message.delete'),
                        Lang.get('messages.swal_message.chat_message_delete'))
                    let messageEle = $('.message-' + messageId)
                    removeTimeline(messageEle)

                    /** UPDATE MEDIA IN PROFILE BAR*/
                    removeMediaFromProfileBar(messageId)

                    if (previousMessage != null && messageEle.nextAll(
                        '#send-receive-direction:first').length === 0) {
                        let chatPersonBox = $('.chat__person-box--active')

                        chatPersonBox.find('.chat-message').
                            html(getMessageByItsTypeForChatList(
                                previousMessage.message,
                                previousMessage.message_type,
                                previousMessage.file_name));
                        chatPersonBox.find('.chat__person-box-time').
                            text(getLocalDate(previousMessage.created_at));
                    }
                    messageEle.remove();
                    checkAllMsgAndShowNoMsgYet();
                }
            },
            error: function (result) {
                displayToastr(Lang.get('messages.error_message.error'), 'error',
                    result.responseJSON.message)
            },
        });
    };

    window.deleteMessageFromChat = function (messageId, previousMessageId) {
        $.ajax({
            type: 'post',
            url: deleteMessageUrl + messageId + '/delete',
            data: { 'previousMessageId': previousMessageId },
            success: function (data) {
                if (data.success === true) {
                    let previousMessage = data.data.previousMessage
                    fireSwal('success',
                        Lang.get('messages.swal_message.delete'),
                        Lang.get('messages.swal_message.chat_message_delete'))
                    let messageEle = $('.message-' + messageId)
                    removeTimeline(messageEle)

                    /** UPDATE MEDIA IN PROFILE BAR*/
                    removeMediaFromProfileBar(messageId)

                    if (previousMessage != null && messageEle.nextAll(
                        '#send-receive-direction:first').length === 0) {
                        let chatPersonBox = $('.chat__person-box--active')

                        chatPersonBox.find('.chat-message').
                            html(getMessageByItsTypeForChatList(
                                previousMessage.message,
                                previousMessage.message_type,
                                previousMessage.file_name));
                        chatPersonBox.find('.chat__person-box-time').
                            text(getLocalDate(previousMessage.created_at));
                    }
                    messageEle.remove();
                    checkAllMsgAndShowNoMsgYet();
                }
            },
            error: function (result) {
                displayToastr(Lang.get('messages.error_message.error'), 'error',
                    result.responseJSON.message)
            },
        });
    };

    /** User Profile Updated */
    Echo.private(`updates`).
        listen('UpdatesEvent', (e) => {
            if (e.type == 1) { //user profile updates
                let user = e.user;
                $('#userListForChat').find('.chat-user-' + user.id).find('img').
                    attr('src', user.photo_url);
                $('#userListForChat').find('.chat-user-' + user.id).
                    find('.add-user-contact-name').text(user.name);
                $('#user-' + user.id).find('img').attr('src', user.photo_url);
                $('#user-' + user.id).find('.contact-name').text(user.name);
                $('.user-chat-image-' + user.id).
                    attr('src', user.photo_url).
                    attr('data-original-title', user.name);

            }

            if (e.type == 3) {
                updateUserStatus(e.user_id, e.status);
            }
        });

    $(document).on('click', '.msg-delete-for-everyone', function (e) {
        e.preventDefault();

        let messageDelEle = $(this);
        let messageId = messageDelEle.parent().
            parent().
            parent().
            parent().
            data('message_id');
        let messageEle = $('.message-' + messageId);
        let previousMessageEle = messageEle.prevAll(
            '#send-receive-direction:first');
        let previousMessageId = 0;
        let badgeMsg = messageEle.prev('#message-badges');

        if (badgeMsg.length) {
            previousMessageId = badgeMsg.data('message_id');
        } else if (previousMessageEle.length) {
            previousMessageId = previousMessageEle.data('message_id');
        }

        swalDelete.fire({
            title: `${deleteHeading} !`,
            html: `${deleteMessage} "${Lang.get(
                'messages.swal_message.message')}" ?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: Lang.get('messages.common.yes'),
            cancelButtonText: Lang.get('messages.common.no'),
            confirmButtonColor: '#33b0b0',
        }).then((result) => {
            if (result.value) {
                deleteMsgForEveryone(messageId, previousMessageId);
            }
        });

        e.stopPropagation();
    });

    $(document).on('click', '.msg-delete-icon', function (e) {
        e.preventDefault();
        let messageDelEle = $(this);
        let messageId = messageDelEle.parent().
            parent().
            parent().
            parent().
            data('message_id');
        let messageEle = $('.message-' + messageId);
        let previousMessageEle = messageEle.prevAll(
            '#send-receive-direction:first',
        );
        let previousMessageId = 0;
        let badgeMsg = messageEle.prev('#message-badges');

        if (badgeMsg.length) {
            previousMessageId = badgeMsg.data('message_id');
        } else if (previousMessageEle.length) {
            previousMessageId = previousMessageEle.data('message_id');
        }

        swalDelete.fire({
            title: `${deleteHeading} !`,
            html: `${deleteMessage} ${Lang.get(
                'messages.swal_message.message')} ?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: Lang.get('messages.common.yes'),
            cancelButtonText: Lang.get('messages.common.no'),
            confirmButtonColor: '#33b0b0',
        }).then((result) => {
            if (result.value) {
                deleteMessageFromChat(messageId, previousMessageId);
            }
        });

        e.stopPropagation();
    });

    function performActionsAfterUnblock (user) {
        $('.blocked-user-' + user.id).remove();
        prepareContactForModal({ users: user }, true);
        if ($('#blockedUsersList').length <= 1) {
            $('.no-blocked-user').show();
            $('.no-blocked-user').find('span').text('No users blocked yet...');
        }

        $('.typing').show();
        $('#user-' + user.id).find('.chat__person-box-status').show();
        $('.chat-profile__person-status').show();
        $('#user-' + user.id).find('.contact-status').show();
        $('.contact-title-status').show();

        if ($('.chat__person-box--active').data('id') == user.id) {
            appendChatArea();
            $('.block-unblock-span').text('Block');
            $('.hdn-text-message').remove();
            $('.blocked-message-text').remove();

        }
    }

    function appendChatArea () {
        $('.chat__area-wrapper').append(chatSendArea);
        loadEojiArea();
        sendMessage();
    }

    function performActionAfterBlock (user) {
        $('.chat__area-text').remove();
        $('.blocked-message-text').remove();
        $('.block-unblock-span').text('Unblock');
        $('.chat__area-wrapper').append(blockedMessageText);
        $('.chat-user-' + user.id).remove();
        $('.typing').hide();
        $('#user-' + user.id).find('.chat__person-box-status').hide();
        $('.chat-profile__person-status').hide();
        $('#user-' + user.id).find('.contact-status').hide();
        $('.contact-title-status').hide();
        prepareBlockedUsers({ users: user }, true);
    }

    function blockUnblockUser (data, blockedTo) {
        let isBlocked = (data.is_blocked) ? true : false;
        $.ajax({
            url: userURL + blockedTo + '/block-unblock',
            type: 'PUT',
            data: data,
            success: function (result) {
                if (result.success) {
                    displayToastr(Lang.get('messages.success_message.success'),
                        'success', result.message)
                    let user = result.data.user
                    if (isBlocked) {
                        fireAddNewBlockedIdEvent(blockedTo)
                        performActionAfterBlock(user)
                    } else {
                        fireRemoveBlockedIdEvent(blockedTo)
                        performActionAfterBlock(user)
                    }
                }
            },
            error: function (result) {
                displayToastr(Lang.get('messages.error_message.error'), 'error',
                    result.responseJSON.message)
            },
        });
    }

    window.fireAddNewBlockedIdEvent = function (userId) {
        userId = parseInt(userId);
        blockedUsersList.push(userId);
        window.livewire.emit('addNewBlockedContactId', userId);
        window.livewire.emit('addBlockedUserId', userId);
    };

    window.fireRemoveBlockedIdEvent = function (userId) {
        userId = parseInt(userId);
        window.livewire.emit('removeBlockedContactId', userId);
        window.livewire.emit('removeBlockedUserId', userId);
        blockedUsersList = jQuery.grep(blockedUsersList, function (value) {
            return value != userId;
        });
    };

    /** UnBLock user */
    $(document).on('click', '.btn-unblock', function (e) {
        e.preventDefault();
        swalDelete.fire({
            title: Lang.get('messages.chats.are_you_sure'),
            html: Lang.get('messages.chats.unblock_this_user'),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: Lang.get('messages.common.yes'),
            cancelButtonText: Lang.get('messages.common.no'),
            confirmButtonColor: '#6777ef',
        }).then((result) => {
            if (result.value) {
                placeCaret = false;
                let userId = $(this).data('id');
                blockUnblockUser(
                    { is_blocked: false, blocked_to: userId }, userId,
                );
            }
        });
    });

    /*** Block UnBLock */
    $(document).on('click', '.block-unblock-user-switch', function (e) {
        let isBlocked = $(this).is(':checked');
        let blockedTo = $('#senderId').val();
        let data = {};
        data.is_blocked = isBlocked;
        data.blocked_to = blockedTo;

        swalDelete.fire({
            title: Lang.get('messages.chats.are_you_sure') ,
            html: (isBlocked) ? Lang.get('messages.chats.you_want_block_this_user') : Lang.get('messages.chats.you_want_unblock_this_user'),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: Lang.get('messages.common.yes'),
            cancelButtonText: Lang.get('messages.common.no'),
            confirmButtonColor: '#6777ef',
        }).then((result) => {
            if (!result.value) {
                $('.block-unblock-user-switch').
                    prop('checked', (isBlocked) ? false : true);
                return;
            }

            blockUnblockUser(data, blockedTo);
        });
    });

    window.displayPhoto = function (input, selector) {
        let displayPreview = true;
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = function (e) {
                let image = new Image();
                image.src = e.target.result;
                image.onload = function () {
                    $(selector).attr('src', e.target.result);
                    displayPreview = true;
                };
            };
            if (displayPreview) {
                reader.readAsDataURL(input.files[0]);
                $(selector).show();
            }
        }
    };

    function updateUnreadMessageCount (countOfConversationRead) {
        totalUnreadConversations -= countOfConversationRead;
        if (totalUnreadConversations === 0 || totalUnreadConversations < 1) {
            $('title').text('Conversations | ' + appName);
            return;
        }

        let messageString = (totalUnreadConversations > 99)
            ? '(99+)'
            : '(' + totalUnreadConversations + ')';

        $('title').text(messageString + ' | Conversations | ' + appName);
    };

    function removeMediaFromProfileBar (id) {
        $('#mediaProfile-' + id).remove();

        let length = $('.chat-profile__media-container').children().length;
        if (length == 1 || length == 0) {
            $('.no-photo-found').show();
        }
    };

    function loadTooltip () {
        $('[data-toggle="tooltip"]').tooltip();
    };

    function getURLFromMessageType (type) {
        if (type == 2) {
            return pdfURL;
        } else if (type == 3) {
            return docsURL;
        } else if (type == 4) {
            return audioURL;
        } else if (type == 5) {
            return videoURL;
        } else if (type == 6) {
            return youtubeURL;
        } else if (type == 7) {
            return textURL;
        } else if (type == 8) {
            return xlsURL;
        }
    }

    if (conversationId !== '') {
        setTimeout(function () {
            let conversationEle = $(document).find('#user-' + conversationId);
            conversationEle.trigger('click');
        }, 6000);
    }

    /**
     * This handler retrieves the images from the clipboard as a base64 string and returns it in a callback.
     *
     * @param pasteEvent
     * @param callback
     */
    function retrieveImageFromClipboardAsBase64 (
        pasteEvent, callback, imageFormat) {
        if (pasteEvent.clipboardData == false) {
            if (typeof (callback) == 'function') {
                callback(undefined);
            }
        }

        let items = pasteEvent.clipboardData.items;

        if (items == undefined) {
            if (typeof (callback) == 'function') {
                callback(undefined);
            }
        }

        for (let i = 0; i < items.length; i++) {
            // Skip content if not image
            if (items[i].type.indexOf('image') == -1) continue;
            // Retrieve image on clipboard as blob
            let blob = items[i].getAsFile();

            // Create an abstract canvas and get context
            let mycanvas = document.createElement('canvas');
            let ctx = mycanvas.getContext('2d');

            // Create an image
            let img = new Image();

            // Once the image loads, render the img on the canvas
            img.onload = function () {
                // Update dimensions of the canvas with the dimensions of the image
                mycanvas.width = this.width;
                mycanvas.height = this.height;

                // Draw the image
                ctx.drawImage(img, 0, 0);

                // Execute callback with the base64 URI of the image
                if (typeof (callback) == 'function') {
                    callback(mycanvas.toDataURL(
                        (imageFormat || 'image/png'),
                    ));
                }
            };

            // Crossbrowser support for URL
            let URLObj = window.URL || window.webkitURL;

            // Creates a DOMString containing a URL representing the object given in the parameter
            // namely the original Blob
            img.src = URLObj.createObjectURL(blob);
        }
    }

    window.addEventListener('paste', function (e) {
        // Handle the event
        retrieveImageFromClipboardAsBase64(e, function (imageDataBase64) {
            // If there's an image, open it in the browser as a new window :)
            if (!(selectedContactId > 0 || selectedContactId != '')) {
                return false;
            }
            if (imageDataBase64) {
                let template = $.templates('#copyPastImgTmplt');
                let imgHtml = template.render({ 'url': imageDataBase64 });
                $('#imageCanvas').append(imgHtml);
                $('#copyImageModal').modal('show');
            }
        });
    }, false);

    $(document).on('click', '#sendImages', function (e) {
        let imagesArr = [];
        let imagesArrHtml = $('#imageCanvas').find('.img-thumbnail');
        $.each(imagesArrHtml, function (index, value) {
            imagesArr.push($(this).attr('src'));
        });

        let data = {
            'to_id': selectedContactId,
            'message_type': 1,
            'images': imagesArr,
        };
        $.ajax({
            type: 'POST',
            url: imageUploadURL,
            data: data,
            success: function (data) {
                $.each(data.data, (index, value) => {
                    setSentOrReceivedMessage(value);
                });
                resetImageCanvas();
                $('#copyImageModal').modal('hide');
            },
            error: function (error) {
                displayToastr(Lang.get('messages.error_message.error'), 'error',
                    error.responseJSON.message)
            },
        });
    });

    $(document).on('click', '.remove-img', function (e) {
        $(this).parent().remove();
    });

    $('#copyImageModal').on('hidden.bs.modal', function () {
        resetImageCanvas();
    });

    window.resetImageCanvas = function () {
        $('#imageCanvas').html('');
    };
});

//Dropzon code
let myDropzone = '';
let sendMsgFiles = [];
$('#submit-all').hide();
$('#cancel-upload-file').hide();
window.Dropzone.options.dropzone = {
    thumbnailWidth: 125,
    acceptedFiles: 'image/*,.pdf,.doc,.docx,.xls,.xlsx,.mp4,.mkv,.avi,.txt,.mp3,.ogg,.wav,.aac,.alac',
    timeout: 50000,
    autoProcessQueue: false,
    parallelUploads: 10, // Number of files process at a time (default 2)
    addRemoveLinks: true,
    dictRemoveFile: '<i class="fa fa-trash-o" title="Remove" style="color: indianred;"></i>',
    uploadMultiple: true,
    dictCancelUpload: "",
    init: function () {
        let submitButton = document.querySelector('#submit-all');
        let cancelButton = document.querySelector('#cancel-upload-file');
        myDropzone = this; // closure

        submitButton.addEventListener('click', function () {
            $('.dz-progress').show();
            myDropzone.processQueue(); // Tell Dropzone to process all queued files.
        });

        cancelButton.addEventListener('click', function () {
            myDropzone.removeAllFiles(true);
            $('#fileUpload').modal('toggle');
        });

        // show the submit button only when files are dropped here:
        this.on('addedfile', function (file, dataUrl, mediaId = null) {
            $('#submit-all,#cancel-upload-file').show();
            $('.dz-progress').hide();
            $('.dz-remove').html('');
            $('.dz-remove').addClass('fas fa-trash text-danger mt-3');
            previewFile(file, dataUrl, mediaId);
        });

        this.on('removedfile', function () {
            if (this.getQueuedFiles().length === 0) {
                $('#submit-all,#cancel-upload-file').hide();
            }
        });

        function previewFile (file, dataUrl, mediaId) {
            let downloadPath = dataUrl;
            let ext = file.name.split('.').pop();
            if (ext == 'pdf') {
                $(file.previewElement).
                    find('.dz-image img').
                    attr('src', '/assets/img/pdf_icon.png');
            } else if (ext.indexOf('doc') != -1 || ext.indexOf('docx') !=
                -1) {
                $(file.previewElement).
                    find('.dz-image img').
                    attr('src', '/assets/img/doc_icon.png');
            } else if (ext.indexOf('xls') != -1 || ext.indexOf('xlsx') != -1 ||
                ext.indexOf('csv') !=
                -1) {
                $(file.previewElement).
                    find('.dz-image img').
                    attr('src', '/assets/img/xls_icon.png');
            }
            $('.dz-image').
                last().
                find('img').
                attr({ width: '100%', height: '100%' });
        }
    },
    complete: function (file) {
        if (this.getQueuedFiles().length > 0) {
            this.processQueue();
        }
        this.files.push(file);
        this.on('queuecomplete', function () {
            $('#fileUpload').modal('toggle');
            this.removeAllFiles(true);
            sendMsgFiles = [];
        });
    },
    success: function (file, response) {
        $.each(response.data, function (index, value) {
            let toId = $('#toId').val();
            let fromId = $('#fromId').val();
            let data = {
                to_id: toId,
                message: value.attachment,
                message_type: value.message_type,
                file_name: value.file_name,
            };
            if (fromId != undefined && fromId !== '') {
                data.from_id = fromId;
                data.send_by = loggedInUserId;
            }
            if ($.inArray(value.unique_code, sendMsgFiles) === -1) {
                storeMessage(data);
                sendMsgFiles.push(value.unique_code);
            }
        });
    },
    error: function (file, response) {
        if (typeof response === 'object') {
            response = (response.hasOwnProperty('message'))
                ? response.message
                : 'There is some error, Please try after some time';
        }
        displayToastr(Lang.get('messages.error_message.error'), 'error',
            response)
        let fileRef;

        return (fileRef = file.previewElement) != null ?
            fileRef.parentNode.removeChild(file.previewElement) : void 0;
    },
};

$('#fileUpload').on('hidden.bs.modal', function () {
    $('#submit-all,#cancel-upload-file').hide();
    myDropzone.removeAllFiles(true);
});

$(document).on('mouseleave', '.chat__person-box', function () {
    $('.more-btn-conversation-item').removeClass('show');
});
