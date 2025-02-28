/**
 * Admin JavaScript for Event Attendee Management System
 */
jQuery(document).ready(function($) {
    // Initialize DataTables
    if ($('#eams-attendees-table').length) {
        $('#eams-attendees-table').DataTable({
            responsive: true,
            order: [[0, 'desc']],
            language: {
                search: "Search attendees:",
                lengthMenu: "Show _MENU_ attendees per page",
                info: "Showing _START_ to _END_ of _TOTAL_ attendees"
            }
        });
    }
    
    // Initialize datepickers
    $('.eams-datepicker').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true
    });
    
    // Add attendee form submission
    $('#eams-add-attendee-form').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $submitButton = $('#eams-submit');
        const originalButtonText = $submitButton.val();
        
        $submitButton.val('Processing...').prop('disabled', true);
        
        $.ajax({
            url: eams_params.ajax_url,
            type: 'POST',
            data: $form.serialize(),
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    window.location.href = response.data.redirect;
                } else {
                    alert(response.data.message);
                    $submitButton.val(originalButtonText).prop('disabled', false);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
                $submitButton.val(originalButtonText).prop('disabled', false);
            }
        });
    });
    
    // QR Code generation
    $('.eams-qr-code-btn').on('click', function() {
        const attendeeId = $(this).data('id');
        const $modal = $('#eams-qr-modal');
        const $qrImage = $('.eams-qr-image');
        const $qrName = $('.eams-qr-name');
        const $qrId = $('.eams-qr-id');
        
        $qrImage.html('<p>Loading...</p>');
        $modal.css('display', 'block');
        
        $.ajax({
            url: eams_params.ajax_url,
            type: 'POST',
            data: {
                action: 'eams_get_qr_code',
                id: attendeeId,
                nonce: eams_params.nonce
            },
            success: function(response) {
                if (response.success) {
                    const qrCodeImg = $('<img>', {
                        src: response.data.qr_code,
                        alt: 'QR Code',
                        class: 'eams-qr-code-img'
                    });
                    
                    $qrImage.html(qrCodeImg);
                    $qrName.text('Name: ' + response.data.attendee_name);
                    $qrId.text('ID: ' + response.data.attendee_id);
                    
                    // Enable download button
                    $('.eams-download-qr').data('qr', response.data.qr_code).data('filename', 'qr-' + response.data.attendee_id);
                } else {
                    $qrImage.html('<p>Error: ' + response.data.message + '</p>');
                }
            },
            error: function() {
                $qrImage.html('<p>An error occurred. Please try again.</p>');
            }
        });
    });
    
    // Download QR Code
    $('.eams-download-qr').on('click', function() {
        const qrCodeData = $(this).data('qr');
        const filename = $(this).data('filename');
        
        if (qrCodeData) {
            const a = document.createElement('a');
            a.href = qrCodeData;
            a.download = filename + '.png';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
    });
    
    // Print QR Code
    $('.eams-print-qr').on('click', function() {
        const $qrContent = $('.eams-qr-content').clone();
        const $printWindow = window.open('', '_blank', 'width=600,height=600');
        
        $printWindow.document.write('<html><head><title>Print QR Code</title>');
        $printWindow.document.write('<style>');
        $printWindow.document.write('body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }');
        $printWindow.document.write('.eams-qr-content { max-width: 400px; margin: 0 auto; }');
        $printWindow.document.write('.eams-qr-image img { max-width: 100%; height: auto; }');
        $printWindow.document.write('.eams-qr-info { margin-top: 20px; }');
        $printWindow.document.write('.eams-qr-actions { display: none; }');
        $printWindow.document.write('</style>');
        $printWindow.document.write('</head><body>');
        $printWindow.document.write($qrContent.html());
        $printWindow.document.write('</body></html>');
        
        $printWindow.document.close();
        $printWindow.focus();
        
        setTimeout(function() {
            $printWindow.print();
            $printWindow.close();
        }, 500);
    });
    
    // Delete attendee
    $('.eams-delete-btn').on('click', function() {
        const attendeeId = $(this).data('id');
        const $confirmModal = $('#eams-confirm-modal');
        
        $confirmModal.css('display', 'block');
        $('.eams-confirm-delete-btn').data('id', attendeeId);
    });
    
    // Confirm delete
    $('.eams-confirm-delete-btn').on('click', function() {
        const attendeeId = $(this).data('id');
        const $confirmModal = $('#eams-confirm-modal');
        
        $.ajax({
            url: eams_params.ajax_url,
            type: 'POST',
            data: {
                action: 'eams_delete_attendee',
                id: attendeeId,
                nonce: eams_params.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message);
                }
                $confirmModal.css('display', 'none');
            },
            error: function() {
                alert('An error occurred. Please try again.');
                $confirmModal.css('display', 'none');
            }
        });
    });
    
    // Cancel delete
    $('.eams-cancel-btn').on('click', function() {
        $('#eams-confirm-modal').css('display', 'none');
    });
    
    // Close modals
    $('.eams-modal-close').on('click', function() {
        $(this).closest('.eams-modal').css('display', 'none');
    });
    
    // Close modal when clicking outside
    $(window).on('click', function(e) {
        if ($(e.target).hasClass('eams-modal')) {
            $('.eams-modal').css('display', 'none');
        }
    });
});