<?php
    /*
    Plugin Name: WFC Plugin
    Description: WFC Development Helper Plugin
    Author: Steve Fischer
    Version: 1.1
    */

    // plugin namespace = WFCDP (Web Full Circle Developer Plugin)

    define( 'WFCDP_SITE_URL', get_bloginfo( 'url' ).'/' );
    define( 'WFCDP_ADMIN_URL', admin_url() );
    define( 'WFCDP_PT', dirname( __DIR__ ).'/' );
    define( 'WFCDP_THEME_ROOT', realpath( __DIR__.'/../../' ) );
    define( 'WFCDP_CONFIG', WFCDP_PT.'/wfc_config' );
    define( 'WFCDP_THEME_FUNCTIONS', WFCDP_PT.'/theme_functions' );
    define( 'WFCDP_BUILD_THEME', WFCDP_THEME_FUNCTIONS.'/build-theme' );
    define( 'WFCDP_FUNCTIONS', WFCDP_PT.'/functions' );
    define( 'WFCDP_WIDGETS', WFCDP_PT.'/widgets' );
    define( 'WFCDP_SHORTCODE', WFCDP_PT.'/admin/shortcode' );
    define( 'WFCDP_URI', get_template_directory_uri() );
    define( 'WFCDP_ADM', WFCDP_PT.'/admin' );
    define( 'WFCDP_PLUGINS', WFCDP_PT.'/admin/plugins' );
    define( 'WFCDP_CSS_URI', WFCDP_URI.'/css' );
    define( 'WFCDP_JS_URI', WFCDP_URI.'/js' );
    define( 'WFCDP_IMG_URI', WFCDP_URI.'/images' );

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
        new BFIGitHubPluginUpdater( __FILE__, 'stevecfischer@gmail.com', "wfc_checker" );
    }

    require_once('web-widget.php');
    require_once('seo-widget.php');
    require_once('classes/wfc.render.php');

    $wfcweb = new wfc_web_checklist();
    $wfcseo = new wfc_seo();
    $render = new wfc_render();