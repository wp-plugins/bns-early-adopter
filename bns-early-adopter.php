<?php
/*
Plugin Name: BNS Early Adopter
Plugin URI: http://buynowshop.com/plugins/bns-early-adopter
Description: Show off you are an early adopter of WordPress (alpha, beta, release candidate, and/or stable versions)
Version: 0.6.1
TextDomain: bns-ea
Author: Edward Caissie
Author URI: http://edwardcaissie.com/
License: GNU General Public License v2
License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

/**
 * BNS Early Adopter WordPress plugin
 * Show off you are an early adopter of WordPress by displaying a message
 * showing what version you are running (alpha, beta, release candidate and/or
 * stable versions).
 *
 * @package     BNS_Early_Adopter
 * @version     0.6.1
 *
 * @link        http://buynowshop.com/plugins/bns-early-adopter/
 * @link        https://github.com/Cais/bns-early-adopter/
 * @link        http://wordpress.org/extend/plugins/bns-early-adopter/
 *
 * @author      Edward Caissie <edward.caissie@gmail.com>
 * @copyright   Copyright (c) 2012-2013, Edward Caissie
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 2, as published by the
 * Free Software Foundation.
 *
 * You may NOT assume that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, write to:
 *
 *      Free Software Foundation, Inc.
 *      51 Franklin St, Fifth Floor
 *      Boston, MA  02110-1301  USA
 *
 * The license for this software can also likely be found here:
 * http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @version 0.5
 * @date    November 26, 2012
 * Remove load_plugin_textdomain as redundant
 * Optimized output buffer code used in shortcode function
 *
 * @version 0.6
 * @date    February 13, 2013
 * Added more i18n compatibility
 * Added code block termination comments
 * Changed constructor function name to __construct (i.e.: PHP5 code format)
 * Move all code into class structure
 *
 * @version 0.6.1
 * @date    April 2, 2013
 * Fixed conditional logic used to display plugin
 */

class BNS_Early_Adopter_Widget extends WP_Widget {

    /**
     * BNS Early Adopter Widget / Constructor
     * Extends the WP_Widget class and adds other related functionality
     *
     * @package BNS_Early_Adopter
     * @since   0.1
     *
     * @uses    (class) WP_Widget
     * @uses    add_action
     * @uses    add_shortcode
     *
     * @version 0.6
     * @date    December 13, 2012
     * Changed constructor function name to __construct (i.e.: PHP5 code format)
     * Moved activation functions and related calls into class constructor
     */
    function __construct() {
        /** Widget Settings */
        $widget_ops = array( 'classname' => 'bns-early-adopter', 'description' => __( 'White knuckling your active WordPress version? Show it off!', 'bns-ea' ) );

        /** Widget Control Settings */
        $control_ops = array( 'width' => '200', 'id_base' => 'bns-early-adopter' );

        /** Create the Widget */
        $this->WP_Widget( 'bns-early-adopter', 'BNS Early Adopter', $widget_ops, $control_ops );

        /**
         * WordPress version compatibility
         *
         * @package BNS_Early_Adopter
         * @since   0.1
         *
         * @uses    (global) $wp_version
         *
         * @version 0.6
         * @date    December 13, 2012
         * @internal Requires PHP5 or greater
         * @internal WordPress 3.2 and newer require PHP5
         * Add i18n compatibility
         */
        global $wp_version;
        $exit_message = __( 'BNS Early Adopter requires WordPress version 3.2 or newer. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please Update!</a>', 'bns-ea' );
        if ( version_compare( $wp_version, "3.2", "<" ) ) {
            exit ( $exit_message );
        } /** End if - version compare */

        /** Add Scripts and Styles */
        add_action( 'wp_enqueue_scripts', array( $this, 'scripts_and_styles' ) );

        /** Add Shortcode */
        add_shortcode( 'bnsea', array( $this, 'bnsea_shortcode' ) );

        /** Add widget */
        add_action( 'widgets_init', array( $this, 'load_bnsea_widget' ) );

    } /** End function - construct */


