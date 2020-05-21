<?php
/**
 * Meeter Groups
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( class_exists( 'BP_Group_Extension' ) ) :

/**
 * The Meeter group class
 *
 * @package Meeter
 * @since 1.0.0
 */
class BuddyMeet_Group extends BP_Group_Extension {
    function __construct() {
        global $bp;

        $enabled = false;
        if ( isset( $bp->groups->current_group->id ) ) {
            $enabled = meeter_is_enabled($bp->groups->current_group->id);
        }

        $args = array(
            'name' => meeter_get_name(),
            'slug' => meeter_get_slug(),
            'nav_item_position' => 40,
            'enable_nav_item' =>  $enabled
        );
        parent::init( $args );
    }

    function create_screen( $group_id = null) {
        global $bp;

        if ( ! $group_id ) {
            $group_id = $bp->groups->current_group->id;
        }

        if ( !bp_is_group_creation_step( $this->slug ) )
            return false;

        wp_nonce_field( 'groups_create_save_' . $this->slug );

        $this->render_settings($group_id, true);
    }

    function create_screen_save($group_id = null) {
        global $bp;

        if ( ! $group_id ) {
            $group_id = $bp->groups->current_group->id;
        }

        check_admin_referer( 'groups_create_save_' . $this->slug );

        $this->persist_settings($group_id);
    }

    function edit_screen( $group_id = null ) {
        global $bp;

        if ( !groups_is_user_admin( $bp->loggedin_user->id, $bp->groups->current_group->id ) && ! current_user_can( 'bp_moderate' ) ) {
            return false;
        }

        if ( !bp_is_group_admin_screen( $this->slug ) )
            return false;

        if (!$group_id){
            $group_id = $bp->groups->current_group->id;
        }

        wp_nonce_field( 'groups_edit_save_' . $this->slug );

        $this->render_settings($group_id, false);
        ?>

        <input type="submit" name="save" value="Save" />
        <?php
    }

    function edit_screen_save( $group_id = null ) {
        global $bp;

        $do_save = isset($_POST['save'] ) ? sanitize_text_field($_POST['save'])  === "true": false;
        if ($do_save) {
            return false;
        }

        if ( !$group_id ) {
            $group_id = $bp->groups->current_group->id;
        }

        check_admin_referer( 'groups_edit_save_' . $this->slug );

        $this->persist_settings($group_id);

        bp_core_add_message( __( 'Settings saved successfully', 'buddypress' ) );

        bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/' . $this->slug );
    }

    function display( $group_id = null ) {
        global $bp;

        if (!$group_id) {
            $group_id = $bp->groups->current_group->id;
        }

        if ( groups_is_user_member( $bp->loggedin_user->id, $group_id )
            || groups_is_user_mod( $bp->loggedin_user->id, $group_id )
            || groups_is_user_admin( $bp->loggedin_user->id, $group_id )
            || is_super_admin() ) {

            $enabled = meeter_is_enabled($bp->groups->current_group->id);
            if ( $enabled == 1 ) {
                $is_bp_nouveau = function_exists('bp_nouveau_single_item_subnav_classes');
                $home = $is_bp_nouveau ? 'meeter/home' : 'meeter/legacy/home';
                $this->get_groups_template_part( $home );
            }
        } else {
            echo '<div id="message" class="error"><p>'.__('This content is only available to group members.', 'meeter').'</p></div>';
        }
    }

    function widget_display() {
        // Not used
    }

