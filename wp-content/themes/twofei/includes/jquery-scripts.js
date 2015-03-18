jQuery(document).ready(function() {
    jQuery('#upload-favicon-button').click(function() {
        uploadfield = '#upload-favicon';
		formfield = jQuery(uploadfield).attr('name');
		tbframe_interval = setInterval(function() {jQuery('#TB_iframeContent').contents().find('.savesend .button').val(picochic_localizing_upload_js.use_this_image);}, 2000);
        tb_show('', 'media-upload.php?type=image&TB_iframe=true');

        return false;
    });

    jQuery('#upload-logo-button').click(function() {
        uploadfield = '#upload-logo';
		formfield = jQuery(uploadfield).attr('name');
		tbframe_interval = setInterval(function() {jQuery('#TB_iframeContent').contents().find('.savesend .button').val(picochic_localizing_upload_js.use_this_image);}, 2000);
        tb_show('', 'media-upload.php?type=image&TB_iframe=true');

        return false;
    });

    jQuery('#remove-favicon-button').click(function() {
   		jQuery('#upload-favicon').val('');
	});

	jQuery('#remove-logo-button').click(function() {
   		jQuery('#upload-logo').val('');		
	});

	jQuery('#set_default_color').click(function() {
		jQuery('#custom_color').val('#364D96');
	});

	jQuery('#custom_header_height_default').click(function() {
		jQuery('#custom_header_height').val('42');
	});

    window.send_to_editor = function(html) {
        imgurl = jQuery('img', html).attr('src');
        jQuery(uploadfield).val(imgurl);
        tb_remove();
    }
});
