<?php
/**
 * Handles all admin ajax interactions for the Soliloquy plugin.
 *
 * @since 1.0.0
 *
 * @package Soliloquy
 * @author  Thomas Griffin
 */

add_action( 'wp_ajax_soliloquy_change_type', 'soliloquy_ajax_change_type' );
/**
 * Changes the type of slider to the user selection.
 *
 * @since 1.0.0
 */
function soliloquy_ajax_change_type() {

    // Run a security check first.
    check_ajax_referer( 'soliloquy-change-type', 'nonce' );

    // Prepare variables.
    $post_id = absint( $_POST['post_id'] );
    $post    = get_post( $post_id );
    $type    = stripslashes( $_POST['type'] );

    // Retrieve the data for the type selected.
    ob_start();
    $instance = Soliloquy_Metaboxes::get_instance();
    $instance->images_display( $type, $post );
    $html = ob_get_clean();

    // Send back the response.
    echo json_encode( array( 'type' => $type, 'html' => $html ) );
    die;

}

add_action( 'wp_ajax_soliloquy_load_image', 'soliloquy_ajax_load_image' );
/**
 * Loads an image into a slider.
 *
 * @since 1.0.0
 */
function soliloquy_ajax_load_image() {

    // Run a security check first.
    check_ajax_referer( 'soliloquy-load-image', 'nonce' );

    // Prepare variables.
    $id      = absint( $_POST['id'] );
    $post_id = absint( $_POST['post_id'] );

    // Set post meta to show that this image is attached to one or more Soliloquy sliders.
    $has_slider = get_post_meta( $id, '_sol_has_slider', true );
    if ( empty( $has_slider ) ) {
        $has_slider = array();
    }

    $has_slider[] = $post_id;
    update_post_meta( $id, '_sol_has_slider', $has_slider );

    // Set post meta to show that this image is attached to a slider on this page.
    $in_slider = get_post_meta( $post_id, '_sol_in_slider', true );
    if ( empty( $in_slider ) ) {
        $in_slider = array();
    }

    $in_slider[] = $id;
    update_post_meta( $post_id, '_sol_in_slider', $in_slider );

    // Set data and order of image in slider.
    $slider_data = get_post_meta( $post_id, '_sol_slider_data', true );
    if ( empty( $slider_data ) ) {
        $slider_data = array();
    }

    // If no slider ID has been set, set it now.
    if ( empty( $slider_data['id'] ) ) {
        $slider_data['id'] = $post_id;
    }

    // Set data and update the meta information.
    $slider_data = soliloquy_ajax_prepare_slider_data( $slider_data, $id );
    update_post_meta( $post_id, '_sol_slider_data', $slider_data );

    // Run hook before building out the item.
    do_action( 'soliloquy_ajax_load_image', $id, $post_id );

    // Build out the individual HTML output for the slider image that has just been uploaded.
    $html = Soliloquy_Metaboxes::get_instance()->get_slider_item( $id, $slider_data['slider'][$id], 'image', $post_id );

    // Flush the slider cache.
    Soliloquy_Common::get_instance()->flush_slider_caches( $post_id );

    echo json_encode( $html );
    die;

}

add_action( 'wp_ajax_soliloquy_load_library', 'soliloquy_ajax_load_library' );
/**
 * Loads the Media Library images into the media modal window for selection.
 *
 * @since 1.0.0
 */
function soliloquy_ajax_load_library() {

    // Run a security check first.
    check_ajax_referer( 'soliloquy-load-slider', 'nonce' );

    // Prepare variables.
    $offset  = (int) $_POST['offset'];
    $post_id = absint( $_POST['post_id'] );
    $html    = '';

    // Grab the library contents with the included offset parameter.
    $library = get_posts( array( 'post_type' => 'attachment', 'post_mime_type' => 'image', 'post_status' => 'any', 'posts_per_page' => 20, 'offset' => $offset ) );
    if ( $library ) {
        foreach ( (array) $library as $image ) {
            $has_slider = get_post_meta( $image->ID, '_sol_has_slider', true );
            $class       = $has_slider && in_array( $post_id, (array) $has_slider ) ? ' selected soliloquy-in-slider' : '';

            $html .= '<li class="attachment' . $class . '" data-attachment-id="' . absint( $image->ID ) . '">';
                $html .= '<div class="attachment-preview landscape">';
                    $html .= '<div class="thumbnail">';
                        $html .= '<div class="centered">';
                            $src = wp_get_attachment_image_src( $image->ID, 'thumbnail' );
                            $html .= '<img src="' . esc_url( $src[0] ) . '" />';
                        $html .= '</div>';
                    $html .= '</div>';
                    $html .= '<a class="check" href="#"><div class="media-modal-icon"></div></a>';
                $html .= '</div>';
            $html .= '</li>';
        }
    }

    echo json_encode( array( 'html' => stripslashes( $html ) ) );
    die;

}

