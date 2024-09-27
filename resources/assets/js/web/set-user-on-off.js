'use strict';

window.setLastSeenOfUser = function (status) {
    $.ajax({
        type: 'post',
        url: setLastSeenURL,
        data: {
            status: status,
            'userId': JSON.parse(getCookie('chat_user')).id,
        },
        success: function (data) {
        },
    });
};

//set user status online
if (getCookie('chat_user') != '') {
    setLastSeenOfUser(1);
}

window.onbeforeunload = function () {
    Echo.leave('user-status');
    if (getCookie('chat_user') != '') {
        setLastSeenOfUser(0);
    }
    //return undefined; to prevent dialog while window.onbeforeunload
    return undefined;
};

// Echo.join(`user-status`);
