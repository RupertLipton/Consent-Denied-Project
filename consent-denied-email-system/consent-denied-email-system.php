<?php
/*
Plugin Name: Consent Denied Email System
Description: Handles automated emails to parking operators
Version: 1.0
Author: Your Name
*/

if (!defined('ABSPATH')) {
    exit;
}

// Load required files
require_once plugin_dir_path(__FILE__) . 'includes/admin/vehicles.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/operators.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-vehicle-manager.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-email-manager.php';
require_once plugin_dir_path(__FILE__) . 'includes/frontend/shortcodes.php';

function cd_create_my_vehicles_page() {
    // Check if the page already exists using WP_Query
    $query = new WP_Query(
        array(
            'post_type' => 'page',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'title' => 'My Vehicles'
        )
    );
    
    // If the page doesn't exist, create it
    if (!$query->have_posts()) {
        $page_data = array(
            'post_title'    => 'My Vehicles',
            'post_content'  => '[cd_vehicle_management]',
            'post_status'   => 'publish',
            'post_type'     => 'page'
        );
        
        // Insert the page into the database
        $page_id = wp_insert_post($page_data);
        
        // Store the page ID in options for future reference
        update_option('cd_my_vehicles_page_id', $page_id);
    } else {
        // Get the existing page ID
        $page = $query->posts[0];
        update_option('cd_my_vehicles_page_id', $page->ID);
    }
}

function cd_modify_navigation_block($block_content, $block) {
    if ($block['blockName'] !== 'core/navigation') {
        return $block_content;
    }

    $page_id = get_option('cd_my_vehicles_page_id');
    if (!$page_id) {
        return $block_content;
    }

    $url = get_permalink($page_id);
    
    // Create new menu item HTML
    $new_item = sprintf(
        '<li class="wp-block-navigation-item has-link is-menu-item"><a class="wp-block-navigation-item__content" href="%s"><span class="wp-block-navigation-item__label">My Vehicles</span></a></li>',
        esc_url($url)
    );

    // Insert after "My account"
    $pattern = '/(<li[^>]*>.*?My account.*?<\/li>)/is';
    return preg_replace($pattern, '$1' . $new_item, $block_content, 1);
}

// Register activation hooks
register_activation_hook(__FILE__, 'cd_create_my_vehicles_page');
register_activation_hook(__FILE__, 'cd_create_tables');

// Add navigation block filter
add_filter('render_block', 'cd_modify_navigation_block', 10, 2);

// Create database tables on plugin activation
function cd_create_tables() {
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
    $charset_collate = $wpdb->get_charset_collate();

    // Parking operators table
    $sql_operators = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cd_parking_operators (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        company_name varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        active tinyint(1) DEFAULT 1,
        date_added datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY email (email)
    ) $charset_collate;";

    // Email logs table
    $sql_logs = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cd_email_logs (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        operator_id bigint(20) NOT NULL,
        vehicle_id bigint(20) NOT NULL,
        email_type varchar(50) NOT NULL,
        reference_number varchar(50) NOT NULL,
        sent_date datetime DEFAULT CURRENT_TIMESTAMP,
        status varchar(20) DEFAULT 'sent',
        email_content text,
        PRIMARY KEY (id)
    ) $charset_collate;";

    // Vehicles table
    $sql_vehicles = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cd_vehicles (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        registration_number varchar(20) NOT NULL,
        make varchar(100) NOT NULL,
        model varchar(100) NOT NULL,
        color varchar(50) NOT NULL,
        active tinyint(1) DEFAULT 1,
        date_added datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY user_registration (user_id, registration_number)
    ) $charset_collate;";

    // Vehicle changes tracking table
    $sql_changes = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cd_vehicle_changes (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        vehicle_id bigint(20) NOT NULL,
        user_id bigint(20) NOT NULL,
        field_name varchar(50) NOT NULL,
        old_value varchar(255) NOT NULL,
        new_value varchar(255) NOT NULL,
        change_date datetime DEFAULT CURRENT_TIMESTAMP,
        email_sent tinyint(1) DEFAULT 0,
        PRIMARY KEY (id),
        KEY vehicle_id (vehicle_id),
        KEY user_id (user_id)
    ) $charset_collate;";

    dbDelta($sql_operators);
    dbDelta($sql_logs);
    dbDelta($sql_vehicles);
    dbDelta($sql_changes);
}

// Add admin menu
add_action('admin_menu', function() {
    add_menu_page(
        'Consent Denied',
        'Consent Denied',
        'manage_options',
        'consent-denied',
        'cd_main_page',
        'dashicons-shield',
        2
    );

    // Add submenus
    add_submenu_page(
        'consent-denied',
        'Parking Operators',
        'Operators',
        'manage_options',
        'consent-denied-operators',
        'cd_operators_page'
    );

    add_submenu_page(
        'consent-denied',
        'Vehicle Management',
        'Vehicles',
        'manage_options',
        'consent-denied-vehicles',
        'cd_vehicles_page'
    );

    add_submenu_page(
        'consent-denied',
        'Email Logs',
        'Email Logs',
        'manage_options',
        'consent-denied-logs',
        'cd_logs_page'
    );

    add_submenu_page(
        'consent-denied',
        'Settings',
        'Settings',
        'manage_options',
        'consent-denied-settings',
        'cd_settings_page'
    );
});

// Main page display
function cd_main_page() {
    ?>
    <div class="wrap">
        <h1>Consent Denied System</h1>
        
        <h2>System Status</h2>
        <?php
        global $wpdb;
        $tables = [
            $wpdb->prefix . 'cd_parking_operators',
            $wpdb->prefix . 'cd_email_logs',
            $wpdb->prefix . 'cd_vehicles'
        ];
        
        echo '<ul class="cd-status-list" style="padding: 20px; background: #fff; border: 1px solid #ccd0d4; border-radius: 4px;">';
        foreach ($tables as $table) {
            $exists = $wpdb->get_var("SHOW TABLES LIKE '$table'") === $table;
            echo "<li style='margin-bottom: 10px;'>";
            echo "<strong>$table:</strong> ";
            echo $exists 
                ? '<span style="color: #46b450;">✓ Created</span>' 
                : '<span style="color: #dc3232;">✗ Missing</span>';
            echo "</li>";
        }
        echo '</ul>';
        ?>
    </div>
    <?php
}

// Logs page display
function cd_logs_page() {
    ?>
    <div class="wrap">
        <h1>Email Logs</h1>
        <?php
        global $wpdb;
        $logs = $wpdb->get_results("
            SELECT l.*, o.company_name, o.email
            FROM {$wpdb->prefix}cd_email_logs l
            JOIN {$wpdb->prefix}cd_parking_operators o ON l.operator_id = o.id
            ORDER BY l.sent_date DESC
            LIMIT 100
        ");
        ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Reference</th>
                    <th>Operator</th>
                    <th>Email</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($logs): ?>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?php echo esc_html($log->sent_date); ?></td>
                        <td><?php echo esc_html($log->reference_number); ?></td>
                        <td><?php echo esc_html($log->company_name); ?></td>
                        <td><?php echo esc_html($log->email); ?></td>
                        <td><?php echo esc_html($log->status); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No email logs found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}

// Add styles
add_action('admin_head', function() {
    echo '<style>
        .cd-status-list li { margin-bottom: 10px; }
        .cd-add-operator { max-width: 800px; }
    </style>';
});

// Handle output buffering cleanup
add_action('admin_init', function() {
    while (ob_get_level()) {
        ob_end_clean();
    }
});