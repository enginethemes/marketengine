jQuery(document).ready(function($) {
    $('#dispute-file').jUploader({
        browse_button: 'me-dipute-upload',
        multi: true,
        name: 'dispute_file',
        extension: 'jpg,jpeg,gif,png',
        upload_url: me_globals.ajaxurl + '?nonce=' + $('#me-dispute-file').val(),
        maxsize: '2mb',
        maxcount: 5,
    });
    /* ajax send debate message in dispute details */
    $('#dispute-message-form').submit(function(e) {
        e.preventDefault();
        var content = $('#debate_content').val();
        if (!content) {
            $('#debate_content').focus();
        } else {
            $.post({
                url: me_globals.ajaxurl,
                data: {
                    action: 'me_dispute_debate',
                    dispute: $('#dispute_id').val(),
                    _wpnonce: $('#_debate_nonce').val(),
                    content: content
                }
            });
        }
    });
});