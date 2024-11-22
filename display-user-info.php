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
    <h3>Profiili</h3>
    <table class="form-table">
        <tr>
            <th><label for="phone_number">Puhelinnumero</label></th>
            <td>
                <input type="text" name="phone_number" id="phone_number" value="<?php echo esc_attr(get_user_meta($user->ID, 'phone_number', true)); ?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th><label for="department">Osasto</label></th>
            <td>
                  <select id="department-filter">
        <option value="MC Executors - Uusimaa">MC Executors - Uusimaa</option>
        <option value="MC Executors - Pohjanmaa">MC Executors - Pohjanmaa</option>
    </select>
            </td>
        </tr>
        <tr>
            <th><label for="company">Yritys</label></th>
            <td>
                <input type="text" name="company" id="company" value="<?php echo esc_attr(get_user_meta($user->ID, 'company', true)); ?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th><label for="motorcycle">Moottoripyörä</label></th>
            <td>
                <input type="text" name="motorcycle" id="motorcycle" value="<?php echo esc_attr(get_user_meta($user->ID, 'motorcycle', true)); ?>" class="regular-text">
            </td>
        </tr>
        <?php if (current_user_can('administrator')) : ?>
        <tr>
            <th><label for="first_aid">Ensiapukoulutus suoritettu:</label></th>
            <td>
            <input type="date" name="first_aid" id="first_aid" 
            value="<?php echo esc_attr(get_user_meta($user->ID, 'first_aid', true)); ?>">
                
            </td>
        </tr>
        <tr>
            <th><label for="tilanne_koulutus">Tilanneturvallisuuskoulutus suoritettu</label></th>
            <td>
            <input type="date" name="tilanne_koulutus" id="tilanne_koulutus" 
       value="<?php echo esc_attr(get_user_meta($user->ID, 'tilanne_koulutus', true)); ?>">
            </td>
        </tr>
        
        <tr>
            
            <th><label for="vip_member">Vuoden kunniajäsen</label></th>
            <td>
                <input type="checkbox" name="vip_member" id="vip_member" value="yes" <?php checked(get_user_meta($user->ID, 'vip_member', true), 'yes'); ?>>
                <label for="vip_member">Tämä jäsen on viime vuoden kunniajäsen</label>
            </td>
        </tr>
        <?php endif; ?>
        <tr>
            <th><label for="biographical_info">Kerro vähän itsestäsi</label></th>
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
        //Handle first aid
        if (isset($_POST['first_aid'])) {
            $date_value = sanitize_text_field($_POST['first_aid']);
            if (!empty($date_value)) {
                update_user_meta($user_id, 'first_aid', $date_value);
            }
        }
        
 // Handle tilanne_koulutus
 if (isset($_POST['tilanne_koulutus'])) {
    $date_value = sanitize_text_field($_POST['tilanne_koulutus']);
    if (!empty($date_value)) {
        update_user_meta($user_id, 'tilanne_koulutus', $date_value);
    }
}


        update_user_meta($user_id, 'hide_email', isset($_POST['hide_email']) ? 'yes' : 'no');
        update_user_meta($user_id, 'hide_phone_number', isset($_POST['hide_phone_number']) ? 'yes' : 'no');
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
    $success_message = '';
    if (isset($_GET['profile_updated']) && $_GET['profile_updated'] == '1') {
        $success_message = '<div class="update-success">Profiili päivitetty onnistuneesti!</div>';
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
        save_custom_user_profile_fields($current_user->ID);

        // Redirect to avoid resubmission and show success message
        wp_redirect(add_query_arg('profile_updated', '1', get_permalink()));
        exit;
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
        .update-success {
            margin: 20px auto;
            padding: 10px;
            max-width: 600px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            font-size: 1em;
            text-align: center;
        }
.success-message {
    color: green;
    font-weight: bold;
    margin-top: 20px;
}
</style>
";
  // Retrieve the profile picture, phone number, department, and biographical info
    $profile_picture = get_user_meta($current_user->ID, 'profile_picture', true) ?: get_avatar_url($current_user->ID, ['size' => 100]);
    $phone_number = get_user_meta($current_user->ID, 'phone_number', true);
    $department = get_user_meta($current_user->ID, 'department', true);
    $company = get_user_meta($current_user->ID, 'company', true);
    $motorcycle = get_user_meta($current_user->ID, 'motorcycle', true);
    $biographical_info=get_user_meta($current_user->ID,'biographical_info',true);
    $custom_user_id = get_user_meta($current_user->ID, 'custom_user_id', true);
    $vip_member= get_user_meta($current_user->ID,'vip_member',true)==='yes';



    $first_aid= get_user_meta($current_user->ID,'first_aid',true);
    if (!empty($first_aid)) {
        $first_aid = date('d.m.Y', strtotime($first_aid)); 
    }

    $tilanne_koulutus= get_user_meta($current_user->ID,'tilanne_koulutus',true);
    if (!empty($tilanne_koulutus)) {
        $tilanne_koulutus = date('d.m.Y', strtotime($tilanne_koulutus)); 
    }
    


    // Retrieve visibility options
    $hide_email = get_user_meta($current_user->ID, 'hide_email', true);
    $hide_phone_number = get_user_meta($current_user->ID, 'hide_phone_number', true);

    ob_start();
    echo $style;
    echo $success_message;
    ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="user-info">
        <p class="view-profile-button"><a href="<?php echo esc_url(get_permalink(get_page_by_path('kaikki-profiilit'))); ?>">Näytä kaikki profiilit</a></p>
            <div class="user-avatar">
                <img src="<?php echo esc_url($profile_picture); ?>" alt="Profile Picture">
                <p><label for="profile_picture">Vaihda profiilikuvaa:</label></p>
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
                <?php if ($vip_member): ?>
                        <span class="vip-crown">&#x1F451;</span>
                        <?php endif; ?>
            </div>
            <div class="user-details">
                <p><strong>Käyttäjänimi:</strong> <?php echo esc_html($current_user->user_login); ?></p>
                <p><strong>Etunimi:</strong> <input type="text" name="first_name" value="<?php echo esc_attr($current_user->first_name); ?>" class="regular-text"></p>
                <p><strong>Sukunimi:</strong> <input type="text" name="last_name" value="<?php echo esc_attr($current_user->last_name); ?>" class="regular-text"></p>
                <p><strong>Jäsennumero: <?php echo esc_attr(($custom_user_id)); ?></strong></p>
                <p><strong>Sähköposti:</strong> <?php echo esc_html($current_user->user_email); ?></p>
                <p><strong>Puhelinnumero:</strong> <input type="text" name="phone_number" value="<?php echo esc_attr($phone_number); ?>" class="regular-text"></p>
                <p> <strong>Osasto:</strong>  <select name="department" id="department-filter">
                    <option value="MC Executors - Uusimaa" <?php selected($department, 'MC Executors - Uusimaa'); ?>>MC Executors - Uusimaa</option><option value="MC Executors - Pohjanmaa" <?php selected($department, 'MC Executors - Pohjanmaa'); ?>>MC Executors - Pohjanmaa</option></select>
</p>
                <p><strong>Yritys:</strong> <input type="text" name="company" value="<?php echo esc_attr($company); ?>" class="regular-text"></p>
                <p><strong>Moottoripyörä:</strong> <input type="text" name="motorcycle" value="<?php echo esc_attr($motorcycle); ?>" class="regular-text"></p>
                <?php if (!empty($first_aid) && $first_aid !== '01.01.1970') : ?>
                    <p><strong> Ensiapukoulutus suoritettu: <?php echo esc_attr($first_aid); ?> </strong> 
                    <?php endif; ?>
                <?php if (!empty($tilanne_koulutus) && $tilanne_koulutus !== '01.01.1970') : ?>
                    <p><strong>Tilanneturvallisuuskurssi suoritettu: <?php echo esc_attr($tilanne_koulutus); ?> </strong> 
                    <?php endif; ?>
                <div class="biography">
                        <label for="biographical_info"> Kerro muille hieman itsestäsi:</label>
                        <textarea id="biographical_info" name="biographical_info" ><?php echo esc_textarea($biographical_info); ?></textarea>   

                <!-- Other form fields as before -->

                <div class="visibility-options">
                    <h4>Profiilin näkyvyysasetukset</h4>
                    <label><input type="checkbox" name="hide_email" value="yes" <?php checked($hide_email, 'yes'); ?>> Piilota sähköpostini muilta käyttäjiltä</label>
                    <label><input type="checkbox" name="hide_phone_number" value="yes" <?php checked($hide_phone_number, 'yes'); ?>> Piilota puhelinnumeroni muilta käyttäjiltä</label>
                </div>
 <!-- Reset Password Button -->
 <button type="button" name="reset_password-button" class="reset-password-button" onclick="toggleChangePasswordForm()">Vaihda salasanaa:</button>

<!-- Update Profile Button -->
<button type="submit" name="update_profile" class="update-button">Päivitä profiilia</button>
</div>
</div>
</form>
<div id="change-password-form" class="user-info" style="display: none; margin-top: 20px;">
        <h4>Vaihda salasanaa</h4>
        <form method="post" action="">
            <?php wp_nonce_field('custom_password_change', 'custom_password_change_nonce'); ?>
            <p><label for="current_password">Nykyinen salasana</label><br><input type="password" name="current_password" id="current_password" required></p>
            <p><label for="new_password">Uusi salasana</label><br><input type="password" name="new_password" id="new_password" required></p>
            <p><label for="confirm_password">Vahvista uusi salasana</label><br><input type="password" name="confirm_password" id="confirm_password" required></p>
            <p><input type="submit" value="Vaihda salasanaa"></p>
        </form>
    </div>

    <script>
    function toggleChangePasswordForm() {
        const form = document.getElementById('change-password-form');
        const updateButton = document.querySelector('button[name="update_profile"]');
        const resetButton = document.querySelector('button[name="reset_password-button"]');

        // Toggle form visibility
        if (form.style.display === 'none') {
            form.style.display = 'block';
            updateButton.disabled = true; // Disable profile update while changing password
            resetButton.disabled = true; // Disable the password reset button itself
        } else {
            form.style.display = 'none';
            updateButton.disabled = false; // Enable profile update button
            resetButton.disabled = false; // Enable the password reset button
        }
    }
</script>
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

function update_last_login($user_login, $user) {
    update_user_meta($user->ID, 'last_login', current_time('mysql'));
}
add_action('wp_login', 'update_last_login', 10, 2);


function custom_password_change_form() {
    if (!is_user_logged_in()) {
        echo '<p>You must be logged in to change your password.</p>';
        return;
    }
    $password_change_message = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['custom_password_change_nonce'])) {
        if (!wp_verify_nonce($_POST['custom_password_change_nonce'], 'custom_password_change')) {
            echo '<p>Error: Invalid nonce.</p>';
            return;
        }

        $current_user = wp_get_current_user();
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Check if the current password is correct
        if (!wp_check_password($current_password, $current_user->user_pass, $current_user->ID)) {
            echo '<p>Error: Current password is incorrect.</p>';
            return;
        }

        // Check if the new passwords match
        if (empty($new_password) || $new_password !== $confirm_password) {
            echo '<p>Error: New passwords do not match or are empty.</p>';
            return;
        }

        // Update the password
        $update_result = wp_update_user(['ID' => $current_user->ID, 'user_pass' => $new_password]);
        if (is_wp_error($update_result)) {
            echo '<p>Error: Failed to update password. Please try again.</p>';
        } else {
            echo '<p>Success: Your password has been updated!</p>';
        }
    }
    // Display the password change form and message
    echo $password_change_message;
    ?>
    <div id="change-password-form" style="display: none; margin-top: 20px;">
        <h4>Vaihda salasanaa</h4>
        <form method="post" action="">
            <?php wp_nonce_field('custom_password_change', 'custom_password_change_nonce'); ?>
            <p><label for="current_password">Nykyinen salasana</label><br><input type="password" name="current_password" id="current_password" required></p>
            <p><label for="new_password">Uusi salasana</label><br><input type="password" name="new_password" id="new_password" required></p>
            <p><label for="confirm_password">Vahvista uusi salasana</label><br><input type="password" name="confirm_password" id="confirm_password" required></p>
            <p><input type="submit" value="Vaihda salasanaa"></p>
        </form>
    </div>

    <button type="button" onclick="toggleChangePasswordForm()">Vaihda salasanaa</button>

    <script>
        function toggleChangePasswordForm() {
            const form = document.getElementById('change-password-form');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>

    <?php
}
add_shortcode('custom_password_change_form', 'custom_password_change_form');