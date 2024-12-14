<?php
/*
Plugin Name: Display User Info
Description: A plugin that displays and allows editing of user information on the frontend, including a profile picture, first-aid course checkbox, company, and motorcycle fields.
Version: 1.4
Author: Group Molto Bene
License: GPL2
*/

// Step 1: Add custom fields to the user profile
function custom_user_profile_fields($user) {
    ?>
    <!-- Table 1: Profiili -->
    <h3>Profiili</h3>
    <table class="form-table">
        <tr>
            <th><label for="titteli">Titteli</label></th>
            <td>
                <?php $titteli = get_user_meta($user->ID, 'titteli', true); ?>
                <select id="titteli" name="titteli">
    <option value="Kokelas" <?php selected($titteli, 'Kokelas', true); ?>>Kokelas</option>
    <option value="Jäsen" <?php selected($titteli, 'Jäsen', true); ?>>Jäsen</option>
    <option value="Puheenjohtaja" <?php selected($titteli, 'Puheenjohtaja', true); ?>>Puheenjohtaja</option>
    <option value="Kunniajäsen" <?php selected($titteli, 'Kunniajäsen', true); ?>>Kunniajäsen</option>
    <option value="Vice_president" <?php selected($titteli, 'Vice_president', true); ?>>Vice President</option>
    <option value="Past_president" <?php selected($titteli, 'Past_president', true); ?>>Past President</option>
    <option value="muu_hallituksen_jäsen" <?php selected($titteli, 'Muu hallituksen jäsen', true); ?>>Muu hallituksen jäsen</option>
    <option value="Aluevastaava" <?php selected($titteli, 'Aluevastaavat', true); ?>>Aluevastaavat</option>
</select>
                </select>
</td>
        </tr>
        <?php if ($titteli === "Kunniajäsen"):?>
        <tr>
            <th><label for="vip_member">Vuoden kunniajäsen</label></th>
            <td>
                <input type="checkbox" name="vip_member" id="vip_member" value="yes" <?php checked(get_user_meta($user->ID, 'vip_member', true), 'yes'); ?>>
                <label for="vip_member">Lisää kruunu profiilikuvaan</label>
            </td>
            <tr>
            <th><label for="honorary_number">Kunniajäsennumero</label></th>
            <td>
                <input type="text" name="honorary_number" id="honorary_number" value="<?php echo esc_attr(get_user_meta($user->ID, 'honorary_number', true)); ?>" class="regular-text">
            </td>
            <tr>
            <th><label for="appointed_date">Nimitetty kunniajäseneksi vuonna: Vain vuosi näytetään</label></th>
            <td>
                <input type="date" name="appointed_date" id="appointed_date" value="<?php echo esc_attr(get_user_meta($user->ID, 'appointed_date', true)); ?>">
            </td>
        </tr>
        </tr>
        <tr>
            <th><label for="vip_member_info">Tähän laatikkoon voi halutessaan kirjoittaa tietoa kunniajäsenestä. HUOM! Näytetään verkkosivuilla kaikille!</label></th>
            <td>
                <textarea name="vip_member_info" id="vip_member_info" rows="5" class="regular-text"><?php echo esc_textarea(get_user_meta($user->ID, 'vip_member_info', true)); ?></textarea>
            </td>
        </tr>
        <?php endif; ?>
        <tr>
            <th><label for="phone_number">Puhelinnumero</label></th>
            <td>
                <input type="text" name="phone_number" id="phone_number" value="<?php echo esc_attr(get_user_meta($user->ID, 'phone_number', true)); ?>" class="regular-text">
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
        <tr>
            <th><label for="department">Alue</label></th>
            <td>
                <?php $department = get_user_meta($user->ID, 'department', true); ?>
                <select id="department" name="department">
                    <option value="Pirkanmaa" <?php selected($department, 'Pirkanmaa', true); ?>>Pirkanmaa</option>
                    <option value="Pohjanmaa" <?php selected($department, 'Pohjanmaa', true); ?>>Pohjanmaa</option>
                    <option value="Päijät-Häme&Kaakkois-Suomi" <?php selected($department, 'Päijät-Häme&Kaakkois-Suomi', true); ?>>Päijät-Häme&Kaakkois-Suomi</option>
                    <option value="Uusimaa" <?php selected($department, 'Uusimaa', true); ?>>Uusimaa</option>
                    <option value="Varsinais-Suomi" <?php selected($department, 'Varsinais-Suomi', true); ?>>Varsinais-Suomi</option>
                </select>
            </td>
        </tr>
               <tr>
            <th><label for="biographical_info">Tähän laatikkoon käyttäjä voi lisätä hieman tietoa itsestään</label></th>
            <td>
                <textarea name="biographical_info" id="biographical_info" rows="5" class="regular-text"><?php echo esc_textarea(get_user_meta($user->ID, 'biographical_info', true)); ?></textarea>
            </td>
        </tr>
    </table>
<!-- Table 2: Laskutustiedot -->
<h3>Laskutustiedot</h3>
<table class="form-table">
<tr>
            <th><label for="use_different_invoice_email"><?php _e("Laskutus-sposti eroaa käyttäjä-spostista", "custom-invoice-email"); ?></label></th>
            <td>
                <input type="checkbox" name="use_different_invoice_email" id="use_different_invoice_email" value="1" <?php checked(get_user_meta($user->ID, 'use_different_invoice_email', true), '1'); ?> />
                <span class="description"><?php _e("Check this box if you want to use a different email for invoices.", "custom-invoice-email"); ?></span>
            </td>
        </tr>
        
        <tr id="invoice_email_row" style="display: <?php echo (get_user_meta($user->ID, 'use_different_invoice_email', true) == '1') ? 'table-row' : 'none'; ?>;">
            <th><label for="invoice_email"><?php _e("Laskutus-sposti", "custom-invoice-email"); ?></label></th>
            <td>
                <input type="email" name="invoice_email" id="invoice_email" value="<?php echo esc_attr(get_user_meta($user->ID, 'invoice_email', true)); ?>" class="regular-text" />
                <span class="description"><?php _e("Enter a different email address for invoices (if checked above).", "custom-invoice-email"); ?></span>
            </td>
        </tr>
    <tr>
  

            <th><label for="osoite">Osoite</label></th>
            <td>
                <input type="text" name="osoite" id="osoite" value="<?php echo esc_attr(get_user_meta($user->ID, 'osoite', true)); ?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th><label for="postinumero">Postinumero</label></th>
            <td>
                <input type="text" name="postinumero" id="postinumero" value="<?php echo esc_attr(get_user_meta($user->ID, 'postinumero', true)); ?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th><label for="postitoimipaikka">Postitoimipaikka</label></th>
            <td>
                <input type="text" name="postitoimipaikka" id="postitoimipaikka" value="<?php echo esc_attr(get_user_meta($user->ID, 'postitoimipaikka', true)); ?>" class="regular-text">
            </td>
        </tr>
    </table>

    <!-- Table 3: Koulutukset -->
    <h3>Koulutukset</h3>
    <table class="form-table">
        <tr>
            <th><label for="first_aid">Ensiapukoulutus suoritettu:</label></th>
            <td>
                <input type="date" name="first_aid" id="first_aid" value="<?php echo esc_attr(get_user_meta($user->ID, 'first_aid', true)); ?>">
            </td>
        </tr>
        <tr>
            <th><label for="tilanne_koulutus">Tilannejohtamiskoulutus suoritettu</label></th>
            <td>
                <input type="date" name="tilanne_koulutus" id="tilanne_koulutus" value="<?php echo esc_attr(get_user_meta($user->ID, 'tilanne_koulutus', true)); ?>">
            </td>
        </tr>
    </table>
    <script type="text/javascript">
        jQuery(document).ready(function($){
            // Toggle the invoice email input field based on the checkbox
            $('#use_different_invoice_email').change(function(){
                if($(this).is(':checked')) {
                    $('#invoice_email_row').show();
                } else {
                    $('#invoice_email_row').hide();
                }
            }).trigger('change');
        });
    </script>
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
        $fields = ['first_name','last_name','user_email','phone_number','titteli','honorary_number','osoite','postinumero','postitoimipaikka', 'department', 'company', 'motorcycle', 'vip_member','vip_member_info','member_id', 'biographical_info'];
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                if ($field === 'user_email') {
                    // Update user email separately
                    $user_data = [
                        'ID'         => $user_id,
                        'user_email' => sanitize_email($_POST[$field]),
                    ];
                    wp_update_user($user_data);
                } else {
                    update_user_meta($user_id, $field, sanitize_text_field($_POST[$field]));
                }
            }
        }

        // Handle first aid date
        if (isset($_POST['first_aid'])) {
            $date_value = sanitize_text_field($_POST['first_aid']);
            if (!empty($date_value)) {
                update_user_meta($user_id, 'first_aid', $date_value);
            }
        }
        if (isset($_POST['appointed_date'])) {
            $date_value = sanitize_text_field($_POST['appointed_date']);
            if (!empty($date_value)) {
                update_user_meta($user_id, 'appointed_date', $date_value);
            }
        }

        // Handle tilanne_koulutus date
        if (isset($_POST['tilanne_koulutus'])) {
            $date_value = sanitize_text_field($_POST['tilanne_koulutus']);
            if (!empty($date_value)) {
                update_user_meta($user_id, 'tilanne_koulutus', $date_value);
            }
        }
        // Save the checkbox value
    update_user_meta($user_id, 'use_different_invoice_email', isset($_POST['use_different_invoice_email']) ? '1' : '0');

    // Save the invoice email if the checkbox is checked
    if (isset($_POST['use_different_invoice_email']) && !empty($_POST['invoice_email'])) {
        update_user_meta($user_id, 'invoice_email', sanitize_email($_POST['invoice_email']));
    } elseif (!isset($_POST['use_different_invoice_email'])) {
        // If checkbox is unchecked, set invoice_email to user_email
        update_user_meta($user_id, 'invoice_email', get_userdata($user_id)->user_email);
    }

        // Handle hide email and phone number
        update_user_meta($user_id, 'hide_email', isset($_POST['hide_email']) ? 'yes' : 'no');
        update_user_meta($user_id, 'hide_phone_number', isset($_POST['hide_phone_number']) ? 'yes' : 'no');
    }

    // Save the last updated timestamp
    update_user_meta($user_id, '_profile_last_updated', current_time('mysql'));
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
        $success_message = '<div id="update-success" class="update-success">Profiili päivitetty onnistuneesti!</div>';
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
        save_custom_user_profile_fields($current_user->ID);

        // Redirect to avoid resubmission and show success message
        wp_redirect(add_query_arg('profile_updated', '1', get_permalink()));
        exit;
    }

