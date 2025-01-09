<?php
/**
 * Plugin Name: Listing Template Plugin
 * Description: Custom template for the Listings Custom Post Type.
 * Version: 1.0
 * Author: IWD
 */

// Hook to load custom template for 'listings' CPT
function custom_listing_template($template) {
    if (is_singular('listings')) {
        // Path to the custom template
        $plugin_template = plugin_dir_path(__FILE__) . 'templates/listing-template.php';
        
        // Check if the template file exists
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }
    
    return $template;
}
add_filter('template_include', 'custom_listing_template');

// You can optionally enqueue scripts and styles here
// e.g., wp_enqueue_style('listing-template-style', plugin_dir_url(__FILE__) . 'styles.css');


function enable_classic_editor() {
    add_filter('use_block_editor_for_post', '__return_false', 10);
    add_filter('use_block_editor_for_post_type', '__return_false', 10);
}
add_action('init', 'enable_classic_editor');

// Optionally remove the Gutenberg styles
function dequeue_gutenberg_styles() {
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('wc-block-style'); // WooCommerce block styles
}
add_action('wp_enqueue_scripts', 'dequeue_gutenberg_styles', 100);

function hide_editor_for_listings($post_type, $post) {
    // Check if the post type is 'listings'
    if ($post_type === 'listings') {
        // Remove the post editor
        remove_post_type_support('listings', 'editor');
    }
}
add_action('add_meta_boxes', 'hide_editor_for_listings', 10, 2);



