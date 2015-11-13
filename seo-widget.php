<?php
    add_action( 'admin_init', 'wfc_seo_checklist', 1 );
    function wfc_seo_checklist(){
        add_meta_box(
            'wfc_seo_checklist',
            'WFC SEO Checklist',
            'wfc_wfc_seo_display',
            'dashboard', 'normal'
        );
    }

    function wfc_wfc_seo_display( $post ){
        global $wfcseo, $render;
        ?>
        <h3>Content</h3>
        <?php $render->render_checklist_array( $wfcseo->wfc_content_items, 'content_check', $wfcseo ); ?>
        <span class="wfc-dashboard-checklist-item wfc-dynamic-check <?php echo wfc_seo::chk_h1(); ?>">
            <input class="wfc-dashboard-checkbox" type="checkbox" name="h1_check"/>
            <label>Is there only 1 H1?</label>
            <br/>
        </span>
        <span class="wfc-dashboard-checklist-item wfc-dynamic-check <?php echo wfc_seo::chk_xml_sitemap(); ?>">
            <input class="wfc-dashboard-checkbox" type="checkbox" name="sitemap_xml_chk"/>
            <label>Sitemap.xml</label>
            <br/>
        </span>
        <hr/>
        <h3>Analytics</h3>
        <span class="wfc-dashboard-checklist-item wfc-dynamic-check <?php echo wfc_seo::chk_ga_script(); ?>">
            <input class="wfc-dashboard-checkbox" type="checkbox" name="ga_check"/>
            <label>Google Analytics Code Detected?</label>
            <br/>
        </span>
        <hr/>
    <?php }

    class wfc_seo
    {
        public $wfc_content_items = array(
            'empty_pages' => 'Blank/Latin Pages',
        );

        public function wfc_seo(){
        }

        public function content_check(){
            global $wpdb;
            $c       = $wpdb->get_results(
                "SELECT count(*) blank_page_count FROM wfc_posts WHERE wfc_posts.post_status='publish' AND (wfc_posts.post_content = '' OR wfc_posts.post_content LIKE  '%Lorem Ipsum%' )", OBJECT );
            $checked = true;
            if( $c[0]->blank_page_count > 0 ){
                $checked = false;
            }
            return $this->return_checked( $checked );
        }

        public function return_checked( $checked ){
            if( $checked === true ){
                return " wfc-checklist-valid ";
            } else{
                return " wfc-checklist-invalid ";
            }
        }

        //@SFTODO: problem is selecting which page(s) to check
        static function chk_h1(){
            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, WFCDP_SITE_URL );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
            $output = curl_exec( $ch );
            curl_close( $ch );
            if( preg_match_all( "/<h1/i", $output ) > 1 ){
                return " wfc-checklist-invalid ";
            } else{
                return " wfc-checklist-valid ";
            }
        }

        static function chk_ga_script(){
            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, WFCDP_SITE_URL );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
            $output = curl_exec( $ch );
            curl_close( $ch );
            if( !preg_match( "/ga\('create', 'UA/i", $output ) ){
                return " wfc-checklist-invalid ";
            } else{
                return " wfc-checklist-valid ";
            }
        }

        static function chk_xml_sitemap(){
            $ch = curl_init();
            echo WFCDP_SITE_URL . 'sitemap_index.xml';
            curl_setopt( $ch, CURLOPT_URL, WFCDP_SITE_URL . 'sitemap_index.xml' );
            curl_setopt( $ch, CURLOPT_NOBODY, 1 );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_exec( $ch );
            $is404 = curl_getinfo( $ch, CURLINFO_HTTP_CODE ) == 404;
            curl_close( $ch );
            if($is404){
                return " wfc-checklist-invalid ";
            } else{
                return " wfc-checklist-valid ";
            }
        }

    }