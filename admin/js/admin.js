/**
 * Callback function for the 'click' event of the 'Set Footer Image'
 * anchor in its meta box.
 *
 * Displays the media uploader for selecting an image.
 *
 * @param    object    $    A reference to the jQuery object
 * @since    1.0.0
 */
function renderMediaUploader($) {
    'use strict';

    var file_frame, image_data, json;

    /**
     * If an instance of file_frame already exists, then we can open it
     * rather than creating a new instance.
     */
    if (undefined !== file_frame) {

        file_frame.open();
        return;

    }

    /**
     * If we're this far, then an instance does not exist, so we need to
     * create our own.
     *
     * Here, use the wp.media library to define the settings of the Media
     * Uploader. We're opting to use the 'post' frame which is a template
     * defined in WordPress core and are initializing the file frame
     * with the 'insert' state.
     *
     * We're also not allowing the user to select more than one image.
     */
    file_frame = wp.media.frames.file_frame = wp.media({
        frame: 'post',
        state: 'insert',
        multiple: false
    });

    /**
     * Setup an event handler for what to do when an image has been
     * selected.
     *
     * Since we're using the 'view' state when initializing
     * the file_frame, we need to make sure that the handler is attached
     * to the insert event.
     */
    file_frame.on('insert', function () {

        // Read the JSON data returned from the Media Uploader
        json = file_frame.state().get('selection').first().toJSON();

        var src = json.sizes.x_slider_full ? json.sizes.x_slider_full.url : json.url;


        // First, make sure that we have the URL of an image to display
        if (0 > $.trim(json.url.length)) {
            return;
        }

        // After that, set the properties of the image and display it
        $('#x-slider-slider-image-container')
            .children('img')
            .attr('src', src)
            .attr('alt', json.caption)
            .attr('title', json.title)
            .show()
            .parent()
            .removeClass('hidden');

        // Next, hide the anchor responsible for allowing the user to select an slide
        $('#x-slider-choose-slider')
            .parent()
            .hide();

        // Display the anchor for the removing the featured image
        $('.x-slider-controls')
            .removeClass('hidden')
            .find('input[type=checkbox]')
            .attr('checked', 'checked');

        // Store the image's information into the meta data fields
        $('#x-slider-slider-src').val(src);
        $('#x-slider-slider-title').val(json.title);

    });

    // Now display the actual file_frame
    file_frame.open();

}

/**
 * Callback function for the 'click' event of the 'Remove Footer Image'
 * anchor in its meta box.
 *
 * Resets the meta box by hiding the image and by hiding the 'Remove
 * Footer Image' container.
 *
 * @param    object    $    A reference to the jQuery object
 * @since    1.0.0
 */
function resetUploadForm($) {
    'use strict';

    // First, we'll hide the image
    $('#x-slider-slider-image-container')
        .children('img')
        .hide();

    // Then display the previous container
    $('#x-slider-choose-slider')
        .parent()
        .show();

    $('.x-slider-controls')
        .addClass('hidden')
        .find('input[type=checkbox]')
        .removeAttr('checked');

    // Finally, we reset the meta data input fields
    $('#x-slider-slider-image-info')
        .children()
        .val('');
}

/**
 * Checks to see if the input field for the thumbnail source has a value.
 * If so, then the image and the 'Remove featured image' anchor are displayed.
 *
 * Otherwise, the standard anchor is rendered.
 *
 * @param    object    $    A reference to the jQuery object
 * @since    1.0.0
 */
function renderFeaturedImage($) {

    /* If a thumbnail URL has been associated with this image
     * Then we need to display the image and the reset link.
     */
    if ('' !== $.trim($('#x-slider-slider-src').val())) {

        $('#x-slider-slider-image-container').removeClass('hidden');

        $('#x-slider-choose-slider')
            .parent()
            .hide();

        $('#x-slider-remove-slider')
            .parent()
            .removeClass('hidden');

        $('.x-slider-controls')
            .removeClass('hidden');

    }

}

(function ($) {
    'use strict';

    $(function () {

        renderFeaturedImage($);

        $('#x-slider-choose-slider').on('click', function (evt) {
            evt.preventDefault();
            renderMediaUploader($);
        });

        $('#x-slider-remove-slider').on('click', function (evt) {

            // Stop the anchor's default behavior
            evt.preventDefault();

            // Remove the image, toggle the anchors
            resetUploadForm($);

        });

    });

})(jQuery);