    /**
     * Enqueue Plugin Scripts and Styles
     * Adds plugin stylesheet (and scripts); also allows for custom stylesheet
     * to be added by end-user.
     *
     * @package BNS_Early_Adopter
     * @since   0.1
     *
     * @uses    get_plugin_data
     * @uses    plugin_dir_url
     * @uses    plugin_dir_path
     * @uses    wp_enqueue_style
     *
     * @version 0.4.2
     * @date    August 2, 2012
     * Programmatically add version number to enqueue calls
     *
     * @version 0.6
     * @date    December 13, 2012
     * Renamed to `scripts_and_styles`
     */
    function scripts_and_styles() {
        /** Call the wp-admin plugin code */
        require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
        /** @var $bnsea_data - holds the plugin header data */
        $bnsea_data = get_plugin_data( __FILE__ );

        /** Enqueue Scripts */
        /** Enqueue Style Sheets */
        wp_enqueue_style( 'BNSEA-Style', plugin_dir_url( __FILE__ ) . 'bnsea-style.css', array(), $bnsea_data['Version'], 'screen' );
        if ( is_readable( plugin_dir_path( __FILE__ ) . 'bnsea-custom-style.css' ) ) {
            wp_enqueue_style( 'BNSEA-Custom-Style', plugin_dir_url( __FILE__ ) . 'bnsea-custom-style.css', array(), $bnsea_data['Version'], 'screen' );
        } /** End if - is readable */

    } /** End function - scripts and styles */


    /**
     * The Widget Itself ...
     * ... lets get down to business.
     *
     * @package BNS_Early_Adopter
     * @since   0.1
     *
     * @param   $args
     * @param   $instance
     *
     * @uses    (global) wp_version
     * @uses    apply_filters
     * @uses    current_user_can
     * @uses    is_user_logged_in
     *
     * @version 0.6
     * @date    February 13, 2013
     * Moved `bnsea_display` out of `widget` method
     *
     * @version 0.6.1
     * @date    April 2, 2013
     * Fixed conditional logic used to display plugin
     */
    function widget( $args, $instance ){
        /** Get widget setting values */
        extract( $args );

        /** Get user selected values */
        $title          = apply_filters( 'widget_title', $instance['title'] );
        $show_alpha     = $instance['show_alpha'];
        $show_beta      = $instance['show_beta'];
        $show_rc        = $instance['show_rc'];
        $show_stable    = $instance['show_stable'];
        $only_admin     = $instance['only_admin'];

        /** The secret sauce of this whole sordid affair ... */
        /**
         * Get the global WordPress version and put it into an all lower case
         * string - we need to start somewhere, right?
         */
        global $wp_version;
        $version_string = strtolower( $wp_version );
        /** @var $ea_version - early adopter version; set to stable as default */
        $ea_version = 'stable';

        /** Step through the version string looking for an "a" for alpha, "b" for beta, or "r" for release candidate */
        for ( $i = 1; $i < strlen( $version_string ); $i++ ) {
            if ( 'a' == substr( $version_string, $i, 1 )
                || 'b' == substr( $version_string, $i, 1 )
                || 'r' == substr( $version_string, $i, 1 )) {
                /** @var    string $test_character - used to assign $ea_version string  */
                $test_character = substr( $version_string, $i, 1 );
                if ( 'a' == $test_character ) {
                    /** @var string $ea_version - if 'a' is found then set to alpha  */
                    $ea_version = 'alpha';
                } elseif ( 'b' == $test_character ) {
                    /** @var string $ea_version - if 'b' is found then set to beta */
                    $ea_version = 'beta';
                } elseif ( 'r' == $test_character ) {
                    /** @var string $ea_version - if 'r' is found then set to release */
                    $ea_version = 'release candidate';
                } else {
                    /** No 'a', 'b', or 'r' found must be some other nonsense */
                    $ea_version = 'stable(?)';
                }
                /** @var number $i - if the `test_character` was found, end the for loop by forcing the index value to its maximum */
                $i = strlen( $version_string );
            } /** End if - sub-string */
        } /** End for - string-length */

        /**
         * If all options are off BNS_Early_Adopter_Widget::bnsea_display will
         * return false and the widget should not display anything
         */
        if ( ! $this->bnsea_display( $instance, $ea_version ) ) {
            echo '<div class="bnsea-no-show">';
        } /** End if - not bnsea display returns true */

        /** Conditional check - only show Administrators */
        if ( $only_admin ) {
            echo '<div class="bnsea-no-show">';
        } /** End if - only administrators */

        /** @var    $before_widget  string - defined by theme */
        echo $before_widget;

        /** Widget $title, $before_widget, and $after_widget defined by theme */
        if ( $title ) {
            /**
             * @var $before_title   string - defined by theme
             * @var $after_title    string - defined by theme
             */
            echo $before_title . $title . $after_title;
        } /** End if  - title */

        /**
         * Get fancy here and write the output with correct grammar
         */
        if ( ( 'alpha' == $ea_version ) && ( $show_alpha ) ) {
            $output = apply_filters( 'bnsea_alpha_text', sprintf( __( "We are running an %s version of WordPress!", 'bns-ea' ), $ea_version ) );
        } elseif ( ( ( 'beta' == $ea_version ) && $show_beta )
            || ( ( 'stable' == $ea_version ) && $show_stable )
            || ( ( 'release candidate' == $ea_version ) && $show_rc ) ) {
            $output = apply_filters( 'bnsea_beta_text', sprintf( __( "We are running a %s version of WordPress!", 'bns-ea' ), $ea_version ) );
        } else {
            /** @var null $output - widgets must have output of some sort or the Gods of programming will rain hellfire down on your code */
            $output = '';
        } /** End if - version */

        /** @var string $output - data to be displayed with a little CSS for added dressing */
        $output = '<h3 class="bnsea-output">' . $output . '</h3>';

        echo apply_filters( 'bns_ea', $output, $args );

        /** @var    $after_widget   string - defined by theme */
        echo $after_widget;

        /** Conditional check - only show Administrators */
        if ( $only_admin ) {
            echo '</div><!-- bnsea-no-show -->';
        } /** End if - only administrators */

        /**
         * If all options are off BNS_Early_Adopter_Widget::bnsea_display will
         * return false and the widget should not display anything
         */
        if ( ! $this->bnsea_display( $instance, $ea_version ) ) {
            echo '</div><!-- bnsea-no-show -->';
        } /** End if - not bnsea display returns true */

    } /** End function - widget */


