<?php
if (!defined('ABSPATH')) {
    exit;
}

// Remove the function declaration and directly use the code inside it.

// Access variables from the main function if needed.
global $vehicle_manager;
global $message;

// Rest of your code...

$vehicle_manager = new CD_Vehicle_Manager();
$message = '';

// Handle deletion if requested
if (isset($_POST['delete_vehicle']) && isset($_POST['vehicle_id'])) {
    $vehicle_id = intval($_POST['vehicle_id']);
    if ($vehicle_manager->delete_vehicle($vehicle_id, get_current_user_id())) {
        $message = '<div class="notice notice-success"><p>Vehicle removed successfully.</p></div>';
    } else {
        $message = '<div class="notice notice-error"><p>Error removing vehicle.</p></div>';
    }
}

// Get all vehicles with user information
global $wpdb;
$vehicles = $wpdb->get_results("
    SELECT v.*, u.display_name as user_name
    FROM {$wpdb->prefix}cd_vehicles v
    JOIN {$wpdb->users} u ON v.user_id = u.ID
    WHERE v.active = 1
    ORDER BY v.date_added DESC
");

?>
<div class="wrap">
    <h1>Vehicle Management</h1>
    
    <?php echo $message; ?>
    
    <div class="cd-vehicles-list">
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Registration</th>
                    <th>Make</th>
                    <th>Model</th>
                    <th>Color</th>
                    <th>Date Added</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($vehicles): ?>
                    <?php foreach ($vehicles as $vehicle): ?>
                        <tr>
                            <td><?php echo esc_html($vehicle->user_name); ?></td>
                            <td><?php echo esc_html($vehicle->registration_number); ?></td>
                            <td><?php echo esc_html($vehicle->make); ?></td>
                            <td><?php echo esc_html($vehicle->model); ?></td>
                            <td><?php echo esc_html($vehicle->color); ?></td>
                            <td><?php echo esc_html($vehicle->date_added); ?></td>
                            <td>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="vehicle_id" value="<?php echo esc_attr($vehicle->id); ?>">
                                    <input type="submit" name="delete_vehicle" class="button button-small" value="Remove"
                                           onclick="return confirm('Are you sure you want to remove this vehicle?');">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No vehicles registered yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
