<?php
/*
Plugin Name: Display User Info
Plugin URI: https://yourwebsite.com/
Description: A plugin that displays and allows editing of user information on the frontend, including a profile picture and visibility options.
Version: 1.3
Author: Your Name
Author URI: https://yourwebsite.com/
License: GPL2
*/

// Step 1: Add custom fields to the user profile
function custom_user_profile_fields($user) {
    ?>
    <h3>Profile Information</h3>
    <table class="form-table">
        <tr>
            <th><label for="profile_picture">Upload Profile Picture</label></th>
            <td>
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
                <?php if ($profile_picture = get_user_meta($user->ID, 'profile_picture', true)) : ?>
                    <img src="<?php echo esc_url($profile_picture); ?>" width="100" style="display: block; margin-top: 10px;">
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th><label for="phone_number">Phone Number</label></th>
            <td>
                <input type="text" name="phone_number" id="phone_number" value="<?php echo esc_attr(get_user_meta($user->ID, 'phone_number', true)); ?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th><label for="department">Department</label></th>
            <td>
                <input type="text" name="department" id="department" value="<?php echo esc_attr(get_user_meta($user->ID, 'department', true)); ?>" class="regular-text">
            </td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'custom_user_profile_fields');
add_action('edit_user_profile', 'custom_user_profile_fields');

// Step 1.1: Add visibility options to the user profile
function add_visibility_options_to_user_profile($user) {
    ?>
    <h3>Profile Visibility Options</h3>
    <table class="form-table">
        <tr>
            <th><label for="show_email">Show Email to Others</label></th>
            <td>
                <input type="checkbox" name="show_email" id="show_email" value="yes" <?php checked(get_user_meta($user->ID, 'show_email', true), 'yes'); ?>>
                <label for="show_email">Allow others to see my email</label>
            </td>
        </tr>
        <tr>
            <th><label for="show_phone_number">Show Phone Number to Others</label></th>
            <td>
                <input type="checkbox" name="show_phone_number" id="show_phone_number" value="yes" <?php checked(get_user_meta($user->ID, 'show_phone_number', true), 'yes'); ?>>
                <label for="show_phone_number">Allow others to see my phone number</label>
            </td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'add_visibility_options_to_user_profile');
add_action('edit_user_profile', 'add_visibility_options_to_user_profile');