$style = "
<style>
    .user-info p,h1, h2, h3, h4, h5, h6 {
        font-family: 'Montserrat', serif;
    }

    .user-info {
        display: flex;
        flex-direction: column;
        gap: 15px;
        padding: 20px;
        max-width: 600px;
        margin: 20px auto;
        border: 1px solid #ddd;
        border-radius: 8px;
        text-decoration: none;
        background-color: #f3f3f3;
        box-shadow: 0.1rem 0.2rem 5px #5a6142;
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
    .user-avatar .vip-crown {
        position: absolute;
        top: -15px;
        right: 230px;
        font-size: 24px;
        color: gold;
    }

    /* User details and input fields */
    .user-details p {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #333;
        font-size: 12px;
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
        font-size: 12px;
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
        font-size: 12px;

    }
    .biography label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
        color: #555;
    }
    .biography textarea {
        resize: vertical;
        min-height: 80px;
        font-family: 'Montserrat', serif;
    }
    .biography .visibility-options h4 {
        font-family: 'Montserrat', serif;
        font-weight: bold;
        font-size: 12px;
        color: #555;
    }

    /* Visibility options */
    .visibility-options {
        border-top: 1px solid #ddd;
        padding-top: 0px;
        margin-top: 15px;
 
    }
    .visibility-options label {
        display: block;
        margin: 5px 0;
        color: #444;
        font-weight: normal;    
    }

    /* Action buttons */
    .update-button,
    .reset-password-button {
        display: inline-block;
        padding: 5px 20px;
        color: #e7e6da;
         width: 12rem;
        font-weight: bold;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        font-size: 12px;
        margin-top: 15px;
        width: calc(50% - 10px);
        box-sizing: border-box;
        box-shadow: 0.1rem 0.2rem 3px #5a6142;
    }
    .reset-password-button {
        background-color: #734e30;
         color: #e7e6da;
    }
    .reset-password-button:hover {
        background-color: #1f2518;
        color: #e2c275;
    }
    .update-button {
        background-color: #e2c275;
         color: #1F2518;
    }
    .update-button:hover {
         background-color: #1f2518;
        color: #e2c275;
    }
    .update-success {
           margin: 20px auto;
        padding: 10px;
        max-width: 600px;
        background-color: #a4ac86;
        color: #5a6142;
        border: 1px solid #5a6142;
        border-radius: 5px;
        font-size: 12px;
        text-align: center;
    }
    .success-message {
        color: #5a6142;
        font-weight: bold;
        margin-top: 20px;
    }
    input[type='submit'] {
        font-family: 'Montserrat', serif;
        padding: 5px 20px;
        background-color: #e2c275;
        color: #1F2518;
        border-radius: 8px;
        font-weight: bold;
        font-size: 12px;
        border: none;
        cursor: pointer;
        font-family: 'Montserrat', serif;
        box-shadow: 0.1rem 0.2rem 3px #5a6142;
    }   
    
    #change-password-form h4 {
        font-size: 12px;  
    }