    /**
     * Early Adopter Display
     *
     * Returns string value to dictate if widget should be displayed or not.
     * Widget will not display by default.
     *
     * @package BNS_Early_Adopter
     * @since   0.2
     *
     * @param   array $instance - widget options
     * @param   string $ea_version - version reference
     *
     * @return  string $ea_display - true | false
     *
     * @version 0.3
     * Added release candidate option
     *
     * @version 0.6
     * @date    February 13, 2013
     * Moved out of `widget` method
     *
     * @version 0.6.1
     * @date    April 2, 2013
     * Refactored where $ea_display boolean value is set
     */
    function bnsea_display( $instance, $ea_version ){

        if ( ( $instance['show_alpha'] && ( 'alpha' == $ea_version ) )
            || ( $instance['show_beta'] && ( 'beta' == $ea_version ) )
            || ( $instance['show_rc'] && ( 'release candidate' == $ea_version ) )
            || ( $instance['show_stable'] && ( 'stable' == $ea_version ) ) ) {

            $ea_display = true;

        } else {

            $ea_display = false;

        } /** End if - boolean test to change display to true */

        return $ea_display;

    } /** End function - bnsea display */


    /**
     * Over writes update; maintains widget settings when multiple instances exist
     *
     * @package BNS_Early_Adopter
     * @since   0.1
     *
     * @param   $new_instance
     * @param   $old_instance
     *
     * @return  array $instance - updated widget settings
     *
     * @version 0.3
     * Added release candidate option
     */
    function update( $new_instance, $old_instance ){
        $instance = $old_instance;

        /** Update widget settings */
        $instance['title']          = $new_instance['title'];
        $instance['show_alpha']     = $new_instance['show_alpha'];
        $instance['show_beta']      = $new_instance['show_beta'];
        $instance['show_rc']        = $new_instance['show_rc'];
        $instance['show_stable']    = $new_instance['show_stable'];
        $instance['only_admin']     = $new_instance['only_admin'];

        return $instance;

    } /** End function - update */