add_action( 'wp_ajax_soliloquy_library_search', 'soliloquy_ajax_library_search' );
/**
 * Searches the Media Library for images matching the term specified in the search.
 *
 * @since 1.0.0
 */
function soliloquy_ajax_library_search() {

    // Run a security check first.
    check_ajax_referer( 'soliloquy-library-search', 'nonce' );

    // Prepare variables.
    $search  = stripslashes( $_POST['search'] );
    $post_id = absint( $_POST['post_id'] );
    $html    = '';

    // Grab the library contents with the included offset parameter.
    $library = get_posts( array( 'post_type' => 'attachment', 'post_mime_type' => 'image', 'post_status' => 'any', 'posts_per_page' => -1, 's' => $search ) );
    if ( $library ) {
        foreach ( (array) $library as $image ) {
            $has_slider = get_post_meta( $image->ID, '_sol_has_slider', true );
            $class       = $has_slider && in_array( $post_id, (array) $has_slider ) ? ' selected soliloquy-in-slider' : '';

            $html .= '<li class="attachment' . $class . '" data-attachment-id="' . absint( $image->ID ) . '">';
                $html .= '<div class="attachment-preview landscape">';
                    $html .= '<div class="thumbnail">';
                        $html .= '<div class="centered">';
                            $src = wp_get_attachment_image_src( $image->ID, 'thumbnail' );
                            $html .= '<img src="' . esc_url( $src[0] ) . '" />';
                        $html .= '</div>';
                    $html .= '</div>';
                    $html .= '<a class="check" href="#"><div class="media-modal-icon"></div></a>';
                $html .= '</div>';
            $html .= '</li>';
        }
    }

    echo json_encode( array( 'html' => stripslashes( $html ) ) );
    die;

}

add_action( 'wp_ajax_soliloquy_insert_slides', 'soliloquy_ajax_insert_slides' );
/**
 * Inserts one or more slides into a slider.
 *
 * @since 1.0.0
 */
function soliloquy_ajax_insert_slides() {

    // Run a security check first.
    check_ajax_referer( 'soliloquy-insert-images', 'nonce' );

    // Prepare variables.
    $images  = ! empty( $_POST['images'] ) ? stripslashes_deep( (array) $_POST['images'] ) : array();
    $videos  = ! empty( $_POST['videos'] ) ? stripslashes_deep( (array) $_POST['videos'] ) : array();
    $html    = ! empty( $_POST['html'] )   ? stripslashes_deep( (array) $_POST['html'] )   : array();
    $post_id = absint( $_POST['post_id'] );

    // Grab and update any slider data if necessary.
    $in_slider = get_post_meta( $post_id, '_sol_in_slider', true );
    if ( empty( $in_slider ) ) {
        $in_slider = array();
    }

    // Set data and order of image in slider.
    $slider_data = get_post_meta( $post_id, '_sol_slider_data', true );
    if ( empty( $slider_data ) ) {
        $slider_data = array();
    }

    // If no slider ID has been set, set it now.
    if ( empty( $slider_data['id'] ) ) {
        $slider_data['id'] = $post_id;
    }

    // Loop through the images and add them to the slider.
    foreach ( (array) $images as $i => $id ) {
        // Update the attachment image post meta first.
        $has_slider = get_post_meta( $id, '_sol_has_slider', true );
        if ( empty( $has_slider ) ) {
            $has_slider = array();
        }

        $has_slider[] = $post_id;
        update_post_meta( $id, '_sol_has_slider', $has_slider );

        // Now add the image to the slider for this particular post.
        $in_slider[] = $id;
        $slider_data = soliloquy_ajax_prepare_slider_data( $slider_data, $id );
    }

    // Loop through the videos and add them to the slider.
    foreach ( (array) $videos as $i => $data ) {
        // Pass over if the main items necessary for the video are not set.
        if ( ! isset( $data['title'] ) || ! isset( $data['url'] ) || ! isset( $data['thumb'] ) ) {
            continue;
        }

        // Generate a custom ID for the video.
        $id = sanitize_title_with_dashes( $slider_data['id'] . '-' . $data['title'] );

        // Now add the image to the slider for this particular post.
        $in_slider[] = $id;
        $slider_data = soliloquy_ajax_prepare_slider_data( $slider_data, $id, 'video', $data );
    }

    // Loop through the videos and add them to the slider.
    foreach ( (array) $html as $i => $data ) {
        // Pass over if the main items necessary for the video are not set.
        if ( empty( $data['title'] ) || empty( $data['code'] ) ) {
            continue;
        }

        // Generate a custom ID for the video.
        $id = sanitize_title_with_dashes( $slider_data['id'] . '-' . $data['title'] );

        // Now add the image to the slider for this particular post.
        $in_slider[] = $id;
        $slider_data = soliloquy_ajax_prepare_slider_data( $slider_data, $id, 'html', $data );
    }

    // Update the slider data.
    update_post_meta( $post_id, '_sol_in_slider', $in_slider );
    update_post_meta( $post_id, '_sol_slider_data', $slider_data );

    // Run hook before finishing.
    do_action( 'soliloquy_ajax_insert_slides', $images, $videos, $html, $post_id );

    // Flush the slider cache.
    Soliloquy_Common::get_instance()->flush_slider_caches( $post_id );

    echo json_encode( true );
    die;

}

