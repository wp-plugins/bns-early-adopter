<?php
/*
Plugin Name: BNS Early Adopter
Plugin URI: http://buynowshop.com/plugins/bns-early-adopter
Description: Show off you are an early adopter of WordPress (alpha and/or beta versions)
Version: 0.1
TextDomain: bns-ea
Author: Edward Caissie
Author URI: http://edwardcaissie.com/
License: GNU General Public License v2
License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

/**
 * BNS Early Adopter WordPress plugin
 *
 * Show off you are an early adopter of WordPress (alpha and/or beta versions)
 *
 * @package     BNS_Early_Adopter
 * @link        http://buynowshop.com/plugins/bns-early-adopter/
 * @link        https://github.com/Cais/bns-early-adopter/
 * @link        http://wordpress.org/extend/plugins/bns-early-adopter/
 * @version     0.1-alpha
 * @author      Edward Caissie <edward.caissie@gmail.com>
 * @copyright   Copyright (c) 2012, Edward Caissie
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
 * Last revised March 6, 2012
 * Initial Release
 * @todo Tune up 'readme.txt'
 * @todo Add shortcode support
 * @todo Add only show administrators option
 */

/**
 * Check installed WordPress version for compatibility
 * @todo Verify and reference which WordPress version is required and due to what function call
 */
global $wp_version;
$exit_message = 'BNS Early Adopter requires WordPress version 3.0 or newer. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please Update!</a>';
if ( version_compare( $wp_version, "3.0", "<" ) )
    exit ( $exit_message );

/**
 * BNS Early Adopter TextDomain
 * Make plugin text available for translation (i18n)
 *
 * @package:    BNS_Early_Adopter
 * @since:      0.1
 *
 * @internal translation files are expected to be found in the plugin root folder / directory.
 * @internal `bns-ea` is being used in place of `bns-early-adopter`
 */
load_plugin_textdomain( 'bns-ea' );
// End: BNS Early Adopter TextDomain

/**
 * Enqueue Plugin Scripts and Styles
 *
 * Adds plugin stylesheet and allows for custom stylesheet to be added by end-user.
 *
 * @package BNS_Early_Adopter
 * @since   0.1
 */
function BNSEA_Scripts_and_Styles() {
        /** Enqueue Scripts */
        /** Enqueue Style Sheets */
        wp_enqueue_style( 'BNSEA-Style', plugin_dir_url( __FILE__ ) . 'bnsea-style.css', array(), '0.1', 'screen' );
        if ( is_readable( plugin_dir_path( __FILE__ ) . 'bnsea-custom-style.css' ) ) {
            wp_enqueue_style( 'BNSEA-Custom-Style', plugin_dir_url( __FILE__ ) . 'bnsea-custom-style.css', array(), '0.1', 'screen' );
        }
}
add_action( 'wp_enqueue_scripts', 'BNSEA_Scripts_and_Styles' );

/** Register BNS Early Adopter Widget */
function load_bnsea_widget() {
        register_widget( 'BNS_Early_Adopter_Widget' );
}
add_action( 'widgets_init', 'load_bnsea_widget' );

class BNS_Early_Adopter_Widget extends WP_Widget {

    function BNS_Early_Adopter_Widget(){
        /** Widget Settings */
        $widget_ops = array( 'classname' => 'bns-early-adopter', 'description' => __( 'White knuckling your active WordPress version? Show it off!', 'bns-ea' ) );

        /** Widget Control Settings */
        $control_ops = array( 'width' => '200', 'id_base' => 'bns-early-adopter' );

        /** Create the Widget */
        $this->WP_Widget( 'bns-early-adopter', 'BNS Early Adopter', $widget_ops, $control_ops );
    }

