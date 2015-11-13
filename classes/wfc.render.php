<?php

    /**
     *
     * @package scf-framework
     * @author steve
     * @date 11/12/15
     */
    class wfc_render
    {
        public function wfc_render(){
        }

        public function render_checklist_item( $v, $k ){
            ?>
            <span class="wfc-dashboard-checklist-item wfc-dynamic-check <?php $this->wfc_plugin_check( $k ); ?>">
                <input class="wfc-dashboard-checkbox" type="checkbox" name="task"/>
                <label><?php _e( $v ); ?></label>
                <br/>
            </span>
            <?php
        }

        /**
         * @param array $arr
         * @param string $callback_check
         */
        public function render_checklist_array( $arr, $callback_check, $class ){
            global $wfcweb;
            foreach( $arr as $k => $v ){
                if( $callback_check == "wfc_option_check" ){
                    $result = call_user_func( array($class, $callback_check), array($k, $v) );
                    $v      = $v['label'];
                } else{
                    $result = call_user_func( array($class, $callback_check), $k );
                }
                ?>
                <span class="wfc-dashboard-checklist-item wfc-dynamic-check <?php echo $result; ?>">
                    <input class="wfc-dashboard-checkbox" type="checkbox" name="task"/>
                    <label><?php _e( $v ); ?></label>
                    <br/>
                </span>
                <?php
            }
        }

        public function render_table( $arr, $callback_check, $class ){
            global $wfcweb;
            foreach( $arr as $k => $v ){
                if( $callback_check == "wfc_forms_check" ){
                    $result = call_user_func_array( array($class, $callback_check), array($v, $k) );
                } else{
                    $result = call_user_func( array($class, $callback_check), $k );
                }
                ?>
                <tr class="wfc-dashboard-checklist-item wfc-dynamic-check <?php echo $result; ?>">
                    <td><?php _e( $v['form_title'] ) ?></td>
                    <td><?php _e( $v['confirmation_type'] ) ?></td>
                    <td><?php _e( $v['to_email'] ) ?></td>
                </tr>
                <?php
            }
        }
    }