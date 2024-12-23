<?php
/*
Plugin Name: Display All User Profiles
Description: Tämä koodi hallinnoi "kaikki jäsenet"- sivua.
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
    <form id="search-form" action="" method="get">
    <input type="hidden" name="page_id" value="<?php echo get_the_ID(); ?>" />
    <input type="text" name="search_user" id="search-user-input" placeholder="Hae profiili..." value="<?php echo esc_attr($search_query); ?>" />
    <button type="submit"><span class="search-icon">&#x1F50D;</span>Hae</button>
    <button id="reset">Näytä kaikki jäsenet</button>
    </form>

    <label for="department">Valitse alue:</label>
    <select id="department-filter">
        <option value="all">Kaikki alueet</option>
        <option value="Pirkanmaa">Pirkanmaa</option>
        <option value="Pohjanmaa">Pohjanmaa</option>
         <option value="Päijät-Häme&Kaakkois-Suomi">Päijät-Häme&Kaakkois-Suomi</option>
        <option value="Uusimaa">Uusimaa</option>
        <option value="Varsinais-Suomi">Varsinais-Suomi</option>
    </select>
    </br>
 <label for="titteli">Valitse titteli:</label>
<select id="titteli-filter">
    <option value="all">Kaikki tittelit</option>
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
            $roles = $user->roles;
            // Fetch the 'show_deactivated_member' meta value
            $show_deactivated_member = get_user_meta($user->ID, 'show_deactivated_member', true) === 'yes';
        
            // Check if the user has the 'deactivated' role and the 'show_deactivated_member' meta is not checked
            if (in_array('deactivated', $roles) && !$show_deactivated_member) {
                continue; // Skip this user if 'deactivated' role exists and 'show_deactivated_member' is not 'yes'
            } 
            $profile_picture = get_user_meta($user->ID, 'profile_picture', true) ?: get_avatar_url($user->ID, ['size' => 100]);
            $department = get_user_meta($user->ID, 'department', true);
            $titteli = get_user_meta($user->ID, 'titteli', true);
            $biographical_info = get_user_meta($user->ID, 'biographical_info', true);
            $user_email= ($user->user_email);
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

            $vip_member_icon =get_user_meta($user->ID,'vip_member_icon',true)==='yes';
            $vip_member_info=get_user_meta($user->ID,'vip_member_info',true);
            $cross_icon=get_user_meta($user->ID,'cross_icon',true)==='yes';

            $phone_number=get_user_meta($user->ID,'phone_number',true);
            // Get visibility settings
            $hide_email = get_user_meta($user->ID, 'hide_email', true) === 'yes';
            $hide_phone_number = get_user_meta($user->ID, 'hide_phone_number', true) === 'yes';
            $custom_user_id = get_user_meta($user->ID, 'custom_user_id', true);

            $kunniajasen= get_user_meta($user->ID,'titteli',true)==='Kunniajäsen';
            $honorary_number =get_user_meta($user->ID,'honorary_number',true);
            $appointed_date= get_user_meta($user->ID,'appointed_date',true);
            

            ob_start();
            ?>
            <div class="user-profile" data-department="<?php echo esc_attr($department); ?>" data-title="<?php echo esc_attr($titteli); ?>">
                <div class="user-avatar">
                    <img src="<?php echo esc_url($profile_picture); ?>" alt="<?php echo esc_attr($user->display_name); ?>'s Profile Picture">
                    <?php if ($vip_member_icon): ?>
                        <span class="vip-crown">&#x1F451;</span>
                    <?php endif; ?>
                    <?php if ($cross_icon): ?>
                        <span class="cross">&#x271D;</span>
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
                    <img src="<?php echo plugin_dir_url( __DIR__ ); ?>includes/images/exe-logo-nobackround.png" alt="logo" class="biography-logo">
                        <textarea id="vip_member_info" name="vip_member_info" disabled><?php echo esc_textarea($vip_member_info); ?></textarea>   
                        </div>
                        <?php endif; ?>
                    <p><strong>Nimi:</strong> <?php echo esc_html($user->first_name . ' ' . $user->last_name); ?></p>
                    <?php if(!empty($user->titteli)): ?>
                    <p><strong>Titteli:</strong> <?php echo esc_html($titteli); ?></p>
                    <?php endif; ?>
                    <p><strong>Jäsennumero: </strong> <?php echo esc_html(($custom_user_id)); ?></p>
                    <?php if ($kunniajasen): ?>
                    <p><strong>Kunniajäsennumero:</strong> <?php echo esc_attr($honorary_number);?></p>
                    <?php endif; ?>
                    <?php if (!empty($appointed_date) && $appointed_date !== '1970') : ?>
                    <p><strong> Nimitetty kunniajäseneksi: <?php echo esc_attr($appointed_date); ?> </strong> 
                    <?php endif; ?>
                    <?php if (!$hide_email &&(!empty($user_email))) : ?>
                    <p><strong>Sähköposti:</strong> <?php echo esc_html($user->user_email); ?></p>
                    <?php endif; ?>
                    <?php if (!$hide_phone_number &&(!empty($user->phone_number))) : ?>
                    <p><strong>Puhelinnumero:</strong> <?php echo esc_html($phone_number); ?></p>
                    <?php endif; ?>
                    <?php if(!empty($user->department)): ?>
                    <p><strong>Alue:</strong> <?php echo esc_html($department); ?></p>
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
                        <p><a href="<?php echo esc_url(get_permalink(get_page_by_path('oma-profiilisivu'))); ?>"class="edit-profile-button">Muokkaa profiiliasi</a></p>
                    <?php else: ?>
                        <p><a href="<?php echo esc_url(add_query_arg('user', $user->user_login, get_permalink(get_page_by_path('view-profile')))); ?>" class="view-profile-button">Näytä profiili</a></p>
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
        echo '<div id="user-table">';
        echo '<table class="user-profiles-table" style="display:none;">';
        
        echo '<thead>
        <tr>
            <th data-sortable="true" onclick="sortTable(0)">Nimi</th>
            <th data-sortable="true" onclick="sortTable(1)">Numero</th>
            <th data-sortable="true" onclick="sortTable(2)">Puhelinnumero</th>
            <th data-sortable="true" onclick="sortTable(3)">Sähköposti</th>
            <th data-sortable="true" onclick="sortTable(4)">Yritys</th>
            <th data-sortable="true" onclick="sortTable(5)">EA-koulutus</th>
            <th data-sortable="true" onclick="sortTable(6)">TT-koulutus</th>
        </tr>
    </thead>';
        echo '<tbody>';
        foreach ($users as $user) {
            $roles = $user->roles;
            // Fetch the 'show_deactivated_member' meta value
            $show_deactivated_member = get_user_meta($user->ID, 'show_deactivated_member', true) === 'yes';
        
            // Check if the user has the 'deactivated' role and the 'show_deactivated_member' meta is not checked
            if (in_array('deactivated', $roles) && !$show_deactivated_member) {
                continue; // Skip this user if 'deactivated' role exists and 'show_deactivated_member' is not 'yes'
            } 
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
            $custom_user_id = get_user_meta($user->ID, 'custom_user_id', true);
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
        echo '</div>';
        ?>
    <?php
        //list for mobile
        echo '<div id="user-list" class="userListContainer" style="display:none;">'; 
       foreach ($users as $user) {
        $roles = $user->roles;
        // Fetch the 'show_deactivated_member' meta value
        $show_deactivated_member = get_user_meta($user->ID, 'show_deactivated_member', true) === 'yes';
    
        // Check if the user has the 'deactivated' role and the 'show_deactivated_member' meta is not checked
        if (in_array('deactivated', $roles) && !$show_deactivated_member) {
            continue; // Skip this user if 'deactivated' role exists and 'show_deactivated_member' is not 'yes'
        } 
        $hide_email = get_user_meta($user->ID, 'hide_email', true) === 'yes';
        $hide_phone_number = get_user_meta($user->ID, 'hide_phone_number', true) === 'yes';
        $custom_user_id = get_user_meta($user->ID, 'custom_user_id', true);
    ?>
        <ul id="<?php echo esc_html($user->first_name . $user->last_name); ?>" class="user-info-list" data-department="<?php echo esc_attr(get_user_meta($user->ID, 'department', true)); ?>" data-title="<?php echo esc_attr(get_user_meta($user->ID,'titteli',true)); ?>">
        <li class="list-name">
                <span class="label">Nimi:</span>
                <span class="value"><?php echo esc_html($user->first_name . ' ' . $user->last_name); ?></span>
            </li>
            <li>
                <span class="label">Numero:</span>
                <span class="value"><?php echo esc_html(($custom_user_id)); ?></span>
            </li>   
            <li>
                <span class="label">Puhelinnumero:</span>
                <span class="value"><?php echo (!$hide_phone_number) ? esc_html(get_user_meta($user->ID, 'phone_number', true)) : 'Private'; ?></span>
            </li>
            <li>
                <span class="label">Sähköposti:</span>
                <span class="value"><?php echo (!$hide_email) ? esc_html($user->user_email) : 'Private'; ?></span>
            </li>
       </ul>
        
        <?php
    }
       
       echo '</div>';


    } else {
        echo '<p>No user profiles found.</p>';
    }
   

    // JavaScript for view toggle and department filtering
    ?>
   <!-- JavaScript for view toggle, department filtering, and live search functionality -->
<script>

    // Reset form
document.getElementById('reset').addEventListener('click', function () {
    document.getElementById("search-user-input").value = "";
});

document.getElementById('toggle-view').addEventListener('click', function () {
    var userProfileContainer = document.querySelector('.user-profiles'); // Grid view
    var userTableContainer = document.querySelector('.user-profiles-table'); // Regular table view
    var userListContainer = document.querySelector('#user-list'); // List view for mobile
    var currentView = this.getAttribute('data-view');
    var width = window.matchMedia("(min-width: 920px)"); // Media query

    if (currentView === 'grid') {
        // Switch to table view
        userProfileContainer.style.display = 'none';

        if (width.matches) {
            // Wide screen
            userTableContainer.style.display = 'table';
            userListContainer.style.display = 'none';
        } else {
            // Narrow screen
            userTableContainer.style.display = 'none';
            userListContainer.style.display = 'block';
        }

        this.setAttribute('data-view', 'table');
        this.textContent = 'Vaihda ruudukkonäkymäksi';
    } else {
        // Switch to grid view
        userProfileContainer.style.display = 'flex';
        userTableContainer.style.display = 'none';
        userListContainer.style.display = 'none';

        this.setAttribute('data-view', 'grid');
        this.textContent = 'Vaihda taulukkonäkymäksi';
    }
});

document.getElementById('department-filter').addEventListener('change', filterProfiles);
document.getElementById('titteli-filter').addEventListener('change', filterProfiles);

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
list.forEach(function(list) {
        var department = list.getAttribute('data-department');
        var title = list.getAttribute('data-title');
        var isVisible = (selectedDepartment === 'all' || department === selectedDepartment) && (selectedTitle === 'all' || title === selectedTitle);
        list.style.display = isVisible ? '' : 'none';
    });
    // Live search functionality
    document.getElementById('search-user-input').addEventListener('input', function () {
        var searchTerm = this.value.toLowerCase();
        var profiles = document.querySelectorAll('.user-profile');
        var rows = document.querySelectorAll('.user-profiles-table tbody tr');
        var list = document.querySelectorAll('.user-info-list');

        profiles.forEach(function(profile) {
            var name = profile.querySelector('h2').textContent.toLowerCase();
            profile.style.display = name.includes(searchTerm) ? 'block' : 'none';
        });

        rows.forEach(function(row) {
            var name = row.querySelector('td:nth-child(1)').textContent.toLowerCase() + ' ' + row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            row.style.display = name.includes(searchTerm) ? '' : 'none';
        });
        list.forEach(function(list) {
            var userUl = list.id.toLowerCase();
            list.style.display = userUl.includes(searchTerm) ? '' : 'none';
        });
    });

    document.querySelectorAll('.user-profile').forEach(function (profile) {
    if (profile.querySelector('.edit-profile-button')) {
        profile.classList.add('current-user');
    }
});
function sortTable(columnIndex) {
    var table = document.querySelector('.user-profiles-table');
    var rows = Array.from(table.querySelectorAll('tbody tr'));
    var isAscending = table.getAttribute('data-sort-order') === 'asc';
    
    // Reset sorting indicators on all headers
    table.querySelectorAll('th').forEach(function (th) {
        th.classList.remove('sort-asc', 'sort-desc');
    });

    // Apply the active class to the clicked header
    var activeHeader = table.querySelectorAll('th')[columnIndex];
    activeHeader.classList.add(isAscending ? 'sort-asc' : 'sort-desc');

    rows.sort(function (rowA, rowB) {
        var cellA = rowA.querySelectorAll('td')[columnIndex].textContent.trim().toLowerCase();
        var cellB = rowB.querySelectorAll('td')[columnIndex].textContent.trim().toLowerCase();

        if (!isNaN(Date.parse(cellA)) && !isNaN(Date.parse(cellB))) {
            return isAscending
                ? new Date(cellA) - new Date(cellB)
                : new Date(cellB) - new Date(cellA);
        }

        if (!isNaN(cellA) && !isNaN(cellB)) {
            return isAscending ? cellA - cellB : cellB - cellA;
        }

        return isAscending ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
    });

    var tbody = table.querySelector('tbody');
    tbody.innerHTML = '';
    rows.forEach(function (row) {
        tbody.appendChild(row);
    });

    table.setAttribute('data-sort-order', isAscending ? 'desc' : 'asc');
}
</script>
<style>
/* Add a pointer cursor to indicate interactivity */
.user-profiles-table th[data-sortable="true"] {
    cursor: pointer;
    background-color: #f8f9fa; /* Light background to differentiate */
    position: relative; /* For adding sort icons */
}

