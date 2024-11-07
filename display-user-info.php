<?php
/*
Plugin Name: Display User Info
Plugin URI: https://yourwebsite.com/
Description: A plugin that displays and allows editing of user information on the frontend, including a profile picture, first-aid course checkbox, company, and motorcycle fields.
Version: 1.4
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
            <th><label for="phone_number">Phone Number</label></th>
            <td>
                <input type="text" name="phone_number" id="phone_number" value="<?php echo esc_attr(get_user_meta($user->ID, 'phone_number', true)); ?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th><label for="department">Department</label></th>
            <td>
                  <select id="department-filter">
        <option value="MC Executors - Uusimaa">MC Executors - Uusimaa</option>
        <option value="MC Executors - Pohjanmaa">MC Executors - Pohjanmaa</option>
    </select>
            </td>
        </tr>
        <tr>
            <th><label for="company">Company</label></th>
            <td>
                <input type="text" name="company" id="company" value="<?php echo esc_attr(get_user_meta($user->ID, 'company', true)); ?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th><label for="motorcycle">Motorcycle</label></th>
            <td>
                <input type="text" name="motorcycle" id="motorcycle" value="<?php echo esc_attr(get_user_meta($user->ID, 'motorcycle', true)); ?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th><label for="first_aid">First Aid Course Completed</label></th>
            <td>
                <input type="checkbox" name="first_aid" id="first_aid" value="yes" <?php checked(get_user_meta($user->ID, 'first_aid', true), 'yes'); ?>>
                <label for="first_aid">Yes, I have completed a first aid course</label>
            </td>
        </tr>
        <tr>
            <th><label for="tilanne_koulutus">Tilannekoulutus Course Completed</label></th>
            <td>
                <input type="checkbox" name="tilanne_koulutus" id="tilanne_koulutus" value="yes" d <?php checked(get_user_meta($user->ID, 'tilanne_koulutus', true), 'yes'); ?>>
                <label for="tilanne_koulutus">Yes, I have completed a tilannekoulutus</label>
            </td>
        </tr>
        <tr>
            <th><label for="biographical_info">Biographical Info</label></th>
            <td>
                <textarea name="biographical_info" id="biographical_info" rows="5" class="regular-text"><?php echo esc_textarea(get_user_meta($user->ID, 'biographical_info', true)); ?></textarea>
            </td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'custom_user_profile_fields');
add_action('edit_user_profile', 'custom_user_profile_fields');

// Step 2: Save custom profile fields

function save_custom_user_profile_fields($user_id) {
    if (current_user_can('edit_user', $user_id)) {
        if (!empty($_FILES['profile_picture']['name'])) {
            $file = $_FILES['profile_picture'];
            if ($file['error'] === UPLOAD_ERR_OK) {
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                require_once(ABSPATH . 'wp-admin/includes/file.php');
                require_once(ABSPATH . 'wp-admin/includes/media.php');
                $attachment_id = media_handle_upload('profile_picture', 0);
                if (!is_wp_error($attachment_id)) {
                    update_user_meta($user_id, 'profile_picture', esc_url(wp_get_attachment_url($attachment_id)));
                }
            }
        }

        // Save additional custom fields
        $fields = ['phone_number', 'department', 'company', 'motorcycle', 'biographical_info'];
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_user_meta($user_id, $field, sanitize_text_field($_POST[$field]));
            }
        }
        update_user_meta($user_id, 'tilanne_koulutus', isset($_POST['tilanne_koulutus']) ? 'yes' : 'no');
        update_user_meta($user_id, 'first_aid', isset($_POST['first_aid']) ? 'yes' : 'no');
        update_user_meta($user_id, 'show_email', isset($_POST['show_email']) ? 'yes' : 'no');
        update_user_meta($user_id, 'show_phone_number', isset($_POST['show_phone_number']) ? 'yes' : 'no');
    }
}
add_action('personal_options_update', 'save_custom_user_profile_fields');
add_action('edit_user_profile_update', 'save_custom_user_profile_fields');

// Step 3: Shortcode to Display and Edit User Info
function display_user_info_shortcode() {
    $current_user = wp_get_current_user();
    if (!is_user_logged_in()) {
        return '<p>You need to be logged in to see your profile information.</p>';
    }

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
    $company = get_user_meta($current_user->ID, 'company', true);
    $motorcycle = get_user_meta($current_user->ID, 'motorcycle', true);
    $first_aid = get_user_meta($current_user->ID, 'first_aid', true);
    $tilanne_koulutus = get_user_meta($current_user->ID, 'tilanne_koulutus', true);
    $biographical_info=get_user_meta($current_user->ID,'description',true);


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
                <p><label for="profile_picture">Change Profile Picture:</label></p>
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
            </div>
            <div class="user-details">
                <p><strong>Username:</strong> <?php echo esc_html($current_user->user_login); ?></p>
                <p><strong>First Name:</strong> <input type="text" name="first_name" value="<?php echo esc_attr($current_user->first_name); ?>" class="regular-text"></p>
                <p><strong>Last Name:</strong> <input type="text" name="last_name" value="<?php echo esc_attr($current_user->last_name); ?>" class="regular-text"></p>
                <p><strong>Email:</strong> <?php echo esc_html($current_user->user_email); ?></p>
                <p><strong>Phone Number:</strong> <input type="text" name="phone_number" value="<?php echo esc_attr($phone_number); ?>" class="regular-text"></p>
                <p>
    <strong>Department:</strong> 
    <select name="department" id="department-filter">
        <option value="MC Executors - Uusimaa" <?php selected($department, 'MC Executors - Uusimaa'); ?>>MC Executors - Uusimaa</option>
        <option value="MC Executors - Pohjanmaa" <?php selected($department, 'MC Executors - Pohjanmaa'); ?>>MC Executors - Pohjanmaa</option>
    </select>
</p>
                <p><strong>Company:</strong> <input type="text" name="company" value="<?php echo esc_attr($company); ?>" class="regular-text"></p>
                <p><strong>Motorcycle:</strong> <input type="text" name="motorcycle" value="<?php echo esc_attr($motorcycle); ?>" class="regular-text"></p>
                <p><strong>First Aid Course:</strong>
                    <input type="checkbox" name="first_aid" value="yes" <?php checked($first_aid, 'yes'); ?> 
                    <?php if (!current_user_can('manage_options')) echo 'disabled'; ?>> Yes, I have completed a first aid course
                </p>
                <p><strong>Tilanne Koulutus:</strong>
                    <input type="checkbox" name="tilanne_koulutus" value="yes" <?php checked($tilanne_koulutus, 'yes'); ?> 
                    <?php if (!current_user_can('manage_options')) echo 'disabled'; ?>> Yes, I have completed a tilanne koulutus
                </p>
                <div class="biography">
                        <label for="biographical_info">Biographical Info:</label>
                        <textarea id="biographical_info" name="biographical_info" ><?php echo esc_textarea($biographical_info); ?></textarea>   

                <!-- Other form fields as before -->

                <div class="visibility-options">
                    <h4>Profile Visibility Options</h4>
                    <label><input type="checkbox" name="show_email" value="yes" <?php checked($show_email, 'yes'); ?>> Allow others to see my email</label>
                    <label><input type="checkbox" name="show_phone_number" value="yes" <?php checked($show_phone_number, 'yes'); ?>> Allow others to see my phone number</label>
                </div>
 <!-- Reset Password Button -->
 <button type="submit" name="reset_password" class="reset-password-button">Reset Password</button>

<!-- Update Profile Button -->
<button type="submit" name="update_profile" class="update-button">Update Profile</button>
</div>
</div>
</form>

<!-- Handle Password Reset Shortcode Output -->
 <!-- Handle Password Reset Shortcode Output -->


<?php echo do_shortcode('[handle_password_reset]'); ?>
    <?php
    return ob_get_clean();
    
}
add_shortcode('display_user_info', 'display_user_info_shortcode');
// Hide unnecessary fields on the admin profile page
function hide_unnecessary_profile_fields() {
    echo '
    <style>
        .user-rich-editing-wrap,
        .user-admin-color-wrap,
        .user-comment-shortcuts-wrap,
        .user-admin-bar-front-wrap,
        .user-url-wrap,
        .user-aim-wrap,
        .user-jabber-wrap,
        .user-yim-wrap,
        .user-nickname-wrap,
        .user-display-name-wrap,
        .user-profile-picture {
            display: none !important;
        }
    </style>';
}
add_action('admin_head', 'hide_unnecessary_profile_fields');

// Step 4: Handle Password Reset Requests

function handle_password_reset_request() {
    if (isset($_POST['reset_password'])) {
        $current_user = wp_get_current_user();
        $user_login = $current_user->user_login;
        $user_data = get_user_by('login', $user_login);

        if (!$user_data) {
            return '<p>Error: User not found.</p>';
        }

        // Generate password reset link
        $reset_key = get_password_reset_key($user_data);
        if (is_wp_error($reset_key)) {
            return '<p>Error: Unable to generate password reset link.</p>';
        }

        // Construct the reset link
        $reset_link = network_site_url("wp-login.php?action=rp&key=$reset_key&login=" . rawurlencode($user_data->user_login), 'login');

        // Send password reset email
        $message = "Hi " . $user_data->user_login . ",\n\n";
        $message .= "You requested a password reset. To reset your password, click the link below:\n\n";
        $message .= "<a href='$reset_link'>$reset_link</a>\n\n";
        $message .= "If you did not request a password reset, please ignore this email.";

        // Use wp_mail to send email
        wp_mail($user_data->user_email, 'Password Reset Request', $message);

        // Confirmation message
        return '<p>A password reset link has been sent to your email address.</p>';
    }
}
add_shortcode('handle_password_reset', 'handle_password_reset_request');