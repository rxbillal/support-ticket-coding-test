'use strict';

$(document).ready(function () {
    $('#publicTicketsSearch').on('keyup', function () {
        let searchTerm = $(this).val();
        if (searchTerm != '') {
            $.ajax({
                url: publicTicketSearchUrl,
                method: "GET",
                data: {searchTerm : searchTerm},
                success: function (result) {
                    $('#publicTicketsSearchResults').fadeIn();
                    $('#publicTicketsSearchResults').html(result);
                },
            });
        } else {
            $('#publicTicketsSearchResults').fadeOut();
        }
    });
});