/* Highlight column headers on hover */
.user-profiles-table th[data-sortable="true"]:hover {
    background-color: #e2e6ea; /* Slightly darker shade */
    color: #007bff; /* Change text color for emphasis */
}

/* Sort icons */
.user-profiles-table th[data-sortable="true"]::after {
    content: '▲▼'; /* Placeholder sort arrows */
    font-size: 0.8em;
    position: absolute;
    color: #6c757d;
    right: 10px; /* Space between text and icon */
    opacity: 0.5;
}

/* Active sort (ascending) */
.user-profiles-table th[data-sortable="true"].sort-asc::after {
    content: '▲'; /* Show only ascending arrow */
    opacity: 1;
    color: #007bff; /* Highlight active sort */
}

/* Active sort (descending) */
.user-profiles-table th[data-sortable="true"].sort-desc::after {
    content: '▼'; /* Show only descending arrow */
    opacity: 1;
    color: #007bff; /* Highlight active sort */
}
</style>
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
        margin: 20px;
    }

    a {
        text-decoration: none;
    }
    
    p {
        font-size: 12px;
    }
    
    .user-profile {
        border-radius: 8px;
        padding: 20px;
        background-color: #f9f9f9;
        max-width: 300px;
        flex: 1 1 calc(33% - 40px);
        box-shadow: 0.1rem 0.2rem 5px #5a6142;
        background-image: url('wp-content/plugins/display-user-info/includes/images/4.jpg');
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
        font-family: 'Montserrat', serif;
    }

    .view-profile-button a {
        display: inline-block;
        padding: 5px 20px;
        color: #1F2518;
        width: 12rem;
        font-weight: bold;
        background-color: #E2C275;
        border-radius: 8px;
        text-decoration: none;
        box-shadow: 0.1rem 0.2rem 3px #5a6142;
    }
    
    .view-profile-button a:hover {
        background-color: #1F2518;
        color: #e2c275;
    }
        
    .edit-profile-button {
        display: inline-block;
        padding: 5px 20px;
        color: #1F2518;
        width: 12rem;
        font-weight: bold;
        background-color: #E2C275;
        border-radius: 8px;
        text-decoration: none;
        box-shadow: 0.1rem 0.2rem 3px #5a6142;
    }

    .edit-profile-button:hover {
        background-color: #1F2518;
        color: #e2c275;
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

    .user-profile .vip-crown{
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
        padding: 5px;
        box-sizing:border-box;
    }
    .last-login {
        display: block;
        margin-top: 10px;
        font-size: 12px;
        color: #555;
        font-style: italic;
    }
            
 /* Highlight the user's own profile */

    .user-profile.current-user {
    position: relative;
    box-shadow: 0.1rem 0.2rem 5px #5a6142;
    border: solid 2px #5a6142;
    background-image: 
    linear-gradient(rgba(255, 255, 255, 1.00), rgba(255, 255, 255, 0.5)), 
    url('wp-content/plugins/display-user-info/includes/images/6.jpg');
    background-size: cover;
    background-position: center;
}
    
    .user-profile.user-profile.current-user p {
        color: #1F2518;
        font-weight: bold;
    }

    #search-user-input {
        width: 30%; 
        padding: 5px;
        margin-bottom: 10px;
        margin-right: 10px; 
        box-shadow: 0.1rem 0.2rem 3px #5a6142;
        border: none;
        border-radius: 8px;
        font-family: 'Montserrat', serif;
    }

    button {
        padding: 5px 20px;
        background-color: #e2c275;
        color: #1F2518;
        border-radius: 8px;
        font-weight: bold;
        font-size: 0.75rem;
        border: none;
        cursor: pointer;
        font-family: 'Montserrat', serif;
        box-shadow: 0.1rem 0.2rem 3px #5a6142;
    }

    button:hover {
        background-color: #1F2518;
        color: #e2c275;
    }

    #toggle-view {
        margin-right: 10px; 
        float: right; 
    }

    label[for='department'], label[for='titteli']  {
        display: inline-block;
        width: 120px; 
        font-size: 14px;
    }

    select#titteli-filter, select#department-filter {
        padding: 5px;
        box-shadow: 0.1rem 0.2rem 3px #5a6142;
        border-radius: 8px;
        border: none;
        margin-bottom: 10px;
        width: 120px; 
        font-family: 'Montserrat', serif;
    }