    function render_settings($group_id, $is_create){
        $defaults = meeter_default_settings();
        $display_settings = apply_filters( 'meeter_display_group_settings', array_keys($defaults) );

        ?>
        <div class="wrap">
            <h4><?php _e( meeter_get_name() . ' Settings', 'meeter' ) ?></h4>

            <fieldset>
                <p><?php _e( 'Allow members of this group to enter the same video conference room.', 'meeter' ); ?></p>
                <?php
                $enabled = $is_create ? $defaults['enabled'] : meeter_is_enabled($group_id);

                //if there is not any room set up create a uuid
                $room = meeter_groups_get_groupmeta( $group_id, 'meeter_room', wp_generate_uuid4());
                $password = meeter_groups_get_groupmeta( $group_id, 'meeter_password', '');
                $domain =  meeter_groups_get_groupmeta( $group_id, 'meeter_domain', $defaults['domain']);
                $toolbar =  meeter_groups_get_groupmeta( $group_id, 'meeter_toolbar',  $defaults['toolbar']);
                $settings =  meeter_groups_get_groupmeta( $group_id, 'meeter_settings',  $defaults['settings']);
                $width =  meeter_groups_get_groupmeta( $group_id, 'meeter_width',  $defaults['width']);
                $height =  meeter_groups_get_groupmeta( $group_id, 'meeter_height',  $defaults['height']);
                $background_color =  meeter_groups_get_groupmeta( $group_id, 'meeter_background_color',  $defaults['background_color']);
                $default_language =  meeter_groups_get_groupmeta( $group_id, 'meeter_default_language',  $defaults['default_language']);
                $show_watermark =  meeter_groups_get_groupmeta( $group_id, 'meeter_show_watermark',  $defaults['show_watermark']);
                $show_brand_watermark =  meeter_groups_get_groupmeta( $group_id, 'meeter_show_brand_watermark',  $defaults['show_brand_watermark']);
                $brand_watermark_link =  meeter_groups_get_groupmeta( $group_id, 'meeter_brand_watermark_link',  $defaults['brand_watermark_link']);
                $film_strip_only =  meeter_groups_get_groupmeta( $group_id, 'meeter_film_strip_only',  $defaults['film_strip_only']);
                $start_audio_only =  meeter_groups_get_groupmeta( $group_id, 'meeter_start_audio_only',  $defaults['start_audio_only']);
                $disable_video_quality_label =  meeter_groups_get_groupmeta( $group_id, 'meeter_disable_video_quality_label',  $defaults['disable_video_quality_label']);
                ?>

                <div class="field-group">
                    <div class="checkbox">
                        <label><input type="checkbox" name="meeter_enabled" value="1" <?php checked( (bool) $enabled )?>> <?php _e( 'Activate', 'meeter' ); ?></label>
                    </div>
                </div>

                <?php if(in_array('domain', $display_settings)): ?>
                <div class="field-group">
                    <label><?php _e( 'Domain', 'meeter' ); ?></label>
                    <input type="text" name="meeter_domain" id="meeter_domain" value="<?php esc_attr_e($domain); ?>"/>
                    <p class="description"><?php esc_html_e( 'The domain the Jitsi Meet server runs. Defaults to their free hosted service.', 'meeter' ); ?></p>
                </div>
                <?php endif; ?>

                <?php if(in_array('room', $display_settings)): ?>
                <div class="field-group">
                        <label><?php _e( 'Room', 'meeter' ); ?></label>
                        <input type="text" name="meeter_room" id="meeter_room" value="<?php esc_attr_e($room); ?>"/>
                        <p class="description"><?php esc_html_e( 'Set the room group members will enter automatically when visiting the ' .meeter_get_name(). ' menu.', 'meeter' ); ?></p>
                </div>
                <?php else: ?>
                    <input type="hidden" name="meeter_room" value="<?php esc_attr_e($room); ?>"/>
                <?php endif; ?>

                <?php if(in_array('password', $display_settings)): ?>
                <div class="field-group">
                    <label><?php _e( 'Password', 'meeter' ); ?></label>
                    <input type="password" name="meeter_password" value="<?php  esc_attr_e($password); ?>"/>
                    <p class="description"><?php esc_html_e( 'Set the password the group members will have to enter to join the room. The first to visit - and therefore create - the room will enter without any password. The rest participants will have to fill-in the password.', 'meeter' ); ?></p>
                </div>
                <?php endif; ?>

                <?php if(in_array('toolbar', $display_settings)): ?>
                <div class="field-group">
                    <label><?php _e( 'Toolbar', 'meeter' ); ?></label>
                    <input type="text" name="meeter_toolbar" id="meeter_toolbar" value="<?php  esc_attr_e($toolbar); ?>"/>
                    <p class="description"><?php _e( 'The toolbar buttons to get displayed in comma separated format. For more information refer to <a  target="_blank" href="https://github.com/jitsi/jitsi-meet/blob/master/interface_config.js#L49">TOOLBAR_BUTTONS</a>.', 'meeter' ); ?></p>
                </div>
                <?php endif; ?>

                <?php if(in_array('settings', $display_settings)): ?>
                <div class="field-group">
                    <label><?php _e( 'Settings', 'meeter' ); ?></label>
                    <input type="text" name="meeter_settings" id="meeter_settings" value="<?php  esc_attr_e($settings); ?>"/>
                    <p class="description"><?php _e( 'The settings to be available in comma separated format. For more information refer to <a  target="_blank" href="https://github.com/jitsi/jitsi-meet/blob/master/interface_config.js#L57">SETTINGS_SECTIONS</a>.', 'meeter' ); ?></p>
                </div>
                <?php endif; ?>

                <?php if(in_array('width', $display_settings)): ?>
                <div class="field-group">
                    <label><?php _e( 'Width', 'meeter' ); ?></label>
                    <input type="text" name="meeter_width" id="meeter_width" value="<?php  esc_attr_e($width); ?>"/>
                    <p class="description"><?php esc_html_e( 'The width in pixels or percentage of the embedded window.', 'meeter' ); ?></p>
                </div>
                <?php endif; ?>

                <?php if(in_array('height', $display_settings)): ?>
                <div class="field-group">
                    <label><?php _e( 'Height', 'meeter' ); ?></label>
                    <input type="text" name="meeter_height" id="meeter_height" value="<?php  esc_attr_e($height); ?>"/>
                    <p class="description"><?php esc_html_e( 'The height in pixels or percentage of the embedded window.', 'meeter' ); ?></p>
                </div>
                <?php endif; ?>

                <?php if(in_array('background_color', $display_settings)): ?>
                <div class="field-group">
                    <label><?php _e( 'Background Color', 'meeter' ); ?></label>
                    <input type="text" name="meeter_background_color" id="meeter_background_color" value="<?php  esc_attr_e($background_color); ?>"/>
                    <p class="description"><?php esc_html_e( 'The background color of the window when camera is off.', 'meeter' ); ?></p>
                </div>
                <?php endif; ?>

                <?php if(in_array('default_language', $display_settings)): ?>
                <div class="field-group">
                    <label><?php _e( 'Default Language', 'meeter' ); ?></label>
                    <input type="text" name="meeter_default_language" id="meeter_default_language" value="<?php  esc_attr_e($default_language); ?>"/>
                    <p class="description"><?php esc_html_e( 'The default language.', 'meeter' ); ?></p>
                </div>
                <?php endif; ?>

                <?php if(in_array('show_watermark', $display_settings)): ?>
                <div class="field-group">
                    <div class="checkbox">
                        <label><input type="checkbox" name="meeter_show_watermark" value="1" <?php checked( (bool) $show_watermark)?>> <?php _e( 'Show Watermark', 'meeter' ); ?></label>
                    </div>
                    <p class="description"><?php esc_html_e( 'Show/Hide the Jitsi Meet watermark. Please leave it checked unless you use your own domain.', 'meeter' ); ?></p>
                </div>
                <?php endif; ?>

                <?php if(in_array('show_brand_watermark', $display_settings)): ?>
                    <div class="field-group">
                        <div class="checkbox">
                            <label><input type="checkbox" name="meeter_show_brand_watermark" value="1" <?php checked( (bool) $show_brand_watermark)?>> <?php _e( 'Show Brand Watermark', 'meeter' ); ?></label>
                        </div>
                        <p class="description"><?php esc_html_e( 'Show/Hide the Jitsi Meet Brand watermark.', 'meeter' ); ?></p>
                    </div>
                <?php endif; ?>

                <?php if(in_array('brand_watermark_link', $display_settings)): ?>
                    <div class="field-group">
                        <label><?php _e( 'Brand Watermark Link', 'meeter' ); ?></label>
                        <input type="text" name="meeter_brand_watermark_link" id="meeter_brand_watermark_link" value="<?php  echo esc_url($brand_watermark_link); ?>"/>
                        <p class="description"><?php esc_html_e( 'The link for the brand watermark.', 'meeter' ); ?></p>
                    </div>
                <?php endif; ?>

                <?php if(in_array('film_strip_only', $display_settings)): ?>
                <div class="field-group">
                    <div class="checkbox">
                        <label><input type="checkbox" name="meeter_film_strip_only" value="1" <?php checked( (bool) $film_strip_only)?>> <?php _e( 'Film Strip Mode Only', 'meeter' ); ?></label>
                    </div>
                    <p class="description"><?php esc_html_e( 'Display the window in film strip only mode.', 'meeter' ); ?></p>
                </div>
                <?php endif; ?>

                <?php if(in_array('start_audio_only', $display_settings)): ?>
                <div class="field-group">
                    <div class="checkbox">
                        <label><input type="checkbox" name="meeter_start_audio_only" value="1" <?php checked( (bool) $start_audio_only)?>> <?php _e( 'Start Audio Only', 'meeter' ); ?></label>
                    </div>
                    <p class="description"><?php esc_html_e( 'Every participant enters the room having enabled only their microphone. Camera is off.', 'meeter' ); ?></p>
                </div>
                <?php endif; ?>

                <?php if(in_array('disable_video_quality_label', $display_settings)): ?>
                <div class="field-group">
                    <div class="checkbox">
                        <label><input type="checkbox" name="meeter_disable_video_quality_label" value="1" <?php checked( (bool) $disable_video_quality_label)?>> <?php _e( 'Disable Video Quality Indicator', 'meeter' ); ?></label>
                    </div>
                    <p class="description"><?php esc_html_e( 'Hide/Show the video quality indicator.', 'meeter' ); ?></p>
                </div>
                <?php endif; ?>

            </fieldset>
        </div>
        <?php
    }

