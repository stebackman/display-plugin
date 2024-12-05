<?php
/*
Plugin Name: Profile plugin
Description: A plugin that displays all user profiles on the frontend, including profile pictures and biographical info. 
Version: 1.3
Author: Group Molto Bene
License: GPL2
*/

if (!defined('ABSPATH')) {
    exit;
}

require_once('includes/display-all-users.php');
require_once('includes/display-one-profile.php');
require_once('includes/display-user-info.php');
require_once('includes/disable-test.php');
require_once('includes/user-profile-tracker.php');


//update user meta with last login time
add_action('wp_login', function($user_login, $user) {
    update_user_meta($user->ID, 'last_login', current_time('mysql'));
}, 10, 2);
?>