// Step 2: Save custom profile fields
// Step 2: Save custom profile fields
function save_custom_user_profile_fields($user_id) {
    if (current_user_can('edit_user', $user_id)) {
        // Handle the profile picture upload
        if (!empty($_FILES['profile_picture']['name'])) {
            $file = $_FILES['profile_picture'];
            if ($file['error'] === UPLOAD_ERR_OK) {
                // Use WordPress's built-in uploader
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                require_once(ABSPATH . 'wp-admin/includes/file.php');
                require_once(ABSPATH . 'wp-admin/includes/media.php');

                // Upload the file
                $attachment_id = media_handle_upload('profile_picture', 0);
                if (is_wp_error($attachment_id)) {
                    // Handle the error if something goes wrong
                    error_log('Upload error: ' . $attachment_id->get_error_message());
                } else {
                    // Update user meta with the attachment URL
                    update_user_meta($user_id, 'profile_picture', esc_url(wp_get_attachment_url($attachment_id)));
                }
            }
        }

        // Save phone number and department
        if (isset($_POST['phone_number'])) {
            update_user_meta($user_id, 'phone_number', sanitize_text_field($_POST['phone_number']));
        }
        if (isset($_POST['department'])) {
            update_user_meta($user_id, 'department', sanitize_text_field($_POST['department']));
        }

        // Save first name, last name, and biographical info
        if (isset($_POST['first_name'])) {
            update_user_meta($user_id, 'first_name', sanitize_text_field($_POST['first_name']));
        }
        if (isset($_POST['last_name'])) {
            update_user_meta($user_id, 'last_name', sanitize_text_field($_POST['last_name']));
        }
        if (isset($_POST['biographical_info'])) {
            update_user_meta($user_id, 'description', sanitize_textarea_field($_POST['biographical_info']));
        }

        // Save visibility options
        update_user_meta($user_id, 'show_email', isset($_POST['show_email']) ? 'yes' : 'no');
        update_user_meta($user_id, 'show_phone_number', isset($_POST['show_phone_number']) ? 'yes' : 'no');
    }
}
// Step 3: Shortcode to Display and Edit User Info
// Updated display_user_info_shortcode function
function display_user_info_shortcode() {
    $current_user = wp_get_current_user();
    if (!is_user_logged_in()) {
        return '<p>You need to be logged in to see your profile information.</p>';
    }

    // Handle the profile update submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
        save_custom_user_profile_fields($current_user->ID);
    }

    $style = "
    <style>
        .user-info {
            display: flex;
            align-items: flex-start;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            background-color: #f9f9f9;
            max-width: 500px;
            margin: 20px auto;
            font-family: Arial, sans-serif;
            position: relative;
        }
        .user-avatar {
            margin-right: 20px;
            flex-shrink: 0;
        }
        .user-avatar img {
            border-radius: 50%;
            width: 100px;
            height: 100px;
            object-fit: cover;
        }
        .user-details h2 {
            font-size: 1.5em;
            margin-bottom: 20px;
            color: #333;
        }
        .user-details p {
            margin: 8px 0;
            color: #555;
        }
        .user-details p strong {
            color: #333;
        }
        .edit-profile-link {
            position: relative;
            right: 15px;
            left: 10px;
            background-color: #0073aa;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 10px;
        }
        .edit-profile-link:hover {
            background-color: #005177;
        }
        .biography {
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .biography textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #555;
        }
        .biography label {
            font-weight: bold;
        }
        .visibility-options {
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .visibility-options label {
            display: block;
            margin-bottom: 5px;
        }
        .update-button {
            background-color: #0073aa;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .update-button:hover {
            background-color: #005177;
        }
    </style>
    ";

    // Retrieve the profile picture, phone number, department, and biographical info
    $profile_picture = get_user_meta($current_user->ID, 'profile_picture', true) ?: get_avatar_url($current_user->ID, ['size' => 100]);
    $phone_number = get_user_meta($current_user->ID, 'phone_number', true);
    $department = get_user_meta($current_user->ID, 'department', true);
    $biographical_info = get_user_meta($current_user->ID, 'description', true);

    // Retrieve visibility options
    $show_email = get_user_meta($current_user->ID, 'show_email', true);
    $show_phone_number = get_user_meta($current_user->ID, 'show_phone_number', true);

    ob_start();
    echo $style;
    ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="user-info">
            <div class="user-avatar">
                <img src="<?php echo esc_url($profile_picture); ?>" alt="Profile Picture">
            </div>
            <div class="user-details">
                <p><strong>Username:</strong> <?php echo esc_html($current_user->user_login); ?></p>
                <p><strong>First Name:</strong> <input type="text" name="first_name" value="<?php echo esc_attr($current_user->first_name); ?>" class="regular-text"></p>
                <p><strong>Last Name:</strong> <input type="text" name="last_name" value="<?php echo esc_attr($current_user->last_name); ?>" class="regular-text"></p>
                <p><strong>Email:</strong> <?php echo esc_html($current_user->user_email); ?></p>
                <p><strong>Phone Number:</strong> <input type="text" name="phone_number" value="<?php echo esc_attr($phone_number); ?>" class="regular-text"></p>
                <p><strong>Department:</strong> <input type="text" name="department" value="<?php echo esc_attr($department); ?>" class="regular-text"></p>
                <div class="biography">
                    <label for="biographical_info">Biographical Info:</label>
                    <textarea id="biographical_info" name="biographical_info" maxlength="400"><?php echo esc_textarea($biographical_info); ?></textarea>
                    <p>Max 400 characters.</p>
                </div>

                
                <div class="visibility-options">
                    <h4>Profile Visibility Options</h4>
                    <label><input type="checkbox" name="show_email" value="yes" <?php checked($show_email, 'yes'); ?>> Allow others to see my email</label>
                    <label><input type="checkbox" name="show_phone_number" value="yes" <?php checked($show_phone_number, 'yes'); ?>> Allow others to see my phone number</label>
                </div>
                <button type="submit" name="update_profile" class="update-button">Update Profile</button>
            </div>
        </div>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('display_user_info', 'display_user_info_shortcode');