    /**
     * The Widget Itself ...
     * ... lets get down to business.
     *
     * @param   $args
     * @param   $instance
     */
    function widget( $args, $instance ){
        /** Get widget setting values */
        extract( $args );

        /** Get user selected values */
        $title          = apply_filters( 'widget_title', $instance['title'] );
        $show_alpha     = $instance['show_alpha'];
        $show_beta      = $instance['show_beta'];
        $show_stable    = $instance['show_stable'];

        /** Test options via echo */
        // echo 'Alpha: ' . $show_alpha . ' ';
        // echo 'Beta: ' . $show_beta . ' ';
        // echo 'Stable: ' . $show_stable . ' ';
        /** End: Test options via echo */

        /**
         * The secret sauce of this whole sordid affair ...
         *
         * Get the global version and put it into an all lower case string
         */
        global $wp_version;
        $version_string = strtolower( $wp_version );
        $ea_version = '';

        /** Step through the version string looking for an "a" for alpha, or a "b" for beta */
        for ( $i = 1; $i < strlen( $version_string ); $i++ ) {
            if ( 'a' == substr( $version_string, $i, 1 ) || 'b' == substr( $version_string, $i, 1 ) ) {
                /** @var    string $test_character - used to assign $ea_version string  */
                $test_character = substr( $version_string, $i, 1 );
                if ( 'a' == $test_character ) {
                    /** @var string $ea_version - if 'a' is found then set to alpha  */
                    $ea_version = 'alpha';
                    // echo $ea_version;
                } elseif ( 'b' == $test_character ) {
                    /** @var string $ea_version - if 'b' is found then set to beta */
                    $ea_version = 'beta';
                    // echo $ea_version;
                } else {
                    /** No 'a' or 'b' found must be some other nonsense or a 'public release' version */
                    $ea_version = 'stable';
                    // echo $ea_version;
                }
                /** @var number $i - if the `test_character` was found end the for loop by forcing the index value to its maximum */
                $i = strlen( $version_string );
            }
        }

        /**
         * Conditional check - if all options are off do not display widget
         * @todo Sort out other combinations when the widget should not be displayed
         */
        if ( ( ! $show_alpha ) && ( ! $show_beta ) && ( ! $show_stable ) ) {
            echo '<div class="bnsea-no-show">';
        }

        /** @var    $before_widget  string - defined by theme */
        echo $before_widget;

        /** Widget $title, $before_widget, and $after_widget defined by theme */
        if ( $title ) {
            /**
             * @var $before_title   string - defined by theme
             * @var $after_title    string - defined by theme
             */
            echo $before_title . $title . $after_title;
        }

        /**
         * The secret sauce of this whole sordid affair ...
         *
         * Get the global version and put it into an all lower case string
         */
        global $wp_version;
        $version_string = strtolower( $wp_version );
        $ea_version = '';

        /** Step through the version string looking for an "a" for alpha, or a "b" for beta */
        for ( $i = 1; $i < strlen( $version_string ); $i++ ) {
            if ( 'a' == substr( $version_string, $i, 1 ) || 'b' == substr( $version_string, $i, 1 ) ) {
                /** @var    string $test_character - used to assign $ea_version string  */
                $test_character = substr( $version_string, $i, 1 );
                if ( 'a' == $test_character ) {
                    /** @var string $ea_version - if 'a' is found then set to alpha  */
                    $ea_version = 'alpha';
                    // echo $ea_version;
                } elseif ( 'b' == $test_character ) {
                    /** @var string $ea_version - if 'b' is found then set to beta */
                    $ea_version = 'beta';
                    // echo $ea_version;
                } else {
                    /** No 'a' or 'b' found must be some other nonsense or a 'public release' version */
                    $ea_version = 'stable';
                    // echo $ea_version;
                }
                /** @var number $i - if the `test_character` was found end the for loop by forcing the index value to its maximum */
                $i = strlen( $version_string );
            }
        }

        /**
         * Get fancy here and write the output with correct grammar
         * @todo Clean up these conditionals ...
         */
        if ( ( 'alpha' == $ea_version ) && ( $show_alpha ) ) {
            $output = sprintf( __( "We are running an %s version of WordPress!", 'bns-ea' ), $ea_version );
        } elseif ( ( 'beta' == $ea_version && $show_beta ) ) {
            $output = sprintf( __( "We are running a %s version of WordPress!", 'bns-ea' ), $ea_version );
        } elseif ( ( 'stable' == $ea_version && $show_stable ) ) {
            $output = sprintf( __( "We are running a %s version of WordPress!", 'bns-ea' ), $ea_version );
        } else {
            /** @var null $output - widgets must have output of some sort or the Gods of programming will rain hellfire down on your code */
            $output = '';
        }
        /** @var string $output - data to be displayed with a little CSS for added dressing */
        $output = '<h3 class="bnsea-output">' . $output . '</h3>';

        echo apply_filters( 'bns_ea', $output, $args );

        /** @var    $after_widget   string - defined by theme */
        echo $after_widget;

        if ( ( ! $show_alpha ) && ( ! $show_beta ) && ( ! $show_stable ) ) {
            echo '</div>';
        } /** End: Conditional check for all options off */

    }

    /**
     * Over writes update; maintains widget settings when mutliple instances exist
     *
     * @param $new_instance
     * @param $old_instance
     *
     * @return array $instance - updated widget settings
     */
    function update( $new_instance, $old_instance ){
        $instance = $old_instance;

        /** Update widget settings */
        $instance['title']      = $new_instance['title'];
        $instance['show_alpha'] = $new_instance['show_alpha'];
        $instance['show_beta']  = $new_instance['show_beta'];
        $instance['show_stable']  = $new_instance['show_stable'];

        return $instance;
    }

    /**
     * Over writes form; displays widget options form
     *
     * @package BNS_Early_Adopter
     * @since   0.1
     *
     * @param   array $instance - widget options
     */
    function form( $instance ){
        /** Set default widget values */
        $defaults = array(
            'title'         => __( 'Early Adopter', 'bns-ea' ),
            'show_alpha'    => '',
            'show_beta'     => '',
            'show_stable'   => '',
            );
        $instance = wp_parse_args( (array) $instance, $defaults );
        ?>
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
            <input class="checkbox" type="checkbox" <?php checked( (bool) $instance['show_stable'], true ); ?> id="<?php echo $this->get_field_id( 'show_stable' ); ?>" name="<?php echo $this->get_field_name( 'show_stable' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'show_stable' ); ?>"><?php _e( 'Show Stable?', 'bns-ea' ); ?></label>
        </p>
    <?php }
}
?>