</style>
";
  // Retrieve the profile picture, phone number, department, and biographical info
    $profile_picture = get_user_meta($current_user->ID, 'profile_picture', true) ?: get_avatar_url($current_user->ID, ['size' => 100]);
    $honorary_number = get_user_meta($current_user->ID, 'honorary_number', true);
    $phone_number = get_user_meta($current_user->ID, 'phone_number', true);
    $home_address = get_user_meta($current_user->ID, 'osoite', true);
    $zipcode = get_user_meta($current_user->ID, 'postinumero', true);
    $city = get_user_meta($current_user->ID, 'postitoimipaikka', true);

    $profile_title = get_user_meta($current_user->ID, 'titteli', true);
    $department = get_user_meta($current_user->ID, 'department', true);
    $company = get_user_meta($current_user->ID, 'company', true);
    $motorcycle = get_user_meta($current_user->ID, 'motorcycle', true);
    $biographical_info=get_user_meta($current_user->ID,'biographical_info',true);
    $custom_user_id = get_user_meta($current_user->ID, 'custom_user_id', true);
    $vip_member= get_user_meta($current_user->ID,'vip_member',true)==='yes';
    $kunniajasen=get_user_meta($current_user->ID,'titteli',true)==='Kunniajäsen';

    $first_aid= get_user_meta($current_user->ID,'first_aid',true);
    if (!empty($first_aid)) {
        $first_aid = date('d.m.Y', strtotime($first_aid)); 
    }

    $tilanne_koulutus= get_user_meta($current_user->ID,'tilanne_koulutus',true);
    if (!empty($tilanne_koulutus)) {
        $tilanne_koulutus = date('d.m.Y', strtotime($tilanne_koulutus)); 
    }
    $appointed_date= get_user_meta($current_user->ID,'appointed_date',true);
    if (!empty($appointed_date)) {
        $appointed_date = date('Y', strtotime($appointed_date)); 
    }
    
    $different_email=get_user_meta($current_user->ID,'different_email',true);

    // Retrieve visibility options
    $hide_email = get_user_meta($current_user->ID, 'hide_email', true);
    $hide_phone_number = get_user_meta($current_user->ID, 'hide_phone_number', true);

    
    ob_start();
    echo $style;
    echo $success_message;
    ?>
    <script>
    // Check if the success message exists and set a timeout to hide it after 5 seconds
    document.addEventListener("DOMContentLoaded", function() {
        const successMessage = document.getElementById("update-success");
        if (successMessage) {
            setTimeout(function() {
                successMessage.style.display = "none";
            }, 5000); // Hide the message after 5 seconds
        }
    });