    function persist_settings($group_id){
        $defaults = meeter_default_settings();

        meeter_groups_update_groupmeta($group_id, 'meeter_enabled', "0");
        meeter_groups_update_groupmeta($group_id, 'meeter_room', '');
        meeter_groups_update_groupmeta($group_id, 'meeter_password', '');
        meeter_groups_update_groupmeta($group_id, 'meeter_domain', $defaults['domain'] );
        meeter_groups_update_groupmeta($group_id, 'meeter_toolbar', $defaults['toolbar'] );
        meeter_groups_update_groupmeta($group_id, 'meeter_settings', $defaults['settings'] );
        meeter_groups_update_groupmeta($group_id, 'meeter_width', $defaults['width'] );
        meeter_groups_update_groupmeta($group_id, 'meeter_height', $defaults['height'] );
        meeter_groups_update_groupmeta($group_id, 'meeter_background_color', $defaults['background_color'] );
        meeter_groups_update_groupmeta($group_id, 'meeter_default_language', $defaults['default_language'] );
        meeter_groups_update_groupmeta($group_id, 'meeter_show_watermark', "0" );
        meeter_groups_update_groupmeta($group_id, 'meeter_show_brand_watermark', "0" );
        meeter_groups_update_groupmeta($group_id, 'meeter_brand_watermark_link', "" );
        meeter_groups_update_groupmeta($group_id, 'meeter_film_strip_only', "0" );
        meeter_groups_update_groupmeta($group_id, 'meeter_start_audio_only', "0" );
        meeter_groups_update_groupmeta($group_id, 'meeter_disable_video_quality_label', "0" );
    }

    function get_groups_template_part( $slug ) {
        add_filter( 'bp_locate_template_and_load', '__return_true');
        add_filter( 'bp_get_template_stack', array($this, 'set_template_stack'), 10, 1 );

        bp_get_template_part( 'groups/single/' . $slug );

        remove_filter( 'bp_locate_template_and_load', '__return_true' );
        remove_filter( 'bp_get_template_stack', array($this, 'set_template_stack'), 10);
    }

    function set_template_stack( $stack = array() ) {
        if ( empty( $stack ) ) {
            $stack = array( meeter_get_plugin_dir() . 'templates' );
        } else {
            $stack[] = meeter_get_plugin_dir() . 'templates';
        }

        return $stack;
    }
}

/**
 * Waits for bp_init hook before loading the group extension
 *
 * Let's make sure the group id is defined before loading our stuff
 *
 * @since 1.0.0
 *
 * @uses bp_register_group_extension() to register the group extension
 */
function meeter_register_group_extension() {
    bp_register_group_extension( 'BuddyMeet_Group' );
}

add_action( 'bp_init', 'meeter_register_group_extension' );

endif;