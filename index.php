<?php
/**
 * Plugin Name: Mobile Logo
 * Plugin URI: https://jasonlawton.com
 * Description: Add the ability for a custom mobile logo at a given breakpoint
 * Version: 1.0.0
 * Author: Jason Lawton
 * Author URI: https://jasonlawton.com
 * License: GPL2
 */

// make sure we have the media folder enqueued
add_action( 'admin_enqueue_scripts', 'jlml_enqueue' );
function jlml_enqueue($hook) {
    wp_enqueue_media();
    // wp_enqueue_script('')
    wp_enqueue_script( 'wp-media-picker', plugins_url( 'includes/wp-media-picker/wp-media-picker.min.js', __FILE__ ),  array( 'jquery', 'jquery-ui-widget', 'media-editor' ), '0.5.1', true );
    wp_enqueue_style(  'wp-media-picker', plugins_url( 'includes/wp-media-picker/wp-media-picker.min.css', __FILE__ ), array(), '0.5.1' );
}

// add this content in the header
add_action( 'wp_head', 'jlml_add_style' );
function jlml_add_style() {
    $options = get_option( 'jl_mobile_logo_settings' );
    $image   = wp_get_attachment_image_src($options['jl_mobile_logo_image']);
    $width   = $options['jl_mobile_logo_media_query_breakpoint'];
    ?>
    <style>
    @media (max-width: <?php echo $width; ?>px) {
        #logo, .custom-logo {
            display : none;
        }
        .jl-mobile-logo {
            content: url("<?php echo $image[0]; ?>")
        }
    }
    </style>
    <?php
}

// once all code is loaded, append our div after the img
add_action( 'wp_head', 'jlml_add_logo' );
function jlml_add_logo() {
    $options = get_option( 'jl_mobile_logo_settings' );
    $class   = $options['jl_mobile_logo_extra_css'];
    ?>
    <script>
        jQuery(function() {
            var logo = jQuery('.custom-logo');
            if (logo.length === 0) {
                logo = jQuery('#logo');
                if (!logo) {
                    console.log('no logo found');
                    return;
                }
            }
            logo.after('<img class="jl-mobile-logo <?php echo $class; ?>">');
        });
    </script>
    <?php
}

/**
 * Settings
 */

add_action( 'admin_menu', 'jl_mobile_logo_add_admin_menu' );
add_action( 'admin_init', 'jl_mobile_logo_settings_init' );


function jl_mobile_logo_add_admin_menu(  ) { 

    add_menu_page( 'Mobile Logo', 'Mobile Logo', 'manage_options', 'Mobile Logo', 'jl_mobile_logo_options_page' );

}


function jl_mobile_logo_settings_init(  ) { 

    register_setting( 'pluginPage', 'jl_mobile_logo_settings' );

    add_settings_section(
        'jl_mobile_logo_pluginPage_section', 
        __( 'Your section description', 'wordpress' ), 
        'jl_mobile_logo_settings_section_callback', 
        'pluginPage'
    );

    add_settings_field( 
        'jl_mobile_logo_image', 
        __( 'Logo Path', 'wordpress' ), 
        'jl_mobile_logo_image_render', 
        'pluginPage', 
        'jl_mobile_logo_pluginPage_section' 
    );

    add_settings_field( 
        'jl_mobile_logo_media_query_breakpoint', 
        __( 'Width to change at (Media Query breakpoint)', 'wordpress' ), 
        'jl_mobile_logo_media_query_breakpoint_render', 
        'pluginPage', 
        'jl_mobile_logo_pluginPage_section' 
    );

    add_settings_field( 
        'jl_mobile_logo_extra_css', 
        __( 'Extra CSS Class on wrapper to help styling on your end', 'wordpress' ), 
        'jl_mobile_logo_extra_css_render', 
        'pluginPage', 
        'jl_mobile_logo_pluginPage_section' 
    );


}


function jl_mobile_logo_image_render(  ) { 

    $options = get_option( 'jl_mobile_logo_settings' );
    ?>
    <input id='jl-mobile-logo' type='text' name='jl_mobile_logo_settings[jl_mobile_logo_image]' value='<?php echo $options['jl_mobile_logo_image']; ?>'>
    <?php

}


function jl_mobile_logo_media_query_breakpoint_render(  ) { 

    $options = get_option( 'jl_mobile_logo_settings' );
    ?>
    <input type='text' name='jl_mobile_logo_settings[jl_mobile_logo_media_query_breakpoint]' value='<?php echo $options['jl_mobile_logo_media_query_breakpoint']; ?>'>
    <?php

}


function jl_mobile_logo_extra_css_render(  ) { 

    $options = get_option( 'jl_mobile_logo_settings' );
    ?>
    <input type='text' name='jl_mobile_logo_settings[jl_mobile_logo_extra_css]' value='<?php echo $options['jl_mobile_logo_extra_css']; ?>'>
    <?php

}


function jl_mobile_logo_settings_section_callback(  ) { 

    echo __( 'Set the options you would like for the plugin.', 'wordpress' );

}


function jl_mobile_logo_options_page(  ) { 

    ?>
    <form action='options.php' method='post'>

        <h2>Mobile Logo</h2>

        <?php
        settings_fields( 'pluginPage' );
        do_settings_sections( 'pluginPage' );
        submit_button();
        ?>

    </form>

    <script type="text/javascript">
        jQuery(function () {
            jQuery( '#jl-mobile-logo' ).wpMediaPicker();
        });
    </script>

    <?php

}