    /**
     * Over writes form; displays widget options form
     *
     * @package BNS_Early_Adopter
     * @since   0.1
     *
     * @param   array $instance - widget options
     *
     * @uses    checked
     * @uses    get_field_id
     *
     * @return string|void
     *
     * @version 0.3
     * Added release candidate option
     */
    function form( $instance ){
        /** Set default widget values */
        $defaults = array(
            'title'         => __( 'Early Adopter', 'bns-ea' ),
            'show_alpha'    => '',
            'show_beta'     => '',
            'show_rc'       => '',
            'show_stable'   => '',
            'only_admin'    => '',
        );
        $instance = wp_parse_args( (array) $instance, $defaults ); ?>

        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'bns-ea' ); ?></label>
            <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked( (bool) $instance['show_alpha'], true ); ?> id="<?php echo $this->get_field_id( 'show_alpha' ); ?>" name="<?php echo $this->get_field_name( 'show_alpha' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'show_alpha' ); ?>"><?php _e( 'Show Alpha?', 'bns-ea' ); ?></label>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked( (bool) $instance['show_beta'], true ); ?> id="<?php echo $this->get_field_id( 'show_beta' ); ?>" name="<?php echo $this->get_field_name( 'show_beta' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'show_beta' ); ?>"><?php _e( 'Show Beta?', 'bns-ea' ); ?></label>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked( (bool) $instance['show_rc'], true ); ?> id="<?php echo $this->get_field_id( 'show_rc' ); ?>" name="<?php echo $this->get_field_name( 'show_rc' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'show_rc' ); ?>"><?php _e( 'Show Release Candidate?', 'bns-ea' ); ?></label>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked( (bool) $instance['show_stable'], true ); ?> id="<?php echo $this->get_field_id( 'show_stable' ); ?>" name="<?php echo $this->get_field_name( 'show_stable' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'show_stable' ); ?>"><?php _e( 'Show Stable?', 'bns-ea' ); ?></label>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked( (bool) $instance['only_admin'], true ); ?> id="<?php echo $this->get_field_id( 'only_admin' ); ?>" name="<?php echo $this->get_field_name( 'only_admin' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'only_admin' ); ?>"><?php _e( 'Only Show Administrators?', 'bns-ea' ); ?></label>
        </p>

        <hr />
        <p>
            <?php _e( 'NB: If no version is checked, or no matching version is found, the widget will not display.', 'bns-ea' ); ?>
        </p>

    <?php } /** End function - form */


    /**
     * BNSEA Shortcode
     *
     * @package BNS_Early_Adopter
     * @since   0.2
     *
     * @param   $atts
     *
     * @uses    shortcode_atts
     * @uses    the_widget
     *
     * @return  string
     *
     * @version 0.3
     * Added release candidate option
     *
     * @version 0.5
     * @date    November 26, 2012
     * Optimized output buffer code
     */
    function bnsea_shortcode( $atts ) {
        /** Get ready to capture the elusive widget output */
        ob_start();
        the_widget( 'BNS_Early_Adopter_Widget',
            $instance = shortcode_atts( array(
                'title'         => '',
                'show_alpha'    => '',
                'show_beta'     => '',
                'show_rc'       => '',
                'show_stable'   => '',
                'only_admin'    => ''
            ), $atts ),
            $args = array(
                /** clear variables defined by theme for widgets */
                $before_widget  = '',
                $after_widget   = '',
                $before_title   = '',
                $after_title    = '',
            )
        );
        /** Get the_widget output and put into its own container */
        $bnsea_content = ob_get_clean();

        $bnsea_content = '<div class="bnsea-shortcode">' . $bnsea_content . '</div>';

        return $bnsea_content;

    } /** End function - shortcode */


    /**
     * Register BNS Early Adopter Widget
     *
     * @package BNS_Early_Adopter
     * @since   0.1
     *
     * @uses    register_widget
     */
    function load_bnsea_widget() {
        register_widget( 'BNS_Early_Adopter_Widget' );
    } /** End function - load widget */


} /** End class */


/** @var $bnsea - instantiate the class */
$bnsea = new BNS_Early_Adopter_Widget();