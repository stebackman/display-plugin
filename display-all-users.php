<?php
/*
Plugin Name: Display All User Profiles
Description: A plugin that displays all user profiles on the frontend, including profile pictures and biographical info. Users can control visibility of their phone number and email.
Version: 1.3
Author: Group Molto Bene
License: GPL2
*/

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

    // Display search form, department filter, and view toggle button
    ?>
    <form action="" method="get">
    <input type="hidden" name="page_id" value="<?php echo get_the_ID(); ?>" />
    <input type="text" name="search_user" id="search-user-input" placeholder="Hae profiili..." value="<?php echo esc_attr($search_query); ?>" />
    <button type="submit">Hae</button>
</form>

    <label for="department">Valitse osasto:</label>
    <select id="department-filter">
        <option value="all">Kaikki osastot</option>
        <option value="MC Executors - Uusimaa">MC Executors - Uusimaa</option>
        <option value="MC Executors - Pohjanmaa">MC Executors - Pohjanmaa</option>
    </select>
    <label for="titteli">Valitse titteli:</label>
    <select id="titteli-filter">
        <option value="all">Kaikki tittelit</option>
        <option value="Puheenjohtaja">Puheenjohtaja</option>
        <option value="Kokelas">Kokelas</option>
        <option value="Jäsen">Jäsen</option>
        <option value="Kunniajäsen">Kunniajäsen</option>
    </select>

    <button id="toggle-view" data-view="grid">Vaihda taulukkonäkymäksi</button>

    <?php
    if ($users) {
        echo '<div class="user-profiles grid-view">';

        // Separate the current user for priority display
        $current_user_profile = '';
        $other_users_profiles = '';

        foreach ($users as $user) {
            $profile_picture = get_user_meta($user->ID, 'profile_picture', true) ?: get_avatar_url($user->ID, ['size' => 100]);
            $department = get_user_meta($user->ID, 'department', true);
            $titteli = get_user_meta($user->ID, 'titteli', true);
            $biographical_info = get_user_meta($user->ID, 'biographical_info', true);

            // Get the last login timestamp
            $last_login = get_user_meta($user->ID, 'last_login', true);
            //Get first aid and tilannekoulutus:
            $first_aid=get_user_meta($user->ID,'first_aid',true);
            if (!empty($first_aid)) {
                $first_aid = date('d.m.Y', strtotime($first_aid)); 
            }
            $tilanne_koulutus= get_user_meta($user->ID,'tilanne_koulutus',true);
            if (!empty($tilanne_koulutus)) {
                $tilanne_koulutus = date('d.m.Y', strtotime($tilanne_koulutus)); 
            }

            $vip_member =get_user_meta($user->ID,'vip_member',true)==='yes';
            $vip_member_info=get_user_meta($user->ID,'vip_member_info',true);

            // Get visibility settings
            $hide_email = get_user_meta($user->ID, 'hide_email', true) === 'yes';
            $hide_phone_number = get_user_meta($user->ID, 'hide_phone_number', true) === 'yes';
            $custom_user_id = get_user_meta($user->ID, 'custom_user_id', true);

            ob_start();
            ?>
         <div class="user-profile" data-department="<?php echo esc_attr($department); ?>" data-title="<?php echo esc_attr($titteli); ?>">
                <div class="user-avatar">
                    <img src="<?php echo esc_url($profile_picture); ?>" alt="<?php echo esc_attr($user->display_name); ?>'s Profile Picture">
                    <?php if ($vip_member): ?>
                        <span class="vip-crown">&#x1F451;</span>
                        <?php endif; ?>
                </div>
                        <?php if ($last_login) : ?>
    <span class="last-login">Viimeksi kirjautuneena: <?php echo esc_html(date('j F, Y', strtotime($last_login))); ?></span>
<?php else : ?>
    <span class="last-login">Ei sisäänkirjautumistietoja</span>
<?php endif; ?>

            
                <div class="user-details">
                    <h2><?php echo esc_html($user->display_name); ?></h2>
                    <?php if (!empty($vip_member_info)) : ?>
                    <div class="biography">
                        <textarea id="vip_member_info" name="vip_member_info" disabled><?php echo esc_textarea($vip_member_info); ?></textarea>   
                        </div>
                        <?php endif; ?>
                    <p><strong>Nimi:</strong> <?php echo esc_html($user->first_name . ' ' . $user->last_name); ?></p>
                    <?php if(!empty($user->titteli)): ?>
                    <p><strong>Titteli:</strong> <?php echo esc_html($titteli); ?></p>
                    <?php endif; ?>
                    <p><strong>Jäsennumero: </strong> <?php echo esc_html(($custom_user_id)); ?></p>
                   
                    <?php if (!$hide_email &&(!empty($user->user_email))) : ?>
                        <p><strong>Sähköposti:</strong> <?php echo esc_html($user->user_email); ?></p>
                    <?php endif; ?>
                    <?php if (!$hide_phone_number &&(!empty($user->phone_number))) : ?>
                        <p><strong>Puhelinnumero:</strong> <?php echo esc_html(get_user_meta($user->ID, 'phone_number', true)); ?></p>
                    <?php endif; ?>
                    <?php if(!empty($user->department)): ?>
                    <p><strong>Osasto:</strong> <?php echo esc_html($department); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($user->motorcycle)) :?>
                    <p><strong>Moottoripyörä:</strong> <?php echo esc_html($user->motorcycle); ?></p>
                    <?php endif; ?>
                    <?php if(!empty($user->company)): ?>
                    <p><strong>Yritys:</strong> <?php echo esc_html($user->company); ?></p>
                    <?php endif ;?>
                    <?php if (!empty($first_aid) && $first_aid !== '01.01.1970') : ?>
                            <p><strong>Ensiapukoulutus suoritettu: <?php echo esc_attr($first_aid); ?> </strong> 
                            <?php endif; ?>
                        <?php if (!empty($tilanne_koulutus) && $tilanne_koulutus !== '01.01.1970') : ?>
                            <p><strong>Tilannejohtamiskurssi suoritettu: <?php echo esc_attr($tilanne_koulutus); ?> </strong> 
                            <?php endif; ?>
                            <?php if (!empty($biographical_info)): ?>
                                <div class="biography">
                        <textarea id="biographical_info" name="biographical_info" disabled><?php echo esc_textarea($biographical_info); ?></textarea>
                    </div>
            <?php endif; ?>
                    <?php if ($current_user_id === (int) $user->ID) : ?>
                        <p><a href="<?php echo esc_url(get_permalink(get_page_by_path('oma-profiilisivu'))); ?>"class="edit-profile-button">Muokkaa profiilia</a></p>
                    <?php else: ?>
                        <p><a href="<?php echo esc_url(add_query_arg('user', $user->display_name, get_permalink(get_page_by_path('view-profile')))); ?>" class="view-profile-button">Näytä profiili</a></p>
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

        // Table structure for list view
        echo '<table class="user-profiles-table" style="display:none;">';
        echo '<thead><tr><th>Nimi</th><th>Numero</th><th>Puhelinnumero</th><th>Sähköposti</th><th>Yritys</th><th>Ensiapu</th><th>Tilanneturvallisuus</th></tr></thead>';
        echo '<tbody>';
        foreach ($users as $user) {
            $hide_email = get_user_meta($user->ID, 'hide_email', true) === 'yes';
            $hide_phone_number = get_user_meta($user->ID, 'hide_phone_number', true) === 'yes';
            $biographical_info = get_user_meta($user->ID, 'biographical_info', true);
            $first_aid=get_user_meta($user->ID,'first_aid',true);
            if (!empty($first_aid)){
                $first_aid = date('d.m.Y',strtotime($first_aid));
            }
            $tilanne_koulutus= get_user_meta($user->ID,'tilanne_koulutus',true);
            if (!empty($tilanne_koulutus)) {
                $tilanne_koulutus = date('d.m.Y', strtotime($tilanne_koulutus)); 
            }
            ?>
            <tr data-department="<?php echo esc_attr(get_user_meta($user->ID, 'department', true)); ?>" data-title="<?php echo esc_attr(get_user_meta($user->ID,'titteli',true)); ?>">
                <td><?php echo esc_html($user->first_name . ' ' . $user->last_name); ?></td>
                <td><?php echo esc_html(($custom_user_id)); ?></p></td>
                <td><?php echo (!$hide_phone_number) ? esc_html(get_user_meta($user->ID, 'phone_number', true)) : 'Private'; ?></td>
                <td><?php echo (!$hide_email) ? esc_html($user->user_email) : 'Private'; ?></td>
                <td><?php echo esc_html($user->company); ?></td>
                <td><?php if (!empty($first_aid) && $first_aid!== '01.01.1970'): echo $first_aid; endif;?></td>
                <td><?php if (!empty($tilanne_koulutus) && $tilanne_koulutus !== '01.01.1970') : echo $tilanne_koulutus; endif;?> </td>

            </tr>
            <?php
        }
        echo '</tbody></table>';
    } else {
        echo '<p>No user profiles found.</p>';
    }

    // JavaScript for view toggle and department filtering
    ?>
   <!-- JavaScript for view toggle, department filtering, and live search functionality -->