</script>
    <form method="POST" enctype="multipart/form-data">
        <div class="user-info">
       <a href="<?php echo esc_url(get_permalink(get_page_by_path('kaikki-profiilit'))); ?>"><p class="view-profile-button">Näytä kaikki profiilit</p></a> 
            <div class="user-avatar">
                <img src="<?php echo esc_url($profile_picture); ?>" alt="Profile Picture">
                <p><label for="profile_picture">Vaihda profiilikuvaa:</label></p>
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
                <?php if ($vip_member): ?>
                        <span class="vip-crown">&#x1F451;</span>
                        <?php endif; ?>
        
            <div class="user-details">
                <p><strong>Käyttäjänimi:</strong> <?php echo esc_html($current_user->display_name); ?></p>
                <p><strong>Etunimi:</strong> <input type="text" name="first_name" value="<?php echo esc_attr($current_user->first_name); ?>" class="regular-text"></p>
                <p><strong>Sukunimi:</strong> <input type="text" name="last_name" value="<?php echo esc_attr($current_user->last_name); ?>" class="regular-text"></p>
                <p><strong>Jäsennumero: <?php echo esc_attr(($custom_user_id)); ?></strong></p>
                <?php if ($kunniajasen):?>
                <p><strong>Kunniajäsennumero:</strong> <?php echo esc_attr($honorary_number);?></p>
                <p><strong> Nimitetty kunniajäseneksi: <?php echo esc_attr($appointed_date); ?> </strong> 
                <?php endif; ?>
                <p> <strong>Titteli:</strong> <?php echo esc_attr(($profile_title)); ?></strong></p>
                <p> <strong>Alue:</strong>  <select name="department" id="department">
                <option value="Pirkanmaa" <?php selected($department, 'Pirkanmaa'); ?>>Pirkanmaa</option>
                    <option value="Pohjanmaa" <?php selected($department, 'Pohjanmaa'); ?>>Pohjanmaa</option>
                    <option value="Päijät-Häme&Kaakkois-Suomi" <?php selected($department, 'Päijät-Häme&Kaakkois-Suomi'); ?>>Päijät-Häme&Kaakkois-Suomi</option>
                    <option value="Uusimaa" <?php selected($department, 'Uusimaa',true); ?>>Uusimaa</option>
                    <option value="Varsinais-Suomi" <?php selected($department, 'Varsinais-Suomi',true); ?>>Varsinais-Suomi</option></select></p>
                <p><strong>Käytäjäsähköposti:</strong> <input type="text" name="user_email" value="<?php echo esc_attr($current_user->user_email); ?>" class="regular-text"></p>
                <p><strong>Puhelinnumero:</strong> <input type="text" name="phone_number" value="<?php echo esc_attr($phone_number); ?>" class="regular-text"></p>
                <p><strong>Yritys:</strong> <input type="text" name="company" value="<?php echo esc_attr($company); ?>" class="regular-text"></p>
                <p><strong>Moottoripyörä:</strong> <input type="text" name="motorcycle" value="<?php echo esc_attr($motorcycle); ?>" class="regular-text"></p>
                <div class="biography">
                        <label for="biographical_info"> Kerro muille hieman itsestäsi:</label>
                        <textarea id="biographical_info" name="biographical_info" ><?php echo esc_textarea($biographical_info); ?></textarea>    
                <p style="text-decoration: underline;"><strong>Koulutukset</strong></p> 
                  <?php if (!empty($first_aid) && $first_aid !== '01.01.1970') : ?>
                    <p><strong> Ensiapukoulutus suoritettu: <?php echo esc_attr($first_aid); ?> </strong> 
                    <?php endif; ?>
                <?php if (!empty($tilanne_koulutus) && $tilanne_koulutus !== '01.01.1970') : ?>
                    <p><strong>Tilannejohtamiskurssi suoritettu: <?php echo esc_attr($tilanne_koulutus); ?> </strong> 
                    <?php endif; ?>
                    <p style="text-decoration: underline;"><strong>Laskutustiedot</strong></p>
                    <label><input type="checkbox" name="different_email" value="yes" <?php checked(get_user_meta(get_current_user_id(), 'different_email', true), 'yes'); ?>> Laskutussähköpostini eroaa käyttäjäsähköpostistani</label>
                <p><strong>Osoite:</strong> <input type="text" name="osoite" value="<?php echo esc_attr($home_address); ?>" class="regular-text"></p>
                <p><strong>Postinumero:</strong> <input type="text" name="postinumero" value="<?php echo esc_attr($zipcode); ?>" class="regular-text"></p>
                <p><strong>Postitoimipaikka:</strong> <input type="text" name="postitoimipaikka" value="<?php echo esc_attr($city); ?>" class="regular-text"></p>
                <div class="visibility-options">
                    <h4>Profiilin näkyvyysasetukset</h4>
                    <label><input type="checkbox" name="hide_email" value="yes" <?php checked($hide_email, 'yes'); ?>> Piilota sähköpostini muilta käyttäjiltä</label>
                    <label><input type="checkbox" name="hide_phone_number" value="yes" <?php checked($hide_phone_number, 'yes'); ?>> Piilota puhelinnumeroni muilta käyttäjiltä</label>
                </div>
 <!-- Reset Password Button -->
 <button type="button" name="reset_password-button" class="reset-password-button" onclick="toggleChangePasswordForm()">Aseta uusi salasana:</button>

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

    // Check current form display status and toggle
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block'; // Show the form
        updateButton.disabled = true; // Disable update profile button
        resetButton.textContent = 'Peruuta'; // Change button text to "Cancel"
    } else {
        form.style.display = 'none'; // Hide the form
        updateButton.disabled = false; // Re-enable update profile button
        resetButton.textContent = 'Vaihda salasanaa'; // Reset button text to "Change Password"
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
        .user-profile-picture,
        .user-syntax-highlighting-wrap,
        .image-container,
        .upload-avatar-row,
        .ratings-row,
        #simple-local-avatar-section,
        .description,
        #description, label[for="description"],
        #profile-description{display: none !important;},
       
    </style>';
}
add_action('admin_head', 'hide_unnecessary_profile_fields');




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
