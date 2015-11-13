<?php
    add_action( 'admin_init', 'wfc_dev_checklist', 1 );
    function wfc_dev_checklist(){
        add_meta_box(
            'wfc_develop_checklist',
            'WFC Development Checklist',
            'wfc_wfc_dev_display',
            'dashboard', 'normal'
        );
    }

    function wfc_wfc_dev_display( $post ){
        global $render, $wfcweb;
        $dev_human_array = array(
            'seo-spreadsheet'     => 'Have you received the SEO Keywords, Description, and Title Spreadsheet?',
            'seo-added-meta-info' => 'Have the SEO Keywords, Description, and Title Spreadsheet been entered?',
            'seo-move-old-pages'  => 'Did you move over all of the sites old pages?'
        );
        ?>
        <h3>Plugins</h3>
        <?php $render->render_checklist_array( $wfcweb->wfc_chklist_plugin_array, 'wfc_plugin_check', $wfcweb ); ?>
        <hr/>
        <h3>Options</h3>
        <?php $render->render_checklist_array( $wfcweb->wfc_chklist_option_array, 'wfc_option_check', $wfcweb );
        ?>
        <hr/>
        <h3>Files</h3>
        <?php $render->render_checklist_array( $wfcweb->wfc_chklist_files_array, 'wfc_file_check', $wfcweb ); ?>
        <hr/>
        <h3>Pages</h3>
        <?php $render->render_checklist_array( $wfcweb->wfc_chklist_pages_array, 'wfc_page_check', $wfcweb ); ?>
        <hr/>
        <h3>Forms</h3>
        <table>
            <tr class="wfc-dashboard-checklist-item wfc-dynamic-check wfc-table-headings">
                <th class="">Form Title</th>
                <th class="">Confirm Type</th>
                <th class="">To Address</th>
            </tr>
            <?php $render->render_table( $wfcweb->wfc_gravity_forms_array, 'wfc_forms_check', $wfcweb ); ?>
        </table>
        <hr/>
        <h3>Other</h3>
        <span class="wfc-dashboard-checklist-item wfc-dynamic-check <?php echo wfc_web_checklist::chk_wfc_footer(); ?>">
            <input class="wfc-dashboard-checkbox" type="checkbox" name="WFC_Footer"/>
            <label>Did you set the standard WFC Footer ("Website Design by Web Full Circle")</label>
            <br/>
        </span>
        <span class="wfc-dashboard-checklist-item wfc-dynamic-check <?php echo wfc_web_checklist::chk_wfc_login(); ?>">
            <input class="wfc-dashboard-checkbox" type="checkbox" name="WFC_Logo"/>
            <label>Did you add the WFC Logo to the WP Login Screen?</label>
            <br/>
        </span>
        <?php
    }

    /**
     * @property mixed pages
     */
    class wfc_web_checklist
    {
        private $active_plugins = array();
        public $wfc_options;
        private $pages = array();
        public $wfc_gravity_forms_array = array();
        public $wfc_chklist_plugin_array = array(
            'gravityforms'             => 'Gravity Forms',
            'threewp-activity-monitor' => 'Activity Monitor',
            'video-user-manuals'       => 'Video User Manual',
            'wordpress-seo'            => 'WordPress SEO'
        );
        public $wfc_chklist_option_array = array(
            'blog_public'         => array('label' => 'block search engines', 'default_value' => 0),
            'blogdescription'     => array(
                'label'         => 'wordpress tagline',
                'default_value' => "Just another wordpress site"
            ),
            'permalink_structure' => array('label' => 'set the permalinks to /%postname%/', 'default_value' => ""),
        );
        public $wfc_chklist_pages_array = array(
            'Sitemap'   => "Sitemap",
            'Thank You' => "Thank You"
        );
        public $wfc_chklist_files_array = array(
            'favicon.ico'             => "favicon.ico",
            '404.php'                 => "404.php",
            'template-full-width.php' => 'template-full-width.php',
            'editor-styles.css'       => 'editor-styles.css'
        );
        public $wfc_chklist_scrap_array = array();

        public function wfc_web_checklist(){
            $this->wfc_set_checklist_scrap_array();
            $this->setWfcOptions();
            $this->setActivePlugins();
            $this->setPages();
            $this->setGravityForms();
            /*'"/Web Full Circle/i"' => ''.get_bloginfo( 'url' ).'/'.'/cms-wfc/wp-login.php',
            '"/<a href=\"http:\/\/www.webfullcircle.com\" target=\"_blank\">Webfullcircle.com<\/a>/i"' => WFC_SITE_URL*/
        }

        public function wfc_set_checklist_scrap_array(){
            $x                             = WFCDP_SITE_URL;
            $this->wfc_chklist_scrap_array = array(
                'Webfullcircle.com' => $x
            );
        }

        /**
         * @param mixed $wfc_options
         */
        public function setWfcOptions(){
            /** @var $wpdb wpdb */
            global $wpdb;
            $where_clause_str = '';
            foreach( $this->wfc_chklist_option_array as $option_k => $option_v ){
                $where_clause_str .= "'$option_k',";
            }
            $tmp_options       =
                $wpdb->get_results(
                    "SELECT `option_name`,`option_value` FROM $wpdb->options WHERE option_name IN (".
                    substr( $where_clause_str, 0, -1 ).") ", ARRAY_A );
            $this->wfc_options = array();
            foreach( $tmp_options as $option ){
                $this->wfc_options[$option['option_name']] =
                    ($option['option_value'] != "" ? $option['option_value'] : false);
            }
        }

        public static function unserialize( $string ){
            if( is_serialized( $string ) ){
                $obj = @unserialize( $string );
            } else{
                $obj = json_decode( $string, true );
            }
            return $obj;
        }

        private function setPages(){
            $pages = get_pages();
            foreach( $pages as $page ){
                $this->pages[$page->post_name] = $page->post_title;
            }
        }

        private function setActivePlugins(){
            $plugins = get_option( 'active_plugins' );
            foreach( $plugins as $plugin ){
                $x = explode( "/", $plugin );
                array_push( $this->active_plugins, $x[0] );
            }
        }

        public function setGravityForms(){
            /** @var $wpdb wpdb */
            global $wpdb;
            $forms       =
                $wpdb->get_results( "SELECT rgf.`title`, rgfm.* FROM wfc_rg_form rgf LEFT JOIN wfc_rg_form_meta AS rgfm ON rgfm.form_id = rgf.id ", ARRAY_A );
            $array_forms = array();
            foreach( $forms as $form ){
                $notification_params = self::unserialize( $form['notifications'] );
                $confirm_params      = self::unserialize( $form['confirmations'] );
                foreach( $confirm_params as $param_k => $param_v ){
                    $confirm_key = $param_k;
                }
                foreach( $notification_params as $param_k => $param_v ){
                    $notify_key = $param_k;
                }

                $array_forms[] = array(
                    'form_title'        => $form['title'],
                    'confirmation_type' => $confirm_params[$confirm_key]['type'],
                    'to_email'          => $notification_params[$notify_key]['to']
                );
            }
            $this->wfc_gravity_forms_array = $array_forms;
        }

        public function wfc_plugin_check( $handle ){
            return $this->return_checked( in_array( $handle, $this->active_plugins ) );
        }

        public function wfc_forms_check( $handle ){
            $checked = false;
            if( $handle['confirmation_type'] == 'page' && $handle['to_email'] != "{admin_email}" ){
                $checked = true;
            }
            return $this->return_checked( $checked );
        }

        public function wfc_option_check( $handle ){
            $checked = true;
            if( $this->wfc_options[$handle[0]] == $handle[1]['default_value'] ){
                $checked = false;
            }
            return $this->return_checked( $checked );
        }

        public function wfc_page_check( $handle ){
            return $this->return_checked( in_array( $handle, $this->pages ) );
        }

        public function wfc_file_check( $handle ){
            $wfc_root = scandir( WFCDP_THEME_ROOT.'/' );
            return $this->return_checked( in_array( $handle, $wfc_root ) );
        }

        static function chk_wfc_footer(){
            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, WFCDP_SITE_URL );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
            $output = curl_exec( $ch );
            curl_close( $ch );
            if( preg_match( "/<a href=\"http:\/\/www.webfullcircle.com\" target=\"_blank\">Webfullcircle<\/a>/i", $output ) ){
                return " wfc-checklist-valid ";
            } else{
                return " wfc-checklist-invalid ";
            }
        }

        static function chk_wfc_login(){
            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, WFCDP_SITE_URL.'cms-wfc/wp-login.php' );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
            $output = curl_exec( $ch );
            curl_close( $ch );
            //Web Full Circle
            $search = "/Username/i";
            if( preg_match( $search, $output ) ){
                return " wfc-checklist-valid ";
            } else{
                return " wfc-checklist-invalid ";
            }
        }

        //@scftodo: this doesn't work yet. use the two static methods above.
        public function wfc_scrap_check( $url, $str ){
            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
            $output = curl_exec( $ch );
            curl_close( $ch );
            return $this->return_checked( preg_match( "/$str/i", $output ) );
        }

        public function return_checked( $checked ){
            if( $checked === true ){
                return " wfc-checklist-valid ";
            } else{
                return " wfc-checklist-invalid ";
            }
        }
    }

    //@scftodo: email is broken...
    function wfc_email_checklist(){
        global $current_user;
        $headers[] = 'From: Website Name <me@example.net>';
        $headers[] = 'Cc: steve fischer <steve.fischer@webfullcircle.com>';
        $to        = 'stevecfischer@gmail.com';
        $subject   = 'Checklist for '.$_POST['checklist_section'];
        $message   = 'Checklist completed and ready for next step.';
        if( !wp_mail( $to, $subject, $message, $headers ) ){
            die("Error emailing form");
        }
    }