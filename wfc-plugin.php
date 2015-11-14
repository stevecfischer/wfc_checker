<?php
    /*
    Plugin Name: WFC Plugin
    Description: WFC Development Helper Plugin
    Author: Steve Fischer
    Plugin URI: https://github.com/stevecfischer/wfc_checker/
    Version: 1.3
    */

    // plugin namespace = WFCDP (Web Full Circle Developer Plugin)
    set_site_transient( 'update_plugins', NULL );
    define( 'WFCDP_SITE_URL', get_bloginfo( 'url' ).'/' );
    define( 'WFCDP_ADMIN_URL', admin_url() );
    define( 'WFCDP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

    /**
     * Includes CSS into WP head
     *
     * @since 0.1
     */
    add_action( 'admin_enqueue_scripts', 'wfcdp_admin_css_styles' );
    function wfcdp_admin_css_styles(){
        //Framework Styles includes jqueryui and bootstrap
        wp_register_style( 'wfcdp-style', plugins_url( 'css/styles.css', __FILE__ ) );
        wp_enqueue_style( 'wfcdp-style' );
    }

    require_once('classes/wfc.update.php');
    if( is_admin() ){
        new BFIGitHubPluginUpdater( __FILE__, 'stevecfischer', "wfc_checker" );
    }

    require_once('web-widget.php');
    require_once('seo-widget.php');
    require_once('classes/wfc.render.php');

    $wfcweb = new wfc_web_checklist();
    $wfcseo = new wfc_seo();
    $render = new wfc_render();