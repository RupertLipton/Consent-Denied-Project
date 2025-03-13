<?php
if (!defined('ABSPATH')) {
    exit;
}

function cd_operators_page() {
    // Start output buffering
    ob_start();
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'cd_parking_operators';
    $message = '';
    
    // Handle form submission
    if (isset($_POST['submit_operator'])) {
        $company_name = sanitize_text_field($_POST['company_name']);
        $email = sanitize_email($_POST['email']);
        
        if ($company_name && $email) {
            $result = $wpdb->insert(
                $table_name,
                array(
                    'company_name' => $company_name,
                    'email' => $email,
                    'active' => 1
                ),
                array('%s', '%s', '%d')
            );
            
            if ($result) {
                $message = '<div class="notice notice-success"><p>Operator added successfully!</p></div>';
            } else {
                $message = '<div class="notice notice-error"><p>Error adding operator. Email might be duplicate.</p></div>';
            }
        }
    }
    
    // Get existing operators
    $operators = $wpdb->get_results("SELECT * FROM $table_name ORDER BY company_name");
    
    ?>
    <div class="wrap">
        <h1>Parking Operators</h1>
        
        <?php echo $message; ?>
        
        <div class="cd-add-operator" style="background: #fff; padding: 20px; margin: 20px 0; border: 1px solid #ccd0d4; border-radius: 4px;">
            <h2>Add New Operator</h2>
            <form method="post" action="">
                <table class="form-table">
                    <tr>
                        <th><label for="company_name">Company Name</label></th>
                        <td>
                            <input type="text" name="company_name" id="company_name" class="regular-text" required>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="email">Email Address</label></th>
                        <td>
                            <input type="email" name="email" id="email" class="regular-text" required>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="submit_operator" class="button button-primary" value="Add Operator">
                </p>
            </form>
        </div>
        
        <div class="cd-operators-list">
            <h2>Existing Operators</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Company Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Date Added</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($operators as $operator): ?>
                    <tr>
                        <td><?php echo esc_html($operator->company_name); ?></td>
                        <td><?php echo esc_html($operator->email); ?></td>
                        <td><?php echo $operator->active ? 'Active' : 'Inactive'; ?></td>
                        <td><?php echo esc_html($operator->date_added); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php do_action('cd_after_operators_list'); ?>
    </div>
    <?php
    
    // Clean up output buffer and return content
    $content = ob_get_clean();
    echo $content;
}

// Add test button to operators page
function cd_add_test_button() {
    if (isset($_POST['test_email'])) {
        $manager = cd_get_email_manager();
        $user_id = get_current_user_id();
        
        // Create test vehicle if needed
        global $wpdb;
        $vehicle_id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}cd_vehicles WHERE user_id = $user_id LIMIT 1");
        
        if (!$vehicle_id) {
            $wpdb->insert(
                $wpdb->prefix . 'cd_vehicles',
                array(
                    'user_id' => $user_id,
                    'registration_number' => 'TEST123',
                    'make' => 'Test Make',
                    'model' => 'Test Model',
                    'color' => 'Test Color'
                )
            );
            $vehicle_id = $wpdb->insert_id;
        }
        
        $sent = $manager->send_notices_to_all_operators($user_id, $vehicle_id);
        echo "<div class='notice notice-success'><p>Test emails sent to $sent operators.</p></div>";
    }
    ?>
    <div class="cd-test-email" style="margin-top: 20px; background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px;">
        <h2>Test Email System</h2>
        <p>Use this button to send a test email to all active operators.</p>
        <form method="post">
            <input type="submit" name="test_email" class="button button-primary" 
                   value="Send Test Email to All Operators">
        </form>
    </div>
    <?php
}
add_action('cd_after_operators_list', 'cd_add_test_button');