<?php
if (!defined('ABSPATH')) {
    exit;
}

class CD_Email_Manager {
    private $wpdb;
    
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }
    
    private function generate_reference() {
        return 'CD' . date('Ym') . mt_rand(1000, 9999);
    }
    
    public function send_notice($user_id, $vehicle_id, $operator_id) {
        $user = get_userdata($user_id);
        $vehicle = $this->wpdb->get_row($this->wpdb->prepare(
            "SELECT * FROM {$this->wpdb->prefix}cd_vehicles WHERE id = %d",
            $vehicle_id
        ));
        $operator = $this->wpdb->get_row($this->wpdb->prepare(
            "SELECT * FROM {$this->wpdb->prefix}cd_parking_operators WHERE id = %d",
            $operator_id
        ));
        
        if (!$user || !$vehicle || !$operator) {
            return false;
        }
        
        $address = array(
            get_user_meta($user_id, 'address_street', true),
            get_user_meta($user_id, 'address_line2', true),
            get_user_meta($user_id, 'address_city', true),
            get_user_meta($user_id, 'address_postcode', true)
        );
        $formatted_address = implode(", ", array_filter($address));
        
        $mobile_number = get_user_meta($user_id, 'billing_phone', true);
        
        $reference = $this->generate_reference();
        
        $template = file_get_contents(plugin_dir_path(dirname(__FILE__)) . 'templates/admin/operator-notice.php');
        
        $replacements = array(
            '{Vehicle_Registration}' => $vehicle->registration_number,
            '{Vehicle_Make}' => $vehicle->make,
            '{Vehicle_Model}' => $vehicle->model,
            '{Vehicle_Color}' => $vehicle->color,
            '{Customer_Name}' => $user->display_name,
            '{Customer_Address}' => $formatted_address,
            '{Customer_Mobile}' => $mobile_number,
            '{Operator_Name}' => $operator->company_name,
            '{Reference_Number}' => $reference,
            '{Current_Date}' => date('d/m/Y')
        );
        
        $email_content = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $template
        );
        
        $headers = array('Content-Type: text/html; charset=UTF-8');
        $subject = "Formal Notice: Alternative Parking Terms - {$vehicle->registration_number}";
        
        $sent = wp_mail($operator->email, $subject, $email_content, $headers);
        
        if ($sent) {
            $this->wpdb->insert(
                $this->wpdb->prefix . 'cd_email_logs',
                array(
                    'user_id' => $user_id,
                    'operator_id' => $operator_id,
                    'vehicle_id' => $vehicle_id,
                    'reference_number' => $reference,
                    'email_content' => $email_content,
                    'email_type' => 'notice',
                    'status' => 'sent'
                )
            );
        }
        
        return $sent;
    }
    
    public function send_notices_to_all_operators($user_id, $vehicle_id) {
        $operators = $this->wpdb->get_results(
            "SELECT id FROM {$this->wpdb->prefix}cd_parking_operators WHERE active = 1"
        );
        
        $success_count = 0;
        foreach ($operators as $operator) {
            if ($this->send_notice($user_id, $vehicle_id, $operator->id)) {
                $success_count++;
            }
        }
        
        return $success_count;
    }
}

// Initialize email manager
function cd_get_email_manager() {
    static $manager = null;
    if ($manager === null) {
        $manager = new CD_Email_Manager();
    }
    return $manager;
}

// Add a hook to handle Contact Form 7 submissions
add_action('wpcf7_mail_sent', 'cd_handle_contact_form_submission');