<script>
    document.getElementById('toggle-view').addEventListener('click', function () {
        var userProfileContainer = document.querySelector('.user-profiles');
        var userTableContainer = document.querySelector('.user-profiles-table');
        var currentView = this.getAttribute('data-view');

        if (currentView === 'grid') {
            userProfileContainer.style.display = 'none';
            userTableContainer.style.display = 'table';
            this.setAttribute('data-view', 'table');
            this.textContent = 'Vaihda ruudukkonäkymäksi ';
        } else {
            userProfileContainer.style.display = 'flex';
            userTableContainer.style.display = 'none';
            this.setAttribute('data-view', 'grid');
            this.textContent = 'Vaihda taulukkonäkymäksi';
        }
    });

    document.getElementById('department-filter').addEventListener('change', function () {
    filterProfiles();
});

document.getElementById('titteli-filter').addEventListener('change', function () {
    filterProfiles();
});

function filterProfiles() {
    var selectedDepartment = document.getElementById('department-filter').value;
    var selectedTitle = document.getElementById('titteli-filter').value;
    
    var profiles = document.querySelectorAll('.user-profile');
    var rows = document.querySelectorAll('.user-profiles-table tbody tr');

    profiles.forEach(function(profile) {
        var department = profile.getAttribute('data-department');
        var title = profile.getAttribute('data-title');
        var isVisible = (selectedDepartment === 'all' || department === selectedDepartment) && (selectedTitle === 'all' || title === selectedTitle);
        profile.style.display = isVisible ? 'block' : 'none';
    });

    rows.forEach(function(row) {
        var department = row.getAttribute('data-department');
        var title = row.getAttribute('data-title');
        var isVisible = (selectedDepartment === 'all' || department === selectedDepartment) && (selectedTitle === 'all' || title === selectedTitle);
        row.style.display = isVisible ? '' : 'none';
    });
}


    // Live search functionality
    document.getElementById('search-user-input').addEventListener('input', function () {
        var searchTerm = this.value.toLowerCase();
        var profiles = document.querySelectorAll('.user-profile');
        var rows = document.querySelectorAll('.user-profiles-table tbody tr');

        profiles.forEach(function(profile) {
            var name = profile.querySelector('h2').textContent.toLowerCase();
            profile.style.display = name.includes(searchTerm) ? 'block' : 'none';
        });

        rows.forEach(function(row) {
            var name = row.querySelector('td:nth-child(1)').textContent.toLowerCase() + ' ' + row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            row.style.display = name.includes(searchTerm) ? '' : 'none';
        });
    });

    document.querySelectorAll('.user-profile').forEach(function (profile) {
    if (profile.querySelector('.edit-profile-button')) {
        profile.classList.add('current-user');
    }
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

        a {
            text-decoration: none;
        }
        .user-profile {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            background-color: #f9f9f9;
            max-width: 300px;
            flex: 1 1 calc(33% - 40px);
            box-shadow: 0.1rem 0.2rem 5px #5a6142;
        }

        .user-avatar {
            margin-bottom: 15px;
            text-align: center;
            position: relative;
            display: inline-block;
        }
        .user-avatar img {
            border-radius: 50%;
            width: 100px;
            height: 100px;
            object-fit: cover;
            box-shadow: 0.1rem 0.2rem 3px #5a6142;

        }
        .user-profile h2 {
            font-size: 1.25em;
            margin: 0 0 10px;
            font-family: 'EB Garamond', serif;

        }
        .user-profile p {
            margin: 5px 0;
            font-family: 'Montserrat', sans-serif;

        }
        .view-profile-button a {
            display: inline-block;
            padding: 0px 20px;
            color: #1F2518;
            width: 12rem;
            font-weight: bold;
            background-color: #E2C275;
            border-radius: 10px;
            text-decoration: none;
            box-shadow: 0.1rem 0.2rem 3px #5a6142;
        }
        .view-profile-button a:hover {
            background-color: #1F2518;
            color: #e2c275;
        }
        .edit-profile-button {
           display: inline-block;
            padding: 0px 20px;
            color: #1F2518;
            width: 12rem;
            font-weight: bold;
            background-color: #E2C275;
            border-radius: 10px;
            text-decoration: none;
            box-shadow: 0.1rem 0.2rem 3px #5a6142;
        }
        .edit-profile-button:hover {
            background-color: #1F2518;
            color: #e2c275;
        }
 /* Highlight the user's own profile */
    .user-profile.current-user {
        background-color: #e2c275; /* Light yellow background */
        border: 2px solid #e2c274; /* Distinct border color */
    }
        /* Styles for table view */
        .user-profiles-table {
            width: 100%;
            border-collapse: collapse;
        }
        .user-profiles-table th, .user-profiles-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
.user-profile .vip-crown {
            position: absolute;
            top: -15px;
            right: 0px;
            font-size: 24px;
            color: gold;
        }
                .biography textarea {
        resize: both;
        min-height: 80px;
        overflow: auto; 
        max-width:100%;
        box-sizing:border-box;
    }
        .last-login {
            display: block;
            margin-top: 10px;
            font-size: 14px;
            color: #555;
            font-style: italic;
        }


</style>
    ";
}
add_action('wp_head', 'display_user_profiles_styles');
?>