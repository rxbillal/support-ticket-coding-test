require('../app');
let chatUser = getCookie('chat_user');
let userId = '';
let textMessageEle = $('#chatSend');
let placeCaret = true;
let authUserName, authImgUrl;
let assignedChatUserId;
const mediaTypeImage = 1;
const mediaTypePdf = 2;
const mediaTypeDocx = 3;
const mediaTypeVoice = 4;
const mediaTypeVideo = 5;
const youtubeUrl = 6;
const mediaTypeTxt = 7;
const mediaTypeXls = 8;

let previousDate = [];
let lastMessageIdForScroll = '';
let scrollAtLastMsg = false;
let isAllMessagesRead = false;
let limit = 20;
let shouldCallApiTop = true;
let shouldCallApiBottom = true;

let unreadMessageIds = [];
let readMessageFunctionInterval = false;
let readedMessageIds = [];
let chatPeopleBodyEle = $('#chat_fullscreen');

$(document).ready(function () {
    'use strict';

    if (isLogin) {
        $('.chat-conversation').html('<h6>You Are Already Login</h6>');

        return;
    }
    if (chatUser == '') {
        // no cookie
        $('.msg_chat').removeClass('d-block');
        $('.msg_form').removeClass('d-none');
        $('.msg_chat').addClass('d-none');
        $('.msg_form').addClass('d-block');
        return;
    }

    // has cookie
    userId = JSON.parse(getCookie('chat_user')).id;
    authUserName = JSON.parse(getCookie('chat_user')).name;

    loadAssignedChatUserId();
    listenForEvents(userId);

    $('.msg_chat').removeClass('d-none');
    $('.msg_form').removeClass('d-block');
    $('.msg_chat').addClass('d-block');
    $('.msg_form').addClass('d-none');
    loadEojiArea();
    getMsg();
});

$('#prime').click(function () {
    toggleFab();
    localStorage.removeItem('chat-visible');
    if ($('.chat').hasClass('is-visible')) {
        localStorage.setItem('chat-visible', '1');
    } else {
        localStorage.setItem('chat-visible', '0');
    }
});

$(document).on('keydown', function (e) {
    if (e.keyCode === 27) {
        localStorage.removeItem('chat-visible');
        $('#chatDivision').removeClass('is-visible');
    }
});
$('.close-chat').click(function () {
    localStorage.removeItem('chat-visible');
    $('#chatDivision').removeClass('is-visible');
});

window.listenForEvents = function (userId) {
    window.Echo.channel('user-updates.' + userId).
        listen('PublicUserEvent', (notification) => {
            if (notification.type == 1) { // message received
                fireReadMessageEventUsingIds([notification.id]);
                $('#chat_fullscreen').
                    append(prepareChatConversation(notification));
                scrollToBottomFunc();
            } else if (notification.type == 2) { // chat assigned
                assignedChatUserId = notification.assignedTo;
                loadAssignedChatUserId();
            } else if (notification.type === 4) { // private message read
                privateMessageReadByUser(notification);
            } else if (notification.type === 5) { // message deleted for everyone
                messageDeletedForEveryone(notification);
            }
        });
};

$(document).on('click', '#endChatButton', function () {
    let personName = $('#chat_head').text();
    swal({
            title: Lang.get('messages.chats.end_chat') + ' !',
            text: Lang.get('messages.chats.are_you_sure_end_chat_with') + ' "' +
                personName + '" ?',
            type: 'warning',
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonColor: '#00b074',
            cancelButtonColor: '#d33',
            cancelButtonText: Lang.get('messages.common.no'),
            confirmButtonText: Lang.get('messages.common.yes'),
        },
        function () {
            setLastSeenOfUser(0);
            deleteCookie('chat_user');
            setTimeout(function (){
                location.reload();
            },1500);
        });
});

