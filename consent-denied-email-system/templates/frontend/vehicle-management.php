<?php if (!defined('ABSPATH')) exit; ?>

<div class="cd-vehicle-management">
    <?php echo $message; ?>
    
    <?php echo do_shortcode('[cd_subscription_status]'); ?>
    
    <?php if ($vehicle_manager->can_add_more_vehicles($user_id)): ?>
        <div class="cd-add-vehicle">
            <h3>Add New Vehicle</h3>
            <form method="post" action="">
                <p>
                    <label for="registration_number">Registration Number</label>
                    <input type="text" name="registration_number" id="registration_number" required maxlength="20">
                </p>
                <p>
                    <label for="make">Make</label>
                    <input type="text" name="make" id="make" required maxlength="100">
                </p>
                <p>
                    <label for="model">Model</label>
                    <input type="text" name="model" id="model" required maxlength="100">
                </p>
                <p>
                    <label for="colour">Colour</label>
                    <input type="text" name="colour" id="colour" required maxlength="50">
                </p>
                <p>
                    <input type="submit" name="submit_vehicle" value="Add Vehicle" class="button">
                </p>
            </form>
        </div>
    <?php endif; ?>
    
    <div class="cd-vehicles-list">
        <h3>Your Vehicles</h3>
        <?php if ($vehicles): ?>
            <table class="cd-vehicles-table">
                <thead>
                    <tr>
                        <th>Registration</th>
                        <th>Make</th>
                        <th>Model</th>
                        <th>Colour</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vehicles as $vehicle): ?>
                        <tr id="vehicle-row-<?php echo esc_attr($vehicle->id); ?>">
                            <td><?php echo esc_html($vehicle->registration_number); ?></td>
                            <td><?php echo esc_html($vehicle->make); ?></td>
                            <td><?php echo esc_html($vehicle->model); ?></td>
                            <td><?php echo esc_html($vehicle->colour); ?></td>
                            <td>
                                <button type="button" class="button edit-vehicle" 
                                        onclick="cdShowEditForm(<?php echo esc_attr($vehicle->id); ?>)">
                                    Edit
                                </button>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="vehicle_id" value="<?php echo esc_attr($vehicle->id); ?>">
                                    <input type="submit" name="delete_vehicle" class="button" value="Remove"
                                           onclick="return confirm('Are you sure you want to remove this vehicle?');">
                                </form>
                            </td>
                        </tr>
                        <tr id="edit-form-<?php echo esc_attr($vehicle->id); ?>" style="display: none;">
                            <td colspan="5">
                                <form method="post" class="cd-edit-vehicle-form">
                                    <input type="hidden" name="vehicle_id" value="<?php echo esc_attr($vehicle->id); ?>">
                                    <div class="form-row">
                                        <label>Registration Number:
                                            <input type="text" name="registration_number" 
                                                   value="<?php echo esc_attr($vehicle->registration_number); ?>"
                                                   required maxlength="20">
                                        </label>
                                        <label>Make:
                                            <input type="text" name="make" 
                                                   value="<?php echo esc_attr($vehicle->make); ?>"
                                                   required maxlength="100">
                                        </label>
                                    </div>
                                    <div class="form-row">
                                        <label>Model:
                                            <input type="text" name="model" 
                                                   value="<?php echo esc_attr($vehicle->model); ?>"
                                                   required maxlength="100">
                                        </label>
                                        <label>Colour:
                                            <input type="text" name="colour" 
                                                   value="<?php echo esc_attr($vehicle->colour); ?>"
                                                   required maxlength="50">
                                        </label>
                                    </div>
                                    <div class="form-actions">
                                        <input type="submit" name="edit_vehicle" value="Save Changes" class="button">
                                        <button type="button" class="button" 
                                                onclick="cdHideEditForm(<?php echo esc_attr($vehicle->id); ?>)">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <script>
                function cdShowEditForm(vehicleId) {
                    document.getElementById('edit-form-' + vehicleId).style.display = 'table-row';
                    document.getElementById('vehicle-row-' + vehicleId).style.display = 'none';
                }
                
                function cdHideEditForm(vehicleId) {
                    document.getElementById('edit-form-' + vehicleId).style.display = 'none';
                    document.getElementById('vehicle-row-' + vehicleId).style.display = 'table-row';
                }
            </script>
            <style>
                .cd-edit-vehicle-form {
                    padding: 1em;
                    background: #f9f9f9;
                    border: 1px solid #ddd;
                }
                .cd-edit-vehicle-form .form-row {
                    display: flex;
                    gap: 1em;
                    margin-bottom: 1em;
                }
                .cd-edit-vehicle-form label {
                    flex: 1;
                }
                .cd-edit-vehicle-form input[type="text"] {
                    width: 100%;
                    padding: 0.5em;
                }
                .cd-edit-vehicle-form .form-actions {
                    display: flex;
                    gap: 0.5em;
                    justify-content: flex-end;
                }
            </style>
        <?php else: ?>
            <p>No vehicles registered yet.</p>
        <?php endif; ?>
    </div>
</div>