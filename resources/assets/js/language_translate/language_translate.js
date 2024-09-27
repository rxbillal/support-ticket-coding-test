'use strict';
$(document).ready(function () {
    $('#lang, #file').select2();

    $('#lang,#file').on('change',function (){
        loadLang($('#lang').val(),$('#file').val());
    });

    function loadLang(lang,file){
        $('.selected-lang-messages').slideUp(100);
        $.ajax({
            url: translationUrl + `?lang=${lang}&file=${file}`,
            success:function(data){
                $('.selected-lang-messages').html(data);
                $('.selected-lang-messages').slideDown(100);
            }
        });
    }
});

$(document).on('click', '.addModal', function () {
    $('#addModal').appendTo('body').modal('show');
});

$(document).on('submit','#addNewLanguage', function (e) {
    e.preventDefault();
    processingBtn('#addNewLanguage', '#btnSave', 'loading');
    $.ajax({
        url: route('translation-manager.store'),
        type: 'POST',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#addModal').modal('hide');
                setTimeout(function () {
                    location.reload();
                }, 2000);
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
            processingBtn('#addNewLanguage', '#btnSave');
        },
        complete: function () {
            processingBtn('#addNewLanguage', '#btnSave');
        },
    });
});

$('#addModal').on('hidden.bs.modal', function () {
    resetModalForm('#addNewLanguage', '#validationErrorsBox');
});
