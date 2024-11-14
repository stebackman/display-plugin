<?php
/*
Plugin Name: Display User Info
Plugin URI: https://yourwebsite.com/
Description: A plugin that displays and allows editing of user information on the frontend, including a profile picture, first-aid course checkbox, company, and motorcycle fields.
Version: 1.4
Author: Group Molto Bene
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
        <?php if (current_user_can('administrator')) : ?>
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
            
            <th><label for="vip_member">Vuoden kunniajäsen</label></th>
            <td>
                <input type="checkbox" name="vip_member" id="vip_member" value="yes" d <?php checked(get_user_meta($user->ID, 'vip_member', true), 'yes'); ?>>
                <label for="vip_member">Tämä jäsen on viime vuoden kunniajäsen</label>
            </td>
        </tr>
        <?php endif; ?>
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
     // Check if current user is admin
     if (!current_user_can('administrator')) {
        return;
    }

    // Update 'vip_member' meta based on checkbox
    if (isset($_POST['vip_member'])) {
        update_user_meta($user_id, 'vip_member', 'yes');
    } else {
        update_user_meta($user_id, 'vip_member', 'no');
    }

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
        $fields = ['first_name','last_name','phone_number', 'department', 'company', 'motorcycle', 'vip_member','member_id', 'biographical_info'];
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
   /* Container for user info and profile details */
    .user-info {
        display: flex;
        flex-direction: column;
        gap: 15px;
        padding: 20px;
        max-width: 600px;
        margin: 20px auto;
        border: 1px solid #ddd;
        border-radius: 8px;
        background-color: #f3f3f3;
        font-family: Arial, sans-serif;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Avatar section */
    .user-avatar {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
    }
    .user-avatar img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 10px;
    }
    .vip-crown {
        position: absolute;
        top: -10px;
        right: -10px;
        font-size: 24px;
        color: gold;
    }

    /* User details and input fields */
    .user-details p {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #333;
        font-size: 0.95em;
        margin-bottom: 8px;
    }
    .user-details p strong {
        font-weight: bold;
        color: #555;
    }
    .user-details input[type='text'],
    .user-details select,
    .user-details textarea {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 0.95em;
        background-color: #fafafa;
        color: #333;
        box-sizing: border-box;
    }
    .user-details input:focus,
    .user-details select:focus,
    .user-details textarea:focus {
        outline: none;
        border-color: #0073aa;
        box-shadow: 0 0 5px rgba(0, 115, 170, 0.3);
    }

    /* Biography section */
    .biography {
        border-top: 1px solid #ddd;
        padding-top: 10px;
        margin-top: 15px;
    }
    .biography label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .biography textarea {
        resize: vertical;
        min-height: 80px;
    }

    /* Visibility options */
    .visibility-options {
        border-top: 1px solid #ddd;
        padding-top: 10px;
        margin-top: 15px;
    }
    .visibility-options label {
        display: block;
        margin: 5px 0;
        color: #444;
    }

    /* Action buttons */
    .update-button,
    .reset-password-button {
        display: inline-block;
        padding: 10px 20px;
        color: #fff;
        background-color: #0073aa;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        font-size: 1em;
        margin-top: 15px;
        width: calc(50% - 10px);
        box-sizing: border-box;
    }
    .reset-password-button {
        background-color: #d9534f;
    }
    .update-button:hover,
    .reset-password-button:hover {
        background-color: #005177;
    }
    .reset-password-button:hover {
        background-color: #b52b2b;
    }

    /* Link button */
    .view-profile-button a {
        display: inline-block;
        text-decoration: none;
        padding: 10px 15px;
        background-color: #6c757d;
        color: #fff;
        border-radius: 5px;
        margin-top: 10px;
        transition: background-color 0.3s ease;
    }
    .view-profile-button a:hover {
        background-color: #495057;
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
    $biographical_info=get_user_meta($current_user->ID,'biographical_info',true);
    $custom_user_id = get_user_meta($current_user->ID, 'custom_user_id', true);
    $vip_member= get_user_meta($current_user->ID,'vip_member',true)==='yes';


    // Retrieve visibility options
    $show_email = get_user_meta($current_user->ID, 'show_email', true);
    $show_phone_number = get_user_meta($current_user->ID, 'show_phone_number', true);

    ob_start();
    echo $style;
    ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="user-info">
        <p class="view-profile-button"><a href="<?php echo esc_url(get_permalink(get_page_by_path('kaikki-profiilit'))); ?>">Show all profiles</a></p>
            <div class="user-avatar">
                <img src="<?php echo esc_url($profile_picture); ?>" alt="Profile Picture">
                <p><label for="profile_picture">Change Profile Picture:</label></p>
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
                <?php if ($vip_member): ?>
                        <span class="vip-crown">&#x1F451;</span>
                        <?php endif; ?>
            </div>
            <div class="user-details">
                <p><strong>Username:</strong> <?php echo esc_html($current_user->user_login); ?></p>
                <p><strong>First Name:</strong> <input type="text" name="first_name" value="<?php echo esc_attr($current_user->first_name); ?>" class="regular-text"></p>
                <p><strong>Last Name:</strong> <input type="text" name="last_name" value="<?php echo esc_attr($current_user->last_name); ?>" class="regular-text"></p>
                <p><strong>Jäsennumero: <?php echo esc_attr(($custom_user_id)); ?></strong></p>
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
                <p><strong>First Aid Course:</strong> <input type="checkbox" name="first_aid" value="yes" <?php checked($first_aid, 'yes'); ?>> Yes, I have completed a first aid course</p>
                <p><strong>Tilanne Koulutus:</strong> <input type="checkbox" name="tilanne_koulutus" value="yes" <?php checked($tilanne_koulutus, 'yes'); ?>> Yes, I have completed a tilannekoulutuskurssi</p>
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
        #description { display: none; }
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