/* Highlight info field for VIP */

    .biography #vip_member_info {
        border-left: 4px solid white;  
        border-right: 4px solid white; 
        border-bottom: 4px solid white; 
        border-top: none;  
        border-bottom-left-radius: 5px;
        border-bottom-right-radius: 5px;
        background-color: black;
        color: #e2c275;
        margin-top: -40px;
        box-shadow: inset 0 -1px 0 0 black; 
    }

    .biography-logo {
        width: 100%; 
        height: 100%; 
        object-fit: contain;  
        display: block;
    } 
        .cross {
    font-size: 1.5em; 
    color: #000000;
    margin-left: 5px;
    vertical-align: middle;
}

@media screen and (max-width:1150px) and (min-width: 920px){
        #content {
            width: 100%;   
        }
    }
    @media screen and (max-width: 475px) {
        #content {
            width: 100%;                 
        }
    }
     
/* Style for list view */
    .user-info-list {
        margin-top: 35px;
        font-size: 0.85rem;
        list-style-type: none;
        border-bottom: 1px solid black;
    }
    .user-info-list li {
        display: flex;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    .user-info-list li:nth-child(odd){
        background-color: #ffffff;
    }
    .user-info-list li:nth-child(even){
        background-color: #f0f0e8;
    }
    .user-info-list li .label {
        flex-basis: 40%;
        margin-right: 10px;
    }
    .user-info-list li .value {
        flex-basis: 60%;
    }
            
}
         
</style>
    ";
}
add_action('wp_head', 'display_user_profiles_styles');
?>