add_action( 'wp_ajax_soliloquy_sort_images', 'soliloquy_ajax_sort_images' );
/**
 * Sorts images based on user-dragged position in the slider.
 *
 * @since 1.0.0
 */
function soliloquy_ajax_sort_images() {

    // Run a security check first.
    check_ajax_referer( 'soliloquy-sort', 'nonce' );

    // Prepare variables.
    $order       = explode( ',', $_POST['order'] );
    $post_id     = absint( $_POST['post_id'] );
    $slider_data = get_post_meta( $post_id, '_sol_slider_data', true );
    $new_order   = array();

    // Loop through the order and generate a new array based on order received.
    foreach ( $order as $id ) {
        $new_order['slider'][$id] = $slider_data['slider'][$id];
    }

    // Update the slider data.
    update_post_meta( $post_id, '_sol_slider_data', $new_order );

    // Flush the slider cache.
    Soliloquy_Common::get_instance()->flush_slider_caches( $post_id );

    echo json_encode( true );
    die;

}

add_action( 'wp_ajax_soliloquy_remove_slide', 'soliloquy_ajax_remove_slide' );
/**
 * Removes an image from a slider.
 *
 * @since 1.0.0
 */
function soliloquy_ajax_remove_slide() {

    // Run a security check first.
    check_ajax_referer( 'soliloquy-remove-slide', 'nonce' );

    // Prepare variables.
    $post_id     = absint( $_POST['post_id'] );
    $attach_id   = trim( $_POST['attachment_id'] );
    $slider_data = get_post_meta( $post_id, '_sol_slider_data', true );
    $in_slider   = get_post_meta( $post_id, '_sol_in_slider', true );
    $has_slider  = get_post_meta( $attach_id, '_sol_has_slider', true );

    // Unset the image from the slider, in_slider and has_slider checkers.
    unset( $slider_data['slider'][$attach_id] );

    if ( ( $key = array_search( $attach_id, (array) $in_slider ) ) !== false ) {
        unset( $in_slider[$key] );
    }

    if ( ( $key = array_search( $post_id, (array) $has_slider ) ) !== false ) {
        unset( $has_slider[$key] );
    }

    // Update the slider data.
    update_post_meta( $post_id, '_sol_slider_data', $slider_data );
    update_post_meta( $post_id, '_sol_in_slider', $in_slider );
    update_post_meta( $attach_id, '_sol_has_slider', $has_slider );

    // Run hook before finishing the reponse.
    do_action( 'soliloquy_ajax_remove_slide', $attach_id, $post_id );

    // Flush the slider cache.
    Soliloquy_Common::get_instance()->flush_slider_caches( $post_id );

    echo json_encode( true );
    die;

}

add_action( 'wp_ajax_soliloquy_save_meta', 'soliloquy_ajax_save_meta' );
/**
 * Saves the metadata for an image in a slider.
 *
 * @since 1.0.0
 */