$(document).on('submit', '#chatForm', function (e) {
    e.preventDefault();
    processingBtn('#chatForm', '#chat_frm_submit', 'loading');

    $.ajax({
        type: 'POST',
        url: chatUserStoreUrl,
        data: $(this).serialize(),
        success: function (data) {
            setCookie('chat_user', JSON.stringify(data.data), 180);
            userId = data.data.id;
            $('.msg_chat').removeClass('d-none');
            $('.msg_form').removeClass('d-block');
            $('.msg_form').addClass('d-none');
            $('.msg_chat').addClass('d-block');
            loadEojiArea();
            listenForEvents(userId);
            getMsg();
            loadAssignedChatUserId();
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
        complete: function () {
            processingBtn('#chatForm', '#chat_frm_submit');
        },
    });

});

function loadEojiArea () {
    textMessageEle.emojioneArea({
        recentEmojis: false,
        pickerPosition: 'top',
        filtersPosition: 'bottom',
        tones: false,
        saveEmojisAs: 'shortname',
        autocomplete: false,
        inline: true,
        hidePickerOnBlur: false,
        events: {
            keyup: function (editor, event) {
                let msg = this.getText();
                if (event.keyCode == 13 && msg != '') {
                    let storedata = {
                        message: msg,
                        to_id: assignedChatUserId,
                        from_id: userId,
                    };
                    storeMessage(storedata);
                }
            },
        },
    });

}

function resetForm () {
    textMessageEle[0].emojioneArea.setText('');
}

function privateMessageReadByUser (e) {

    let readClass = 'chat-container__read-status--read';
    let unreadClass = 'chat-container__read-status--unread';
    $.each(e.ids, function (i, v) {
        $('.message-' + v).
            find('.chat-container__read-status').
            removeClass(unreadClass).
            addClass(readClass);
    });
}

// Function for remove chat from conversation body
function messageDeletedForEveryone (e) {
    if (chatPeopleBodyEle.find('.message-' + e.id).length) {
        // if chat window is open
        $('.message-' + e.id).remove();
    }
}

//Toggle chat and links
function toggleFab () {
    $('.chat').toggleClass('is-visible');
    $('.fab').toggleClass('is-visible');
}

// get Message when page is load or when msg successfully send
function getMsg () {
    $.ajax({
        type: 'GET',
        url: '/user/' + userId + '/conversation',
        success: function (data) {
            let conversations = data.data.conversations;
            if (conversations.length !== null) {
                fireReadMessageEvent(conversations);
                $('#chat_fullscreen').html(
                    conversations.reverse().
                        map(prepareChatConversation).
                        join(''));
            }
            scrollToBottomFunc();
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
            if (result.responseJSON.message = 'User not found.') {
                deleteCookie('chat_user');
                $('.msg_chat').removeClass('d-block');
                $('.msg_form').removeClass('d-none');
                $('.msg_chat').addClass('d-none');
                $('.msg_form').addClass('d-block');
            }
        },
        complete: function () {
        },
    });
}

function singleMessage (conversations) {
    let data = [
        {
            'conversations': conversations,
            'time': moment(conversations.created_at).format('h:mm a'),
            'message': conversations.message,
        }];
    return prepareTemplateRender('#tmplSingleMessage', data);
}

// make a function to scroll down auto
function scrollToBottomFunc () {
    $('#chat_fullscreen').animate({
        scrollTop: $('#chat_fullscreen').get(0).scrollHeight,
    }, 10);
}

window.loadAssignedChatUserId = function () {
    $.ajax({
        type: 'GET',
        url: '/get-assign-agent',
        data: {
            id: userId,
        },
        success: function (data) {
            if (typeof data.data === 'object') {
                const { id, photo_url, name } = data.data.agent;
                $('#chat_head').text(name);
                $('.chat_header .header_img img').attr('src', photo_url);
                assignedChatUserId = id;
            } else {
                assignedChatUserId = data.data;
            }
        },
    });
};

function deleteCookie (cname) {
    document.cookie = cname + '=;expires=' + new Date(0).toUTCString();
}

window.storeMessage = function (reqData) {
    let message = reqData.message.trim();
    if (message === '') {
        return false;
    }
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

    $.ajax({
        type: 'POST',
        url: conversationsStoreUrl,
        data: reqData,
        success: function (data) {
            reqData.reply_to = '';
            if (data.success === true) {
                let messageData = data.data.message;

                setSentOrReceivedMessage(messageData);
                if (messageData.message_type === 0) {
                    $('.chat-conversation').
                        find('[data-message_id=' + randomMsgId + ']').
                        addClass('message-' + messageData.id).
                        attr('data-message_id', messageData.id);

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
            }
        },
        error: function (error) {
            reqData.reply_to = ''
            displayToastr(Lang.get('messages.error_message.error'), 'error',
                error.responseJSON.message)
            $('#btnSend').removeClass('chat__area-send-btn--disable')
        },
    });
};

function addMessage (data) {
    let messageData = data;
    messageData.message = data.message.replace(/(<([^>]+)>)/ig, '');
    let currentTime = moment().tz(timeZone).format('hh:mma');
    if (isUTCTimezone == '1') {
        currentTime = getLocalDate(moment().utc());
    }
    messageData.time = currentTime;
    // messageData.senderName = authUserName;
    // messageData.senderImg = authImgURL;
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
    $('#no_chat_msg').hide();
    $('.chat-conversation').append(htmlOutput);

    scrollToLastMessage();
    resetForm();
    return randomMsgId;
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
window.getLocalDate = function (dateTime, format = 'hh:mma') {
    if (isUTCTimezone == '0') {
        return moment(dateTime).format(format);
    }

    let date = moment(dateTime).utc(dateTime).local();
    return date.calendar(null, {
        sameDay: format,
        lastDay: '[Yesterday]',
        lastWeek: 'M/D/YY',
        sameElse: 'M/D/YY',
    });
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
window.setSentOrReceivedMessage = function (message) {
    let recentChatMessage = prepareChatConversation(message, false);

    let chatConversationEle = $('.chat-conversation');
    let needToScroll = false;
    // displaySideMedia(message);

    if (message.from_id == userId) {
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
    // updateMessageInSenderwindow(message);

};

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
        },
    });
}

window.fireReadMessageEvent = function (conversations) {
    let unreadMessageIds = getUnreadMessageIds(conversations);
    fireReadMessageEventUsingIds(unreadMessageIds);
};

window.getUnreadMessageIds = function (conversations) {
    let ids = [];
    $.each(conversations, function (index, conversation) {
        if (conversation.to_id == userId && !conversation.status) {
            ids.push(conversation.id);
        }
    });

    return ids;
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
        // isAllMessagesRead = true;
    }
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
    let className = (data.from_id == userId)
        ? 'chat-conversation__sender'
        : (!data.status)
            ? 'chat-conversation__receiver unread'
            : 'chat-conversation__receiver';

    let readUnread = (data.status == 1 ||
        (data.hasOwnProperty('read_by_all_count') &&
            data.read_by_all_count === 0))
        ? 'chat-container__read-status--read'
        : 'chat-container__read-status--unread';

    if (className.includes('chat-conversation__receiver')) {
        isReceiver = true;
    }

    let allowToDelete = false;
    let deleteMsgForEveryone = false;

    let templateData = {};
    let helpers = {
        displayMessage: displayMessage,
        getChatMagTimeInConversation: getChatMagTimeInConversation,
    };
    let template = $.templates('#tmplMessage');
    templateData.data = data;
    templateData.isReceiver = isReceiver;
    templateData.loggedInUserId = userId;

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

window.imageRenderer = function (message) {
    return `<a href="${message}" target="blank" data-fancybox="gallery" data-toggle="lightbox" data-gallery="example-gallery" data-src="${message}"><img src="${message}"></a>`;
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
    if (fileName.length > 15) {
        fileName = fileName.substring(0, 15) + '...';
    }
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
    let messageClassName = (data.from_id != userId)
        ? 'float-right'
        : 'float-left';
    let rendererClassName = (data.from_id != userId)
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
window.renderYoutubeURL = function (url, redererClassName = '') {
    let newUrl = getYoutubeEmbedURL(url);
    return `<iframe width="246" height="246" style="border-radius:8px;" class="` +
        redererClassName + `" src="${newUrl}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen>
            </iframe>`;
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
window.getCalenderFormatForTimeLine = function (dateTime) {
    return moment(dateTime).utc(dateTime).local().calendar(null, {
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
window.getChatMagTimeInConversation = function (
    dateTime, format = 'h:mma') {

    if (isUTCTimezone == '0') {
        return moment(dateTime).format(format);
    }

    return moment.utc(dateTime).local().format(format);
};
window.getMessageByScroll = function () {
    $('.chat-conversation').on('scroll', function () {
        if ($(this).scrollTop() === 0) {
            shouldCallApiTop = (callBeforeAPI) ? true : false;
            if (shouldCallApiTop === true) {
                let reqData = {
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
window.getYoutubeEmbedURL = function (url) {
    let newUrl = url;
    let regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
    let match = url.match(regExp);
    if (match && match[2].length === 11) {
        newUrl = 'https://www.youtube.com/embed/' + match[2] + '';
    }
    return newUrl;
};
window.detectUrlFromTextMessage = function (message) {
    let regex = /((http|https|ftp):\/\/[a-zа-я0-9\w?=&.\/-;#~%-]+(?![a-zа-я0-9\w\s?&.\/;#~%"=-]*>))/g;
    // Replace plain text links by hyperlinks
    return message.replace(regex,
        '<a href=\'$1\' target=\'_blank\'>$1</a>');
};
window.updateUserStatus = function (user, status) {
    //recent chat-list ele
    let UserEle = chatPeopleBodyEle.find('#user-' + user.id);
    //new conversation ele (in pop up)
    let newUserEle = $('.user-' + user.id);
    let newUserEleParent = $('.chat-user-' + user.id);

    /** Do not show user status when user is blocked */
    if ($.inArray(user.id, blockedUsersList) != -1) {
        return;
    }

    if (status == 1) {
        UserEle.find('.chat__person-box-status').
            removeClass('chat__person-box-status--offline').
            addClass('chat__person-box-status--online');

        //conversation
        if ($('#toId').val() == user.id) {
            $('.typing').html('online');

            //user profile
            $('.chat-profile__person-status').show().text('online');
            $('.chat-profile__person-last-seen').hide();
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
        $('.chat-profile__person-last-seen').show().text(last_seen);
        $('.chat-profile__person-status').hide();
    }
};
