'use strict';

function loadEmoji () {
    $('#userStatusEmoji').emojioneArea({
        standalone: true,
        autocomplete: false,
        saveEmojisAs: 'shortname',
        pickerPosition: 'right',
    });
}

loadEmoji();

$(document).on('click', '#setUserStatus', function (e) {
    e.preventDefault();
    let emojiShortName = $('#userStatusEmoji').
        data('emojioneArea').
        getText().
        trim();
    let emoji = emojione.shortnameToImage(emojiShortName);
    let data = {
        'emoji': emoji,
        'emoji_short_name': emojiShortName,
        'status': $('#userStatus').val(),
    };

    $.ajax({
        type: 'post',
        url: setUserCustomStatusUrl,
        data: data,
        success: function (data) {
            displayToastr(Lang.get('messages.success_message.success'),
                'success', data.message)
            $('#setCustomStatusModal').modal('hide')
        },
        error: function (result) {
            displayToastr(Lang.get('messages.error_message.error'), 'error',
                result.responseJSON.message)
        },
    });
});

$(document).on('click', '#clearUserStatus', function (e) {
    e.preventDefault();
    $.ajax({
        type: 'get',
        url: clearUserCustomStatusUrl,
        success: function (data) {
            $('#userStatus').val('')
            $('#userStatusEmoji')[0].emojioneArea.setText('')
            displayToastr(Lang.get('messages.success_message.success'),
                'success', data.message)
            $('#setCustomStatusModal').modal('hide')
        },
        error: function (result) {
            displayToastr(Lang.get('messages.error_message.error'), 'error',
                result.responseJSON.message)
        },
    });
});

if (loggedInUserStatus != '' && loggedInUserStatus.hasOwnProperty('status')) {
    $('#userStatus').val(loggedInUserStatus.status);
    $('#userStatusEmoji')[0].emojioneArea.setText(
        loggedInUserStatus.emoji_short_name);
}