function soliloquy_ajax_save_meta() {

    // Run a security check first.
    check_ajax_referer( 'soliloquy-save-meta', 'nonce' );

    // Prepare variables.
    $post_id     = absint( $_POST['post_id'] );
    $attach_id   = $_POST['attach_id'];
    $meta        = $_POST['meta'];
    $slider_data = get_post_meta( $post_id, '_sol_slider_data', true );

    // Save the different types of default meta fields for images, videos and HTML slides.
    if ( isset( $meta['title'] ) ) {
        $slider_data['slider'][$attach_id]['title'] = trim( esc_html( $meta['title'] ) );
    }

    if ( isset( $meta['alt'] ) ) {
        $slider_data['slider'][$attach_id]['alt'] = trim( esc_html( $meta['alt'] ) );
    }

    if ( isset( $meta['link'] ) ) {
        $slider_data['slider'][$attach_id]['link'] = esc_url( $meta['link'] );
    }

    if ( isset( $meta['caption'] ) ) {
        $slider_data['slider'][$attach_id]['caption'] = trim( $meta['caption'] );
    }

    if ( isset( $meta['url'] ) ) {
        $slider_data['slider'][$attach_id]['url'] = esc_url( $meta['url'] );
    }

    if ( isset( $meta['thumb'] ) ) {
        $slider_data['slider'][$attach_id]['thumb'] = esc_url( $meta['thumb'] );
        $slider_data['slider'][$attach_id]['src']   = esc_url( $meta['thumb'] );
    }

    if ( isset( $meta['code'] ) ) {
        $slider_data['slider'][$attach_id]['code'] = trim( $meta['code'] );
    }

    // Allow filtering of meta before saving.
    $slider_data = apply_filters( 'soliloquy_ajax_save_meta', $slider_data, $meta, $attach_id, $post_id );

    // Update the slider data.
    update_post_meta( $post_id, '_sol_slider_data', $slider_data );

    // Flush the slider cache.
    Soliloquy_Common::get_instance()->flush_slider_caches( $post_id );

    echo json_encode( true );
    die;

}

add_action( 'wp_ajax_soliloquy_refresh', 'soliloquy_ajax_refresh' );
/**
 * Refreshes the DOM view for a slider.
 *
 * @since 1.0.0
 */
function soliloquy_ajax_refresh() {

    // Run a security check first.
    check_ajax_referer( 'soliloquy-refresh', 'nonce' );

    // Prepare variables.
    $post_id = absint( $_POST['post_id'] );
    $slider = '';

    // Grab all slider data.
    $slider_data = get_post_meta( $post_id, '_sol_slider_data', true );

    // If there are no slider items, don't do anything.
    if ( empty( $slider_data ) || empty( $slider_data['slider'] ) ) {
        echo json_encode( array( 'error' => true ) );
        die;
    }

    // Loop through the data and build out the slider view.
    foreach ( (array) $slider_data['slider'] as $id => $data ) {
        $slider .= Soliloquy_Metaboxes::get_instance()->get_slider_item( $id, $data, $data['type'], $post_id );
    }

    echo json_encode( array( 'success' => $slider ) );
    die;

}

add_action( 'wp_ajax_soliloquy_load_slider_data', 'soliloquy_ajax_load_slider_data' );
/**
 * Retrieves and return slider data for the specified ID.
 *
 * @since 1.0.0
 */
function soliloquy_ajax_load_slider_data() {

    // Prepare variables and grab the slider data.
    $slider_id   = absint( $_POST['id'] );
    $slider_data = get_post_meta( $slider_id, '_sol_slider_data', true );

    // Send back the slider data.
    echo json_encode( $slider_data );
    die;

}

add_action( 'wp_ajax_soliloquy_install_addon', 'soliloquy_ajax_install_addon' );
/**
 * Installs an Soliloquy addon.
 *
 * @since 1.0.0
 */
