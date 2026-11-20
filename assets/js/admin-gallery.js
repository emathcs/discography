jQuery(document).all ? jQuery(window) : jQuery(document).ready(function($) {
    var galleryFrame;
    // Set an image
    $('.discography-upload-btn').on('click', function(e) {
        e.preventDefault();
        // If the media frame already exists, reopen it.
        if (galleryFrame) {
            galleryFrame.open();
            return;
        }
        // Create the media frame
        galleryFrame = wp.media({
            title: 'Select Images for Gallery',
            button: {
                text: 'Add to Gallery'
            },
            multiple: false, // Allow only one image selection
            library: {
                type: 'image' // Restrict selection to images only
            }
        });
        // When images are selected, run a callback
        galleryFrame.on('select', function() {
            var selection = galleryFrame.state().get('selection');
            var imageIds = [];
            var previewHtml = '';

            selection.map(function(attachment) {
                attachment = attachment.toJSON();
                // Collect image attachment IDs
                imageIds.push(attachment.id);
                // Build a quick thumbnail preview for the backend
                var thumbnailUrl = attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
                previewHtml += '<img src="' + thumbnailUrl + '"/>';
            });

            // Populate the input field with a comma-separated list of IDs
            $('#image-id').val(imageIds);
            // Update the preview and the buttons
            $('#upload-btn').addClass('discography-admin-hide');
            $('#remove-btn').removeClass('discography-admin-hide');
            $('#discography-image-gallery-item-preview').html(previewHtml);
            $('#discography-image-gallery-item-upload').addClass('discography-admin-hide');
            $('#discography-image-gallery-item-preview').removeClass('discography-admin-hide');
        });

        // Finally, open the modal
        galleryFrame.open();
    });

    // Removing the image
    $('.discography-remove-btn').on('click', function(e) {
        // Remove the image from the hidden imput
        $('#image-id').val('');
        // Update the preview and the buttons
        $('#discography-image-gallery-item-preview').html('');
        $('#remove-btn').addClass('discography-admin-hide');
        $('#upload-btn').removeClass('discography-admin-hide');
        $('#discography-image-gallery-item-preview').addClass('discography-admin-hide');
        $('#discography-image-gallery-item-upload').removeClass('discography-admin-hide');
    });

    //
    $('.delete-record').on('click', function(e) {
        var id = $(this).attr('data-id');
        var model = $(this).attr('data-model');
        $('#delete-' + model + '-id').val(id);
        $('#delete-' + model).val(model);
    });
});
