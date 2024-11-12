<?php
/*
Plugin Name: Display Selected User Profile
Plugin URI: https://yourwebsite.com/
Description: A plugin that displays a specific user's profile on the frontend based on a user selection from the list page.
Version: 1.1
Author: Your Name
Author URI: https://yourwebsite.com/
License: GPL2
*/

// Shortcode to Display Selected User Profile
function display_selected_user_profile_shortcode() {
    // Check if a user ID is provided in the URL
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

    // If no user ID is specified, show a message or handle it as needed
    if (!$user_id) {
        return '<p>No user selected. Please choose a profile to view.</p>';
    }

    $user = get_user_by('ID', $user_id);

    // If the user does not exist, display an error message
    if (!$user) {
        return '<p>User profile not found.</p>';
    }

    // Get user meta information
    $profile_picture = get_user_meta($user->ID, 'profile_picture', true) ?: get_avatar_url($user->ID, ['size' => 100]);
    $department = get_user_meta($user->ID, 'department', true);
    $biographical_info = get_user_meta($user->ID, 'description', true);
    $company = get_user_meta($user->ID,'company',true);
    $motorcycle = get_user_meta($user->ID,'motorcycle',true);
    $member_id= get_user_meta($user->ID,'member_id',true);
    $vip_member= get_user_meta($user->ID,'vip_member',true);

    //Get first aid and tilannekoulutus:
    $first_aid_completed=get_user_meta($user->ID,'first_aid',true)==='yes';
    $tilanne_koulutus_completed= get_user_meta($user->ID,'tilanne_koulutus',true)==='yes';

    // Get visibility settings
    $show_email = get_user_meta($user->ID, 'show_email', true) === 'yes';
    $show_phone_number = get_user_meta($user->ID, 'show_phone_number', true) === 'yes';

    ob_start();
    ?>
    <div class="user-profile">
        <div class="user-avatar">
            <img src="<?php echo esc_url($profile_picture); ?>" alt="<?php echo esc_attr($user->display_name); ?>'s Profile Picture">
            <?php if ($vip_member): ?>
                <span class="vip-crown">&#x1F451;</span>
                <?php endif; ?>
            </div>
        <div class="user-details">
            <h2><?php echo esc_html($user->display_name); ?></h2>
            <p><strong>Name:</strong> <?php echo esc_html($user->first_name . ' ' . $user->last_name); ?></p>
            <p><strong>Jäsennumero:</strong> <?php echo esc_html($member_id); ?></p>
            <?php if ($show_email) : ?>
                <p><strong>Email:</strong> <?php echo esc_html($user->user_email); ?></p>
            <?php endif; ?>
            <?php if ($show_phone_number) : ?>
                <p><strong>Phone Number:</strong> <?php echo esc_html(get_user_meta($user->ID, 'phone_number', true)); ?></p>
            <?php endif; ?>
            <p><strong>Department:</strong> <?php echo esc_html($department); ?></p>
            <p><strong>Company:</strong><?php echo esc_html($company);?></p>
            <p><strong>Motorcycle:</strong><?php echo esc_html($motorcycle);?></p>
            <div class="biography">
                <label for="biographical_info">Biographical Info:</label>
                <textarea id="biographical_info" name="biographical_info" disabled><?php echo esc_textarea($biographical_info); ?></textarea>
            </div>
            <?php if ($first_aid_completed) : ?>
                <p><strong>First aid completed: 2024</strong> 
            <?php endif; ?>
            <?php if ($tilanne_koulutus_completed) : ?>
                <p><strong>Tilanneturvallisuuskurssi completed: 2024 </strong> 
            <?php endif; ?>
            <?php if (get_current_user_id() === $user->ID) : ?>
                <p><a href="<?php echo esc_url(get_permalink(get_page_by_path('oma-profiilisivu'))); ?>">Edit Profile</a></p>
            <?php endif; ?>

        </div>
    </div>
    <?php

    return ob_get_clean();
}
add_shortcode('display_selected_user_profile', 'display_selected_user_profile_shortcode');

// Optional CSS Styling
function display_user_profile_styles() {
    echo "
    <style>
        .user-profile {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            background-color: #f9f9f9;
            max-width: 300px;
            margin: 20px auto;
            text-align: center;
        }
        .user-avatar {
            margin-bottom: 15px;
            text-align:center;
            position: relative;
            display: inline-block;
        }
        .user-avatar img {
            border-radius: 50%;
            width: 100px;
            height: 100px;
            object-fit: cover;
        }
        .user-details h2 {
            font-size: 1.25em;
            margin: 0 0 10px;
        }
        .user-details p {
            margin: 5px 0;
        }
        .biography {
            margin-top: 10px;
        }
        .biography textarea {
            width: 100%;
            height: 80px;
            resize: none;
        }
            .vip-crown {
            position: absolute;
            top: -10px;
            right: -10px;
            font-size: 24px;
            color: gold;
}
    </style>
    ";
}
add_action('wp_head', 'display_user_profile_styles');
?>