// Register Custom Post Type
function create_listings_post_type() {
    $labels = array(
        'name'                  => _x('Listings', 'Post type general name', 'textdomain'),
        'singular_name'         => _x('Listing', 'Post type singular name', 'textdomain'),
        'menu_name'             => _x('Listings', 'Admin Menu text', 'textdomain'),
        'name_admin_bar'        => _x('Listing', 'Add New on Toolbar', 'textdomain'),
        'add_new'               => __('Add New', 'textdomain'),
        'add_new_item'          => __('Add New Listing', 'textdomain'),
        'new_item'              => __('New Listing', 'textdomain'),
        'edit_item'             => __('Edit Listing', 'textdomain'),
        'view_item'             => __('View Listing', 'textdomain'),
        'all_items'             => __('All Listings', 'textdomain'),
        'search_items'          => __('Search Listings', 'textdomain'),
        'not_found'             => __('No listings found.', 'textdomain'),
        'not_found_in_trash'    => __('No listings found in Trash.', 'textdomain'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'show_in_rest'       => true,
        'supports'           => array('title', 'editor', 'thumbnail'),
        'menu_icon'          => 'dashicons-store',
        'rewrite'            => array('slug' => '', 'with_front' => false),
    );

    register_post_type('listings', $args);
}
add_action('init', 'create_listings_post_type');

// Adjust Rewrite Rules
function listings_custom_rewrite_rule() {
    add_rewrite_rule(
        '^listings/([^/]+)?$', // Match listings specifically under 'listings/'
        'index.php?listings=$matches[1]', // Redirect to the custom post type
        'top'
    );
}
add_action('init', 'listings_custom_rewrite_rule', 10, 0);

// Add a Precedence Check
function listings_rewrite_conflict_fix($query) {
    // Check if the query is for a single page
    if (isset($query->query_vars['pagename']) && !isset($query->query_vars['post_type'])) {
        $pagename = $query->query_vars['pagename'];
        
        // Check if a page exists with the given pagename
        $page = get_page_by_path($pagename);

        // If no page is found, set post type to listings
        if (!$page) {
            $query->set('post_type', 'listings');
        }
    }
}
add_action('pre_get_posts', 'listings_rewrite_conflict_fix');




// Ensure Correct Query Vars for Listings Post Type
function listings_post_type_query_vars($query) {
   if (
    !is_admin() &&
    $query->is_main_query() &&
    empty($query->query_vars['name']) &&
    isset($query->query_vars['post_type']) && // Check if 'post_type' exists
    $query->query_vars['post_type'] === 'listings'
) {
        $query->set('post_type', array('listings'));
    }
}
add_action('pre_get_posts', 'listings_post_type_query_vars');


// Add Meta Box
function add_listings_meta_boxes() {
    add_meta_box(
        'listings_meta_box', 
        'Listing Details', 
        'render_listings_meta_box', 
        'listings', 
        'normal', 
        'default'
    );
}
add_action('add_meta_boxes', 'add_listings_meta_boxes');

// Render Meta Box
function render_listings_meta_box($post) {
    wp_nonce_field('save_listings_meta', 'listings_meta_nonce');

    // Retrieve existing values
    $contact_info = get_post_meta($post->ID, 'contact_info', true) ?: array();
    $description = get_post_meta($post->ID, 'description', true);
    $meta_title = get_post_meta($post->ID, 'meta_title', true);
    $meta_des = get_post_meta($post->ID, 'meta_des', true);
    $logo = get_post_meta($post->ID, 'logo', true);
    $slideshow_pics = get_post_meta($post->ID, 'slideshow_pics', true) ?: array();
    $gallery_pics = get_post_meta($post->ID, 'gallery_pics', true) ?: array();

    ?>
    <h4>Contact Information:</h4>
    <label>Phone:</label>
    <input type="text" name="contact_info[phone]" value="<?php echo esc_attr($contact_info['phone'] ?? ''); ?>" style="width: 100%;">
    <label>Address:</label>
    <input type="text" name="contact_info[address]" value="<?php echo esc_attr($contact_info['address'] ?? ''); ?>" style="width: 100%;">
    <label>Email:</label>
    <input type="email" name="contact_info[email]" value="<?php echo esc_attr($contact_info['email'] ?? ''); ?>" style="width: 100%;">
    

    <h4>Content Information:</h4>
    <textarea name="description" style="width: 100%; height: 100px;"><?php echo esc_textarea($description); ?></textarea>
<h3>Meta Tags</h3>
 <h4>Title:</h4>
    <input type="text" name="meta_title" value="<?php echo esc_attr($meta_title); ?>" style="width: 100%;">
    
    <h4>Description:</h4>
    <input type="text" name="meta_des" value="<?php echo esc_attr($meta_des); ?>" style="width: 100%;">

    <h4>Logo:</h4>
<?php 
// Retrieve the logo URL from the attachment ID
$logo_url = $logo ? wp_get_attachment_url($logo) : ''; 
?>
<?php if ($logo_url): ?>
    <div id="logo-preview" style="margin-bottom: 10px;">
        <img src="<?php echo esc_url($logo_url); ?>" alt="Logo" style="max-width: 150px;">
    </div>
<?php else: ?>
    <p>No logo set.</p>
<?php endif; ?>
<input type="hidden" name="logo" id="logo" value="<?php echo esc_attr($logo); ?>">
<button type="button" class="button upload-logo-button">Upload/Change Logo</button>

    <h4>Slideshow Pictures:</h4>
    <div id="slideshow-preview" style="margin-bottom: 10px;">
        <?php if (!empty($slideshow_pics)): ?>
            <?php foreach ($slideshow_pics as $pic): ?>
                <img src="<?php echo wp_get_attachment_url($pic); ?>" alt="Slideshow Image" style="max-width: 150px; margin-right: 10px;">
            <?php endforeach; ?>
        <?php else: ?>
            <p>No slideshow pictures added.</p>
        <?php endif; ?>
    </div>
    <input type="hidden" name="slideshow_pics" id="slideshow_pics" value="<?php echo esc_attr(json_encode($slideshow_pics)); ?>">
    <button type="button" class="button upload-slideshow-button">Upload/Change Slideshow</button>

    <h4>Gallery Pictures:</h4>
    <div id="gallery-preview" style="margin-bottom: 10px;">
        <?php if (!empty($gallery_pics)): ?>
            <?php foreach ($gallery_pics as $pic): ?>
                <img src="<?php echo wp_get_attachment_url($pic); ?>" alt="Gallery Image" style="max-width: 150px; margin-right: 10px;">
            <?php endforeach; ?>
        <?php else: ?>
            <p>No gallery pictures added.</p>
        <?php endif; ?>
    </div>
    <input type="hidden" name="gallery_pics" id="gallery_pics" value="<?php echo esc_attr(json_encode($gallery_pics)); ?>">
    <button type="button" class="button upload-gallery-button">Upload/Change Gallery</button>

    <script>
        jQuery(document).ready(function ($) {
            var mediaUploader;

            // Logo Upload
            $('.upload-logo-button').on('click', function (e) {
                e.preventDefault();
                mediaUploader = wp.media({
                    title: 'Select Logo',
                    button: { text: 'Use Logo' },
                    multiple: false
                });
                mediaUploader.on('select', function () {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#logo').val(attachment.id);
                    $('#logo-preview').html('<img src="' + attachment.url + '" alt="Logo" style="max-width: 150px;">');
                });
                mediaUploader.open();
            });

            // Slideshow Upload
            $('.upload-slideshow-button').on('click', function (e) {
                e.preventDefault();
                mediaUploader = wp.media({
                    title: 'Select Slideshow Images',
                    button: { text: 'Add to Slideshow' },
                    multiple: true
                });
                mediaUploader.on('select', function () {
                    var attachments = mediaUploader.state().get('selection').toJSON();
                    var urls = [];
                    $('#slideshow-preview').empty();
                    attachments.forEach(function (attachment) {
                        urls.push(attachment.id);
                        $('#slideshow-preview').append('<img src="' + attachment.url + '" alt="Slideshow Image" style="max-width: 150px; margin-right: 10px;">');
                    });
                    $('#slideshow_pics').val(JSON.stringify(urls));
                });
                mediaUploader.open();
            });

      // Gallery Upload
$('.upload-gallery-button').on('click', function (e) {
    e.preventDefault();
    mediaUploader = wp.media({
        title: 'Select Gallery Images',
        button: { text: 'Add to Gallery' },
        multiple: true
    });
    mediaUploader.on('select', function () {
        var attachments = mediaUploader.state().get('selection').toJSON();
        var currentImages = $('#gallery-preview img').length;
        var totalImages = currentImages + attachments.length;

        // Check if the total images exceed the limit of 15
        if (totalImages > 15) {
            alert('You can only upload a maximum of 15 gallery images.');
            return; // Stop the process if the limit is exceeded
        }

        var urls = [];
        $('#gallery-preview').empty();
        attachments.forEach(function (attachment) {
            urls.push(attachment.id);
            $('#gallery-preview').append('<img src="' + attachment.url + '" alt="Gallery Image" style="max-width: 150px; margin-right: 10px;">');
        });
        $('#gallery_pics').val(JSON.stringify(urls));
    });
    mediaUploader.open();
});

        });
    </script>
    <?php
}


// Save Meta Box Data
function save_listings_meta($post_id) {
    if (!isset($_POST['listings_meta_nonce']) || !wp_verify_nonce($_POST['listings_meta_nonce'], 'save_listings_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save custom fields
    update_post_meta($post_id, 'contact_info', $_POST['contact_info']);
    update_post_meta($post_id, 'description', sanitize_textarea_field($_POST['description']));
    update_post_meta($post_id, 'meta_title', sanitize_text_field($_POST['meta_title']));
    update_post_meta($post_id, 'meta_des', sanitize_text_field($_POST['meta_des']));
    update_post_meta($post_id, 'logo', sanitize_text_field($_POST['logo']));
    update_post_meta($post_id, 'slideshow_pics', json_decode(stripslashes($_POST['slideshow_pics']), true));
    //update_post_meta($post_id, 'gallery_pics', json_decode(stripslashes($_POST['gallery_pics']), true));
    
      // Save gallery pictures
    if (isset($_POST['gallery_pics'])) {
        $gallery_pics = json_decode(stripslashes($_POST['gallery_pics']), true);

        // Limit gallery pictures to 15
        if (count($gallery_pics) > 15) {
            $gallery_pics = array_slice($gallery_pics, 0, 15); // Truncate array to 15 items
        }

        update_post_meta($post_id, 'gallery_pics', $gallery_pics);
    }
}
add_action('save_post', 'save_listings_meta');



// Add this shortcode to render the form
function listings_frontend_form() {
    
    
    // Check if the form was successfully submitted
    if (isset($_GET['submitted']) && $_GET['submitted'] === 'true') {
        return '<p class="successmessage">Your listing is submitted and waiting for admin approval.</p>';
    }

    // Display the form if it hasn't been submitted
    ob_start(); ?>
    <script>
document.addEventListener('DOMContentLoaded', function () {
    // Form submission
    const form = document.querySelector('.listingform form');

    // Gallery file input
    const galleryInput = document.getElementById('gallery_pics');

    form.addEventListener('submit', function (e) {
        // Check gallery pictures limit
        const galleryFiles = galleryInput.files;

        if (galleryFiles.length > 15) {
            e.preventDefault(); // Prevent form submission
            alert('You can only upload a maximum of 15 gallery images.');
        }
    });
});
</script>
<div class="listingform">
    <style>
     p.successmessage {
    padding: 15px;
    text-align: center;
    border: 4px solid green;
    font-size: 18px;
}
.listingform {
    padding: 35px;
    width: 100%;
    box-shadow: 1px 1px 7px 1px gray;
    border-radius: 10px;
}
.listingform input,.listingform textarea {
    width: 90%;
    padding: 15px;
    box-shadow: 1px 1px 4px 0px gray;
    border: none;
    border-radius: 6px;
}
input[type="submit"] {
    background: #0d580d;
    max-width: 200px;
    margin: 0 auto;
    display: block;
    color: white;
    font-size: 18px;
    cursor:pointer;
}

.listingform h3 {
    text-align: center;
    font-weight: 600;
}
    </style>
    <h3>Add Your Listing</h3>
   <form method="post" action="" enctype="multipart/form-data">
    <label for="title">Title:</label><br>
    <input type="text" id="title" name="title" required><br><br>

    <label for="phone">Phone:</label><br>
    <input type="text" id="phone" name="phone" required><br><br>
    
    <label for="address">Address:</label><br>
    <input type="text" id="address" name="address" required><br><br>
    
    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email" required><br><br>

    
    <label for="description">Content Information:</label><br>
    <textarea id="description" name="description" rows="5" required></textarea>
    <h4>Meta Tags</h4>
   <label for="meta_title">Title:</label><br>
    <input type="text" id="meta_title" name="meta_title"><br><br>

      <label for="meta_title">Description:</label><br>
    <input type="text" id="meta_des" name="meta_des"><br><br>
    
    <label for="logo">Logo:</label><br>
    <input type="file" id="logo" name="logo" accept="image/*"><br><br>
    
	   <label for="slideshow_pics">Slideshow Pictures (up to 4):</label><br>
    <input type="file" id="slideshow_pics" name="slideshow_pics[]" accept="image/*" multiple><br><br>
	   
	   <label for="gallery_pics">Gallery Pictures (up to 16):</label><br>
    <input type="file" id="gallery_pics" name="gallery_pics[]" accept="image/*" multiple><br><br>
	   
    <input type="hidden" name="action" value="submit_listing">
    <?php wp_nonce_field('submit_listing_action', 'submit_listing_nonce'); ?>
    
    <input type="submit" value="Submit">
</form>

</div>


    <?php return ob_get_clean();
}
add_shortcode('listings_form', 'listings_frontend_form');


function handle_listings_form_submission() {
    if (
        isset($_POST['action']) &&
        $_POST['action'] === 'submit_listing' &&
        isset($_POST['submit_listing_nonce']) &&
        wp_verify_nonce($_POST['submit_listing_nonce'], 'submit_listing_action')
    ) {
        // Sanitize and capture form data
        $title = sanitize_text_field($_POST['title']);
        $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
        $address = isset($_POST['address']) ? sanitize_text_field($_POST['address']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';
        $meta_title = isset($_POST['meta_title']) ? sanitize_text_field($_POST['meta_title']) : '';
        $meta_des = isset($_POST['meta_des']) ? sanitize_text_field($_POST['meta_des']) : '';

        // Insert the post into the database
        $post_id = wp_insert_post([
            'post_title'   => $title,
            'post_content' => $description,
            'post_status'  => 'pending', // Set status to pending
            'post_type'    => 'listings', // Your custom post type
        ]);

        if ($post_id) {
            // Save contact info as a single meta field
            $contact_info = [
                'phone'         => $phone,
                'address'       => $address,
                'email'         => $email,
            ];
            update_post_meta($post_id, 'contact_info', $contact_info);

            // Save other meta fields
            update_post_meta($post_id, 'description', $description);
            update_post_meta($post_id, 'meta_title', $meta_title);
            update_post_meta($post_id, 'meta_des', $meta_des);

            // Handle logo upload (same as before)
            if (!empty($_FILES['logo']['name'])) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
                require_once ABSPATH . 'wp-admin/includes/image.php';

                $uploaded_file = $_FILES['logo'];
                $upload = wp_handle_upload($uploaded_file, ['test_form' => false]);

                if (!isset($upload['error']) && isset($upload['file'])) {
                    $attachment_id = wp_insert_attachment([
                        'post_mime_type' => $upload['type'],
                        'post_title'     => sanitize_file_name($uploaded_file['name']),
                        'post_content'   => '',
                        'post_status'    => 'inherit'
                    ], $upload['file'], $post_id);

                    if (!is_wp_error($attachment_id)) {
                        $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
                        wp_update_attachment_metadata($attachment_id, $attachment_data);

                        update_post_meta($post_id, 'logo', $attachment_id);
                    }
                }
            }

            // Handle multiple slideshow_pics uploads (same as before)
            if (!empty($_FILES['slideshow_pics']['name'][0])) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
                require_once ABSPATH . 'wp-admin/includes/image.php';

                $slideshow_pics_ids = [];
                foreach ($_FILES['slideshow_pics']['name'] as $key => $value) {
                    if (!empty($value)) {
                        $file = [
                            'name'     => $_FILES['slideshow_pics']['name'][$key],
                            'type'     => $_FILES['slideshow_pics']['type'][$key],
                            'tmp_name' => $_FILES['slideshow_pics']['tmp_name'][$key],
                            'error'    => $_FILES['slideshow_pics']['error'][$key],
                            'size'     => $_FILES['slideshow_pics']['size'][$key],
                        ];
                        $upload = wp_handle_upload($file, ['test_form' => false]);

                        if (!isset($upload['error']) && isset($upload['file'])) {
                            $attachment_id = wp_insert_attachment([ 
                                'post_mime_type' => $upload['type'],
                                'post_title'     => sanitize_file_name($file['name']),
                                'post_content'   => '',
                                'post_status'    => 'inherit',
                            ], $upload['file'], $post_id);

                            if (!is_wp_error($attachment_id)) {
                                $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
                                wp_update_attachment_metadata($attachment_id, $attachment_data);

                                $slideshow_pics_ids[] = $attachment_id;
                            }
                        }
                    }
                }

                // Save the slideshow_pics IDs as a meta field
                if (!empty($slideshow_pics_ids)) {
                    update_post_meta($post_id, 'slideshow_pics', $slideshow_pics_ids);
                }
            }

            // Handle multiple gallery_pics uploads, with limit of 15 images
            if (!empty($_FILES['gallery_pics']['name'][0])) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
                require_once ABSPATH . 'wp-admin/includes/image.php';

                $gallery_pics_ids = [];
                $gallery_files_count = count($_FILES['gallery_pics']['name']);
                
                // Limit the gallery images to 15
                if ($gallery_files_count > 15) {
                    // Optionally, you could display an error here if you want
                    $gallery_files_count = 15; // Truncate to 15
                }

                // Process up to 15 files
                for ($key = 0; $key < $gallery_files_count; $key++) {
                    $file = [
                        'name'     => $_FILES['gallery_pics']['name'][$key],
                        'type'     => $_FILES['gallery_pics']['type'][$key],
                        'tmp_name' => $_FILES['gallery_pics']['tmp_name'][$key],
                        'error'    => $_FILES['gallery_pics']['error'][$key],
                        'size'     => $_FILES['gallery_pics']['size'][$key],
                    ];

                    $upload = wp_handle_upload($file, ['test_form' => false]);

                    if (!isset($upload['error']) && isset($upload['file'])) {
                        $attachment_id = wp_insert_attachment([ 
                            'post_mime_type' => $upload['type'],
                            'post_title'     => sanitize_file_name($file['name']),
                            'post_content'   => '',
                            'post_status'    => 'inherit',
                        ], $upload['file'], $post_id);

                        if (!is_wp_error($attachment_id)) {
                            $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
                            wp_update_attachment_metadata($attachment_id, $attachment_data);

                            $gallery_pics_ids[] = $attachment_id;
                        }
                    }
                }

                // Save the gallery_pics IDs as a meta field
                if (!empty($gallery_pics_ids)) {
                    update_post_meta($post_id, 'gallery_pics', $gallery_pics_ids);
                }
            }
        }

        // Redirect or display a success message
        wp_redirect(add_query_arg('submitted', 'true', get_permalink()));
        exit;
    }
}
add_action('init', 'handle_listings_form_submission');


//delete media when post deleted
function delete_associated_media_on_post_delete($post_id) {
    // Check if the post is of the 'listings' post type
    if (get_post_type($post_id) !== 'listings') {
        return;
    }

    // Delete logo image
    $logo_attachment_id = get_post_meta($post_id, 'logo', true);
    if ($logo_attachment_id) {
        wp_delete_attachment($logo_attachment_id, true);  // The 'true' flag ensures the file is deleted from the server
    }

    // Delete slideshow images
    $slideshow_pics = get_post_meta($post_id, 'slideshow_pics', true);
    if ($slideshow_pics) {
        foreach ($slideshow_pics as $pic_id) {
            wp_delete_attachment($pic_id, true);  // Delete the image
        }
    }

    // Delete gallery images
    $gallery_pics = get_post_meta($post_id, 'gallery_pics', true);
    if ($gallery_pics) {
        foreach ($gallery_pics as $pic_id) {
            wp_delete_attachment($pic_id, true);  // Delete the image
        }
    }
}

add_action('before_delete_post', 'delete_associated_media_on_post_delete');