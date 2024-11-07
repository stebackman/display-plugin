<?php
/*
Plugin Name: Display All User Profiles
Plugin URI: https://yourwebsite.com/
Description: A plugin that displays all user profiles on the frontend, including profile pictures and biographical info. Users can control visibility of their phone number and email.
Version: 1.3
Author: Your Name
Author URI: https://yourwebsite.com/
License: GPL2
*/

// Step 1: Add visibility fields to user profile
/*function add_visibility_options_to_user_profile($user) {
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

// Step 2: Save the visibility options
function save_visibility_options($user_id) {
    if (current_user_can('edit_user', $user_id)) {
        update_user_meta($user_id, 'show_email', isset($_POST['show_email']) ? 'yes' : 'no');
        update_user_meta($user_id, 'show_phone_number', isset($_POST['show_phone_number']) ? 'yes' : 'no');
    }
}
add_action('personal_options_update', 'save_visibility_options');
add_action('edit_user_profile_update', 'save_visibility_options');
*/

// Step 3: Shortcode to Display All User Profiles
// Step 3: Shortcode to Display Filtered User Profiles by Department
// Shortcode to Display Filtered User Profiles by Department
function display_all_user_profiles_shortcode($atts) {
    $search_query = isset($_GET['search_user']) ? sanitize_text_field($_GET['search_user']) : '';
    
    $args = array(
        'orderby' => 'display_name',
        'order'   => 'ASC',
        'meta_query' => array(),
    );

    if (!empty($search_query)) {
        $args['meta_query'][] = array(
            'relation' => 'OR',
            array(
                'key'     => 'first_name',
                'value'   => $search_query,
                'compare' => 'LIKE'
            ),
            array(
                'key'     => 'last_name',
                'value'   => $search_query,
                'compare' => 'LIKE'
            ),
            array(
                'key'     => 'display_name',
                'value'   => $search_query,
                'compare' => 'LIKE'
            ),
        );
    }

    $users = get_users($args);
    $current_user_id = get_current_user_id();

    ob_start();

    // Display search form and department filter
    ?>
    <form action="" method="get">
        <input type="hidden" name="page_id" value="<?php echo get_the_ID(); ?>" />
        <input type="text" name="search_user" placeholder="Search profiles..." value="<?php echo esc_attr($search_query); ?>" />
        <button type="submit">Search</button>
    </form>

    <label for="department">Select Department:</label>
    <select id="department-filter">
        <option value="all">All Departments</option>
        <option value="MC Executors - Uusimaa">MC Executors - Uusimaa</option>
        <option value="MC Executors - Pohjanmaa">MC Executors - Pohjanmaa</option>
    </select>
    <?php

    if ($users) {
        echo '<div class="user-profiles">';

        // Separate the current user for priority display
        $current_user_profile = '';
        $other_users_profiles = '';

        foreach ($users as $user) {
            $profile_picture = get_user_meta($user->ID, 'profile_picture', true) ?: get_avatar_url($user->ID, ['size' => 100]);
            $department = get_user_meta($user->ID, 'department', true);
            $biographical_info = get_user_meta($user->ID, 'description', true);

            // Get visibility settings
            $show_email = get_user_meta($user->ID, 'show_email', true) === 'yes';
            $show_phone_number = get_user_meta($user->ID, 'show_phone_number', true) === 'yes';

            ob_start();
            ?>
            <div class="user-profile" data-department="<?php echo esc_attr($department); ?>">
                <div class="user-avatar">
                    <img src="<?php echo esc_url($profile_picture); ?>" alt="<?php echo esc_attr($user->display_name); ?>'s Profile Picture">
                </div>
                <div class="user-details">
                    <h2><?php echo esc_html($user->display_name); ?></h2>
                    <p><strong>Name:</strong> <?php echo esc_html($user->first_name . ' ' . $user->last_name); ?></p>
                    <?php if ($show_email) : ?>
                        <p><strong>Email:</strong> <?php echo esc_html($user->user_email); ?></p>
                    <?php endif; ?>
                    <?php if ($show_phone_number) : ?>
                        <p><strong>Phone Number:</strong> <?php echo esc_html(get_user_meta($user->ID, 'phone_number', true)); ?></p>
                    <?php endif; ?>
                    <p><strong>Department:</strong> <?php echo esc_html($department); ?></p>
                    <p><strong>Motorcycle:</strong> <?php echo esc_html($user->motorcycle); ?></p>
                    <p><strong>Company:</strong> <?php echo esc_html($user->company); ?></p>
        
                    <div class="biography">
                        <label for="biographical_info">Biographical Info:</label>
                        <textarea id="biographical_info" name="biographical_info" disabled><?php echo esc_textarea($biographical_info); ?></textarea>
                    </div>
                    <p><a href="<?php echo esc_url('/wordpress/?page_id=109&user_id=' . $user->ID); ?>" class="view-profile-button">View Profile</a></p>
                    <?php if ($current_user_id === (int) $user->ID) : ?>
                        <p><a href="<?php echo esc_url(get_permalink(get_page_by_path('oma-profiilisivu'))); ?>">Edit Profile</a></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php
            $profile_output = ob_get_clean();

            if ($user->ID == $current_user_id) {
                $current_user_profile = $profile_output;
            } else {
                $other_users_profiles .= $profile_output;
            }
        }

        echo $current_user_profile;
        echo $other_users_profiles;

        echo '</div>';
    } else {
        echo '<p>No user profiles found.</p>';
    }
    
    // JavaScript for department filtering
    ?>
    <script>
        document.getElementById('department-filter').addEventListener('change', function () {
            var selectedDepartment = this.value;
            var profiles = document.querySelectorAll('.user-profile');

            profiles.forEach(function(profile) {
                var department = profile.getAttribute('data-department');
                if (selectedDepartment === 'all' || department === selectedDepartment) {
                    profile.style.display = 'block';
                } else {
                    profile.style.display = 'none';
                }
            });
        });
    </script>
    <?php

    return ob_get_clean();
}
add_shortcode('display_all_user_profiles', 'display_all_user_profiles_shortcode');

// Step 4: Add CSS for Styling (Optional)
function display_user_profiles_styles() {
    echo "
    <style>
        .user-profiles {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .user-profile {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            background-color: #f9f9f9;
            max-width: 300px;
            flex: 1 1 calc(33% - 40px);
        }
        .user-avatar {
            margin-bottom: 15px;
            text-align: center;
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
        .view-profile-button {
            display: inline-block;
            padding: 8px 12px;
            color: #fff;
            background-color: #0073aa;
            border-radius: 5px;
            text-decoration: none;
        }
        .view-profile-button:hover {
            background-color: #005177;
        }
    </style>
    ";
}
add_action('wp_head', 'display_user_profiles_styles');
?>