function soliloquy_ajax_install_addon() {

    // Run a security check first.
    check_ajax_referer( 'soliloquy-install', 'nonce' );

    // Install the addon.
    if ( isset( $_POST['plugin'] ) ) {
        $download_url = $_POST['plugin'];
        global $hook_suffix;

        // Set the current screen to avoid undefined notices.
        set_current_screen();

        // Prepare variables.
        $method = '';
        $url    = add_query_arg(
            array(
                'page' => 'soliloquy-settings'
            ),
            admin_url( 'admin.php' )
        );

        // Start output bufferring to catch the filesystem form if credentials are needed.
        ob_start();
        if ( false === ( $creds = request_filesystem_credentials( $url, $method, false, false, null ) ) ) {
            $form = ob_get_clean();
            echo json_encode( array( 'form' => $form ) );
            die;
        }

        // If we are not authenticated, make it happen now.
        if ( ! WP_Filesystem( $creds ) ) {
            ob_start();
            request_filesystem_credentials( $url, $method, true, false, null );
            $form = ob_get_clean();
            echo json_encode( array( 'form' => $form ) );
            die;
        }

        // We do not need any extra credentials if we have gotten this far, so let's install the plugin.
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once plugin_dir_path( Soliloquy::get_instance()->file ) . 'includes/admin/skin.php';

        // Create the plugin upgrader with our custom skin.
        $installer = new Plugin_Upgrader( $skin = new Soliloquy_Skin() );
        $installer->install( $download_url );

        // Flush the cache and return the newly installed plugin basename.
        wp_cache_flush();
        if ( $installer->plugin_info() ) {
            $plugin_basename = $installer->plugin_info();
            echo json_encode( array( 'plugin' => $plugin_basename ) );
            die;
        }
    }

    // Send back a response.
    echo json_encode( true );
    die;

}

add_action( 'wp_ajax_soliloquy_activate_addon', 'soliloquy_ajax_activate_addon' );
/**
 * Activates an Soliloquy addon.
 *
 * @since 1.0.0
 */
function soliloquy_ajax_activate_addon() {

    // Run a security check first.
    check_ajax_referer( 'soliloquy-activate', 'nonce' );

    // Activate the addon.
    if ( isset( $_POST['plugin'] ) ) {
        $activate = activate_plugin( $_POST['plugin'] );

        if ( is_wp_error( $activate ) ) {
            echo json_encode( array( 'error' => $activate->get_error_message() ) );
            die;
        }
    }

    echo json_encode( true );
    die;

}

add_action( 'wp_ajax_soliloquy_deactivate_addon', 'soliloquy_ajax_deactivate_addon' );
/**
 * Deactivates an Soliloquy addon.
 *
 * @since 1.0.0
 */
function soliloquy_ajax_deactivate_addon() {

    // Run a security check first.
    check_ajax_referer( 'soliloquy-deactivate', 'nonce' );

    // Deactivate the addon.
    if ( isset( $_POST['plugin'] ) ) {
        $deactivate = deactivate_plugins( $_POST['plugin'] );
    }

    echo json_encode( true );
    die;

}

/**
 * Helper function to prepare the metadata for an image in a slider.
 *
 * @since 1.0.0
 *
 * @param array $slider_data  Array of data for the slider.
 * @param int $id             The attachment ID to prepare data for.
 * @param string $type        The type of slide to prepare (defaults to image).
 * @param array $data         Data to be used for the slide.
 * @return array $slider_data Amended slider data with updated image metadata.
 */
function soliloquy_ajax_prepare_slider_data( $slider_data, $id, $type = 'image', $data = array() ) {

    switch ( $type ) {
        case 'image' :
            $attachment = get_post( $id );
            $url        = wp_get_attachment_image_src( $id, 'full' );
            $alt_text   = get_post_meta( $id, '_wp_attachment_image_alt', true );
            $slider_data['slider'][$id] = array(
                'status'  => 'pending',
                'src'     => isset( $url[0] ) ? esc_url( $url[0] ) : '',
                'title'   => get_the_title( $id ),
                'link'    => '',
                'alt'     => ! empty( $alt_text ) ? $alt_text : get_the_title( $id ),
                'caption' => ! empty( $attachment->post_excerpt ) ? $attachment->post_excerpt : '',
                'type'    => $type
            );
            break;
        case 'video' :
            $slider_data['slider'][$id] = array(
                'status'  => 'pending',
                'src'     => esc_url( $data['thumb'] ),
                'title'   => esc_html( $data['title'] ),
                'url'     => esc_url( $data['url'] ),
                'thumb'   => esc_url( $data['thumb'] ),
                'caption' => trim( $data['caption'] ),
                'type'    => $type
            );
            break;
        case 'html' :
            $slider_data['slider'][$id] = array(
                'status' => 'pending',
                'src'    => esc_url( $data['thumb'] ),
                'title'  => esc_html( $data['title'] ),
                'code'   => trim( $data['code'] ),
                'type'   => $type
            );
            break;
    }

    $slider_data = apply_filters( 'soliloquy_ajax_item_data', $slider_data, $id, $type );

    return $slider_data;

}