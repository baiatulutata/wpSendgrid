jQuery(document).ready(function($) {
    $('#sendgrid-test-email').on('click', function(e) {
        e.preventDefault();

        const toEmail = $('#sendgrid-test-email-to').val();

        $('#sendgrid-test-result').text('Sending...');

        $.post(sendgrid_ajax.ajax_url, {
            action: 'sendgrid_send_test_email',
            to: toEmail,
            nonce: sendgrid_ajax.nonce || ''
        }, function(response) {
            if (response.success) {
                $('#sendgrid-test-result').text(response.data);
            } else {
                $('#sendgrid-test-result').text('Error: ' + response.data);
            }
        });
    });
});
