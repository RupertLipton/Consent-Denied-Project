<?php
if (!defined('ABSPATH')) {
    exit;
}

function cd_settings_page() {
    // Save settings
    if (isset($_POST['save_settings'])) {
        update_option('cd_basic_subscription_id', sanitize_text_field($_POST['cd_basic_subscription_id']));
        update_option('cd_premium_subscription_id', sanitize_text_field($_POST['cd_premium_subscription_id']));
        echo '<div class="notice notice-success"><p>Settings saved successfully.</p></div>';
    }

    // Get current values
    $basic_id = get_option('cd_basic_subscription_id', '62');
    $premium_id = get_option('cd_premium_subscription_id', '63');
    ?>
    <div class="wrap">
        <h1>Consent Denied Settings</h1>
        
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row">Basic Subscription Product ID</th>
                    <td>
                        <input type="text" name="cd_basic_subscription_id" 
                               value="<?php echo esc_attr($basic_id); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Premium Subscription Product ID</th>
                    <td>
                        <input type="text" name="cd_premium_subscription_id" 
                               value="<?php echo esc_attr($premium_id); ?>" class="regular-text">
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <input type="submit" name="save_settings" class="button button-primary" 
                       value="Save Settings">
            </p>
        </form>
    </div>
    <?php
}