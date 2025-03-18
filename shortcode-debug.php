<?php
/*
Template Name: Shortcode Debug
*/
get_header();
?>

<div class="container">
    <h1>Shortcode Test Page</h1>
    
    <div class="test-section">
        <h2>Public Registration Shortcode:</h2>
        <?php echo do_shortcode('[cd_public_registration]'); ?>
    </div>
    
    <div class="test-section">
        <h2>Built-in Shortcode Test:</h2>
        <?php echo do_shortcode('[gallery]'); ?>
    </div>
    
    <div class="test-section">
        <h2>Raw Function Call:</h2>
        <?php 
        if (function_exists('cd_public_registration_shortcode')) {
            echo cd_public_registration_shortcode();
        } else {
            echo 'Function does not exist!';
        }
        ?>
    </div>
</div>

<?php get_footer(); ?>