function cd_handle_contact_form_submission($contact_form) {
    // Get form submission data
    $submission = WPCF7_Submission::get_instance();
    if (!$submission) {
        return;
    }
    
    $posted_data = $submission->get_posted_data();
    
    // Check if we have the essential fields
    if (empty($posted_data['user_email']) || empty($posted_data['registration_number'])) {
        return;
    }

    // NEW: Sanitize and validate address fields
    $address_fields = [
        'address_street' => sanitize_text_field($posted_data['address_street'] ?? ''),
        'address_line2' => sanitize_text_field($posted_data['address_line2'] ?? ''),
        'address_city' => sanitize_text_field($posted_data['address_city'] ?? ''),
        'address_postcode' => sanitize_text_field($posted_data['address_postcode'] ?? '')
    ];

    // Validation logic
    $validation_errors = [];
    if (empty($address_fields['address_street'])) {
        $validation_errors[] = 'Street address is required';
    }
    if (empty($address_fields['address_city'])) {
        $validation_errors[] = 'City is required';
    }
    if (empty($address_fields['address_postcode'])) {
        $validation_errors[] = 'Postcode is required';
    }

    // UK Postcode validation (optional but recommended)
    if (!empty($address_fields['address_postcode'])) {
        $postcode_pattern = '/^[A-Z]{1,2}[0-9][0-9A-Z]?\s?[0-9][A-Z]{2}$/i';
        if (!preg_match($postcode_pattern, $address_fields['address_postcode'])) {
            $validation_errors[] = 'Invalid postcode format';
        }
    }

    // If validation fails, you might want to handle this 
    // (e.g., return an error, log it, or prevent submission)
    if (!empty($validation_errors)) {
        error_log('Address Validation Errors: ' . implode(', ', $validation_errors));
        return;
    }
    
    $user_email = sanitize_email($posted_data['user_email']);
    $first_name = !empty($posted_data['billing_first_name']) ? sanitize_text_field($posted_data['billing_first_name']) : '';
    $last_name = !empty($posted_data['billing_last_name']) ? sanitize_text_field($posted_data['billing_last_name']) : '';
    
    // Check if user exists
    $user = get_user_by('email', $user_email);
    
    // If user doesn't exist, create one
    if (!$user) {
        // Generate a username from the email
        $username = sanitize_user(current(explode('@', $user_email)), true);
        $counter = 1;
        $new_username = $username;
        
        // Make sure username is unique
        while (username_exists($new_username)) {
            $new_username = $username . $counter;
            $counter++;
        }
        
        // Generate a random password
        $random_password = wp_generate_password(12, false);
        
        // Create the user
        $user_id = wp_create_user($new_username, $random_password, $user_email);
        
        if (is_wp_error($user_id)) {
            // Handle error - could log this
            return;
        }
        
        // Set the user's role
        $user = new WP_User($user_id);
        $user->set_role('customer');
        
        // Send the user a notification with their password
        wp_new_user_notification($user_id, null, 'user');
    } else {
        $user_id = $user->ID;
    }
    
    // Now update user meta with form data
    if (!empty($first_name)) {
        update_user_meta($user_id, 'billing_first_name', $first_name);
        update_user_meta($user_id, 'first_name', $first_name);
    }
    
    if (!empty($last_name)) {
        update_user_meta($user_id, 'billing_last_name', $last_name);
        update_user_meta($user_id, 'last_name', $last_name);
    }
    
    // Update other billing fields
    $meta_fields = array(
        'billing_address_1', 'billing_address_2', 'billing_city', 'billing_postcode',
        // NEW: Add new address fields
        'address_street', 'address_line2', 'address_city', 'address_postcode'
    );
    
    foreach ($meta_fields as $field) {
        if (!empty($posted_data[$field])) {
            update_user_meta($user_id, $field, sanitize_text_field($posted_data[$field]));
        }
    }
    
    // Update mobile number
    if (!empty($posted_data['mobile'])) {
        $mobile = sanitize_text_field($posted_data['mobile']);
        update_user_meta($user_id, 'billing_phone', $mobile);
        update_user_meta($user_id, 'cd_mobile_number', $mobile);
    }
    
    // Now handle the vehicle registration
    if (!empty($posted_data['registration_number']) && !empty($posted_data['make']) && 
        !empty($posted_data['model']) && !empty($posted_data['color'])) {
        
        global $wpdb;
        
        // Check if this vehicle already exists for this user
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}cd_vehicles 
             WHERE user_id = %d AND registration_number = %s",
            $user_id, $posted_data['registration_number']
        ));
        
        if (!$exists) {
            // Add the vehicle
            $wpdb->insert(
                $wpdb->prefix . 'cd_vehicles',
                array(
                    'user_id' => $user_id,
                    'registration_number' => sanitize_text_field($posted_data['registration_number']),
                    'make' => sanitize_text_field($posted_data['make']),
                    'model' => sanitize_text_field($posted_data['model']),
                    'color' => sanitize_text_field($posted_data['color']),
                    'active' => 1
                )
            );
            
            // If insertion was successful, get the vehicle ID
            if ($wpdb->insert_id) {
                $vehicle_id = $wpdb->insert_id;
                
                // Optional: Send emails to parking operators
                $email_manager = cd_get_email_manager();
                $email_manager->send_notices_to_all_operators($user_id, $vehicle_id);
            }
        }
    }
}