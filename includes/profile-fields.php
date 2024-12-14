<?php function custom_user_profile_fields($user) {
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
    <option value="Vice_president" <?php selected($titteli, 'Vice_president', true); ?>>Vice President/option>
    <option value="Past_president" <?php selected($titteli, 'Past_president', true); ?>>Past President</option>
    <option value="muu_hallituksen_jäsen" <?php selected($titteli, 'Muu hallituksen jäsen', true); ?>>Muu hallituksen jäsen</option>
    <option value="Aluevastaava" <?php selected($titteli, 'Aluevastaavat', true); ?>>Aluevastaavat</option>
</select>
                </select>
</td>
        </tr>
        
        <tr>
            <td>
                <input type="checkbox" name="vip_member_icon" id="vip_member_icon" value="yes" <?php checked(get_user_meta($user->ID, 'vip_member_icon', true), 'yes'); ?>>
                <label for="vip_member_icon">Lisää kruunu profiilikuvan viereen</label>
            </td>
            <td>
                <input type="checkbox" name="cross_icon" id="cross_icon" value="yes" <?php checked(get_user_meta($user->ID, 'cross_icon', true), 'yes'); ?>>
                <label for="cross_icon">Lisää risti profiilikuvan viereen</label>
            </td>
        </tr>
        <?php if ($titteli === "Kunniajäsen"):?>
            <th><label for="honorary_number">Kunniajäsennumero</label></th>
            <td>
                <input type="text" name="honorary_number" id="honorary_number" value="<?php echo esc_attr(get_user_meta($user->ID, 'honorary_number', true)); ?>" class="regular-text">
            </td>
            <tr>
            <th><label for="appointed_date">Nimitetty kunniajäseneksi vuonna:</label></th>
            <td>
                <input type="number" name="appointed_date" id="appointed_date" value="<?php echo esc_attr(get_user_meta($user->ID, 'appointed_date', true)); ?>">
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
<h3 id="fennoa_laskutus_tiedot">Laskutustiedot</h3>
<p>Alla olevat tiedot tulee olla täytettynä, jotta käyttäjälle voidaan luoda Fennoa asiakasnumero.</p>
<table class="form-table">
<tr>
<tr>
            <th><label for="fennoa_email">Laskutus-sähköposti</label></th>
            <td>
                <input type="text" name="fennoa_email" id="fennoa_email" value="<?php echo esc_attr(get_user_meta($user->ID, 'fennoa_email', true)); ?>" class="regular-text">
            </td>
</tr>
    <tr>
            <th><label for="fennoa_address">Osoite</label></th>
            <td>
                <input type="text" name="fennoa_address" id="fennoa_address" value="<?php echo esc_attr(get_user_meta($user->ID, 'fennoa_address', true)); ?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th><label for="fennoa_postcode">Postinumero</label></th>
            <td>
                <input type="text" name="fennoa_postcode" id="fennoa_postcode" value="<?php echo esc_attr(get_user_meta($user->ID, 'fennoa_postcode', true)); ?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th><label for="fennoa_city">Postitoimipaikka</label></th>
            <td>
                <input type="text" name="fennoa_city" id="fennoa_city" value="<?php echo esc_attr(get_user_meta($user->ID, 'fennoa_city', true)); ?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th><label for="fennoa_country_code">Maa</label></th>
            <td>
                <input type="text" name="fennoa_country_code" id="fennoa_country_code" value="<?php echo esc_attr(get_user_meta($user->ID, 'fennoa_country_code', true)); ?>" class="regular-text">
                <p class="description">ISO 3166-1 alpha-2 -muoto (esim. FI = Suomi).</p>
                
            </td>
        </tr>
        <tr>
            <th><label for="fennoa_customer_number">Fennoa asiakasnumero</label></th>
            <td>
                <input type="text" name="fennoa_customer_number" id="fennoa_customer_number" value="<?php echo esc_attr(get_user_meta($user->ID, 'fennoa_customer_number', true)); ?>" class="regular-text">
                <p class="description">Jos jäsenellä ei ole asiakasnumeroa,uusi Fennoa-asiakasnumero luodaan Fennoa Laskut-lisäosassa</p>
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
        $fields = ['first_name','last_name','user_email','phone_number','fennoa_email','fennoa_address','fennoa_postcode','fennoa_city','fennoa_country_code','fennoa_delivery_method','titteli','honorary_number','osoite','postinumero','postitoimipaikka', 'department', 'company', 'motorcycle','cross_icon', 'vip_member_icon','vip_member_info','member_id', 'biographical_info'];
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

        // Handle hide email and phone number
        update_user_meta($user_id, 'hide_email', isset($_POST['hide_email']) ? 'yes' : 'no');
        update_user_meta($user_id, 'hide_phone_number', isset($_POST['hide_phone_number']) ? 'yes' : 'no');
    }

    // Save the last updated timestamp
    update_user_meta($user_id, '_profile_last_updated', current_time('mysql'));
}
add_action('personal_options_update', 'save_custom_user_profile_fields');
add_action('edit_user_profile_update', 'save_custom_user_profile_fields');


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
        #email-description,
        .user-description-wrap,
        .ratings-row,
        #simple-local-avatar-section,
        #description, label[for="description"],
        #profile-description{display: none !important;},
       
    </style>';
}
add_action('admin_head', 'hide_unnecessary_profile_fields');