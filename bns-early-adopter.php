<?php
/*
Plugin Name: BNS Early Adopter
Plugin URI: http://buynowshop.com/plugins/bns-early-adopter
Description: Show off you are an early adopter of WordPress (alpha, beta, release candidate, and/or stable versions)
Version: 1.1
TextDomain: bns-early-adopter
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
 * @package        BNS_Early_Adopter
 * @version        1.1
 *
 * @link           http://buynowshop.com/plugins/bns-early-adopter/
 * @link           https://github.com/Cais/bns-early-adopter/
 * @link           http://wordpress.org/extend/plugins/bns-early-adopter/
 *
 * @author         Edward Caissie <edward.caissie@gmail.com>
 * @copyright      Copyright (c) 2012-2015, Edward Caissie
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
 * @version        1.0
 * @date           May 2015
 */
class BNS_Early_Adopter_Widget extends WP_Widget {

	/**
	 * BNS Early Adopter Widget / Constructor
	 * Extends the WP_Widget class and adds other related functionality
	 *
	 * @package     BNS_Early_Adopter
	 * @since       0.1
	 *
	 * @internal    Requires WordPress version 3.6
	 * @internal    @uses shortcode_atts with optional shortcode filter parameter
	 *
	 * @uses        (CONSTANT) WP_CONTENT_DIR
	 * @uses        (GLOBAL) $wp_version
	 * @uses        (CLASS)  WP_Widget
	 * @uses        __
	 * @uses        add_action
	 * @uses        add_shortcode
	 * @uses        content_url
	 *
	 * @version     0.8
	 * @date        May 3, 2014
	 * Define location for BNS plugin customizations
	 *
	 * @version     0.9
	 * @date        November 8, 2014
	 * Added sanity checks for `BNS_CUSTOM_*` define statements
	 */
	function __construct() {

		/**
		 * WordPress version compatibility
		 * Check installed WordPress version for compatibility
		 */
		global $wp_version;
		$exit_message = __( 'BNS Early Adopter requires WordPress version 3.6 or newer.', 'bns-early-adopter' )
		                . ' '
		                . sprintf( '<a href="http://codex.wordpress.org/Upgrading_WordPress">%1$s</a>', __( 'Please Update!', 'bns-early-adopter' ) );
		if ( version_compare( $wp_version, "3.6", "<" ) ) {
			exit ( $exit_message );
		}

		/** Add Scripts and Styles */
		add_action(
			'wp_enqueue_scripts', array(
				$this,
				'scripts_and_styles'
			)
		);

		/** Widget Settings */
		$widget_ops = array(
			'classname'   => 'bns-early-adopter',
			'description' => __( 'White knuckling your active WordPress version? Show it off!', 'bns-early-adopter' )
		);

		/** Widget Control Settings */
		$control_ops = array(
			'width'   => '200',
			'id_base' => 'bns-early-adopter'
		);

		/** Create the Widget */
		parent::__construct( 'bns-early-adopter', 'BNS Early Adopter', $widget_ops, $control_ops );

		/** Define location for BNS plugin customizations */
		if ( ! defined( 'BNS_CUSTOM_PATH' ) ) {
			define( 'BNS_CUSTOM_PATH', WP_CONTENT_DIR . '/bns-customs/' );
		}
		if ( ! defined( 'BNS_CUSTOM_URL' ) ) {
			define( 'BNS_CUSTOM_URL', content_url( '/bns-customs/' ) );
		}

		/** Add Shortcode */
		add_shortcode( 'bnsea', array( $this, 'bnsea_shortcode' ) );

		/** Add widget */
		add_action( 'widgets_init', array( $this, 'load_bnsea_widget' ) );

		/** Add i18n support */
		load_plugin_textdomain( 'bns-early-adopter' );

	}


	/**
	 * Enqueue Plugin Scripts and Styles
	 * Adds plugin stylesheet (and scripts); also allows for custom stylesheet
	 * to be added by end-user.
	 *
	 * @package    BNS_Early_Adopter
	 * @since      0.1
	 *
	 * @uses       BNS_Early_Adopter::plugin_data
	 * @uses       plugin_dir_url
	 * @uses       plugin_dir_path
	 * @uses       wp_enqueue_style
	 *
	 * @version    0.4.2
	 * @date       August 2, 2012
	 * Programmatically add version number to enqueue calls
	 *
	 * @version    0.6
	 * @date       December 13, 2012
	 * Renamed to `scripts_and_styles`
	 *
	 * @version    0.8
	 * @date       May 3, 2014
	 * Apply `plugin_data` method
	 * Move to use generic folder for all "BNS" plugins to use
	 */
	function scripts_and_styles() {

		/** @var object $bnsea_data - contains plugin header data */
		$bnsea_data = $this->plugin_data();

		/** Enqueue Scripts */
		/** Enqueue Style Sheets */
		wp_enqueue_style( 'BNSEA-Style', plugin_dir_url( __FILE__ ) . 'bnsea-style.css', array(), $bnsea_data['Version'], 'screen' );

		/** This location is not recommended as it is not upgrade safe */
		if ( is_readable( plugin_dir_path( __FILE__ ) . 'bnsea-custom-style.css' ) ) {
			wp_enqueue_style( 'BNSEA-Custom-Style', plugin_dir_url( __FILE__ ) . 'bnsea-custom-style.css', array(), $bnsea_data['Version'], 'screen' );
		}

		/** Move to use generic folder for all "BNS" plugins to use */
		if ( is_readable( BNS_CUSTOM_PATH . 'bnsea-custom-style.css' ) ) {
			wp_enqueue_style( 'BNSEA-Custom-Style', BNS_CUSTOM_URL . 'bnsea-custom-style.css', array(), $bnsea_data['Version'], 'screen' );
		}

	}


	/**
	 * The Widget Itself ...
	 * ... lets get down to business.
	 *
	 * @package          BNS_Early_Adopter
	 * @since            0.1
	 *
	 * @param   $args
	 * @param   $instance
	 *
	 * @uses             (GLOBAL) wp_version
	 * @uses             BNS_Early_Adopter_Widget::bnsea_display
	 * @uses             __
	 * @uses             _x
	 * @uses             apply_filters
	 * @uses             current_user_can
	 * @uses             is_user_logged_in
	 *
	 * @version          0.7
	 * @date             July 14, 2013
	 * Corrected Administrator Only conditional and added admin only classes
	 *
	 * @version          0.9
	 * @date             November 8, 2014
	 * Corrected i18n code for 'Administrator ONLY View' text
	 */
	function widget( $args, $instance ) {

		/** Get widget setting values */
		extract( $args );

		/** Get user selected values */
		$title       = apply_filters( 'widget_title', $instance['title'] );
		$show_alpha  = $instance['show_alpha'];
		$show_beta   = $instance['show_beta'];
		$show_rc     = $instance['show_rc'];
		$show_stable = $instance['show_stable'];
		$only_admin  = $instance['only_admin'];

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
		for ( $i = 1; $i < strlen( $version_string ); $i ++ ) {
			if ( 'a' == substr( $version_string, $i, 1 )
			     || 'b' == substr( $version_string, $i, 1 )
			     || 'r' == substr( $version_string, $i, 1 )
			) {

				/** @var    string $test_character - used to assign $ea_version string */
				$test_character = substr( $version_string, $i, 1 );
				if ( 'a' == $test_character ) {
					/** @var string $ea_version - if 'a' is found then set to alpha */
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
			}

		}

		/**
		 * If all options are off BNS_Early_Adopter_Widget::bnsea_display will
		 * return false and the widget should not display anything
		 */
		if ( ! $this->bnsea_display( $instance, $ea_version ) ) {
			echo '<div class="bnsea-no-show">';
		}

		/** Conditional check - only show Administrators */
		if ( $only_admin ) {

			if ( is_user_logged_in() && current_user_can( 'activate_plugins' ) ) {

				echo '<div class="bnsea-admin-only">';
				$bnsea_admin_only_text_output = apply_filters(
					'bnsea_admin_only_text',
					sprintf( '<span class="bnsea-admin-only-text">%1$s</span>', __( 'Administrator ONLY View', 'bns-early-adopter' ) )
				);
				echo $bnsea_admin_only_text_output;

			} else {

				echo '<div class="bnsea-no-show">';

			}

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
		 * Get fancy here and write the output with correct grammar
		 */
		if ( ( 'alpha' == $ea_version ) && ( $show_alpha ) ) {

			$output = apply_filters( 'bnsea_alpha_text', sprintf( _x( "We are running an %s version of WordPress!", '%s is a PHP replacement variable', 'bns-early-adopter' ), $ea_version ) );

		} elseif ( ( ( 'beta' == $ea_version ) && $show_beta )
		           || ( ( 'stable' == $ea_version ) && $show_stable )
		           || ( ( 'release candidate' == $ea_version ) && $show_rc )
		) {

			$output = apply_filters( 'bnsea_beta_text', sprintf( _x( "We are running a %s version of WordPress!", '%s is a PHP replacement variable', 'bns-early-adopter' ), $ea_version ) );

		} else {

			/** @var null $output - widgets must have output of some sort or the Gods of programming will rain hellfire down on your code */
			$output = '';

		}

		/** @var string $output - data to be displayed with a little CSS for added dressing */
		$output = '<h3 class="bnsea-output">' . $output . '</h3>';

		echo apply_filters( 'bns_ea', $output, $args );

		/** @var    $after_widget   string - defined by theme */
		echo $after_widget;

		/** Conditional check - only show Administrators */
		if ( $only_admin ) {
			echo '</div><!-- bnsea-no-show -->';
		}

		/**
		 * If all options are off BNS_Early_Adopter_Widget::bnsea_display will
		 * return false and the widget should not display anything
		 */
		if ( ! $this->bnsea_display( $instance, $ea_version ) ) {
			echo '</div><!-- bnsea-no-show -->';
		}

	}


	/**
	 * Early Adopter Display
	 *
	 * Returns string value to dictate if widget should be displayed or not.
	 * Widget will not display by default.
	 *
	 * @package BNS_Early_Adopter
	 * @since   0.2
	 *
	 * @param   array  $instance   - widget options
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
	function bnsea_display( $instance, $ea_version ) {

		if ( ( $instance['show_alpha'] && ( 'alpha' == $ea_version ) )
		     || ( $instance['show_beta'] && ( 'beta' == $ea_version ) )
		     || ( $instance['show_rc'] && ( 'release candidate' == $ea_version ) )
		     || ( $instance['show_stable'] && ( 'stable' == $ea_version ) )
		) {

			$ea_display = true;

		} else {

			$ea_display = false;

		}

		return $ea_display;

	}


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
	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		/** Update widget settings */
		$instance['title']       = $new_instance['title'];
		$instance['show_alpha']  = $new_instance['show_alpha'];
		$instance['show_beta']   = $new_instance['show_beta'];
		$instance['show_rc']     = $new_instance['show_rc'];
		$instance['show_stable'] = $new_instance['show_stable'];
		$instance['only_admin']  = $new_instance['only_admin'];

		return $instance;

	}


	/**
	 * Over writes form; displays widget options form
	 *
	 * @package BNS_Early_Adopter
	 * @since   0.1
	 *
	 * @param   array $instance - widget options
	 *
	 * @uses    WP_Widget::get_field_id
	 * @uses    WP_Widget::get_field_name
	 * @uses    _e
	 * @uses    _x
	 * @uses    checked
	 * @uses    wp_parse_args
	 *
	 * @return string|void
	 *
	 * @version 0.3
	 * Added release candidate option
	 */
	function form( $instance ) {

		/** Set default widget values */
		$defaults = array(
			'title'       => _x( 'Early Adopter', 'used as a title', 'bns-early-adopter' ),
			'show_alpha'  => '',
			'show_beta'   => '',
			'show_rc'     => '',
			'show_stable' => '',
			'only_admin'  => '',
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'bns-early-adopter' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>"
			       value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['show_alpha'], true ); ?>
			       id="<?php echo $this->get_field_id( 'show_alpha' ); ?>"
			       name="<?php echo $this->get_field_name( 'show_alpha' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_alpha' ); ?>"><?php _e( 'Show Alpha?', 'bns-early-adopter' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['show_beta'], true ); ?>
			       id="<?php echo $this->get_field_id( 'show_beta' ); ?>"
			       name="<?php echo $this->get_field_name( 'show_beta' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_beta' ); ?>"><?php _e( 'Show Beta?', 'bns-early-adopter' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['show_rc'], true ); ?>
			       id="<?php echo $this->get_field_id( 'show_rc' ); ?>"
			       name="<?php echo $this->get_field_name( 'show_rc' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'show_rc' ); ?>"><?php _e( 'Show Release Candidate?', 'bns-early-adopter' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['show_stable'], true ); ?>
			       id="<?php echo $this->get_field_id( 'show_stable' ); ?>"
			       name="<?php echo $this->get_field_name( 'show_stable' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'show_stable' ); ?>"><?php _e( 'Show Stable?', 'bns-early-adopter' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['only_admin'], true ); ?>
			       id="<?php echo $this->get_field_id( 'only_admin' ); ?>"
			       name="<?php echo $this->get_field_name( 'only_admin' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'only_admin' ); ?>"><?php _e( 'Only Show Administrators?', 'bns-early-adopter' ); ?></label>
		</p>

		<hr />
		<p>
			<?php _e( 'NB: If no version is checked, or no matching version is found, the widget will not display.', 'bns-early-adopter' ); ?>
		</p>

	<?php }


	/**
	 * BNSEA Shortcode
	 *
	 * @package    BNS_Early_Adopter
	 * @since      0.2
	 *
	 * @param   $atts
	 *
	 * @uses       shortcode_atts
	 * @uses       the_widget
	 *
	 * @return  string
	 *
	 * @version    0.3
	 * Added release candidate option
	 *
	 * @version    0.5
	 * @date       November 26, 2012
	 * Optimized output buffer code
	 *
	 * @version    0.8
	 * @date       May 3, 2014
	 * Added optional shortcode_atts filter variable
	 */
	function bnsea_shortcode( $atts ) {

		/** Get ready to capture the elusive widget output */
		ob_start();
		the_widget(
			'BNS_Early_Adopter_Widget',
			$instance = shortcode_atts(
				array(
					'title'       => '',
					'show_alpha'  => '',
					'show_beta'   => '',
					'show_rc'     => '',
					'show_stable' => '',
					'only_admin'  => ''
				), $atts, 'bnsea'
			),
			$args = array(
				/** clear variables defined by theme for widgets */
				$before_widget = '',
				$after_widget = '',
				$before_title = '',
				$after_title = '',
			)
		);
		/** Get the_widget output and put into its own container */
		$bnsea_content = ob_get_clean();

		/** @var string $bnsea_content - wrapped in its own class for CSS specificity */
		$bnsea_content = '<div class="bnsea-shortcode">' . $bnsea_content . '</div>';

		return $bnsea_content;

	}


	/**
	 * Plugin Data
	 * Returns the plugin header data as an array
	 *
	 * @package    BNS_Early_Adopter
	 * @since      0.8
	 *
	 * @uses       get_plugin_data
	 *
	 * @return array
	 */
	function plugin_data() {

		/** Call the wp-admin plugin code */
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		/** @var $plugin_data - holds the plugin header data */
		$plugin_data = get_plugin_data( __FILE__ );

		return $plugin_data;

	}


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
	}


}


/** @var $bnsea - instantiate the class */
$bnsea = new BNS_Early_Adopter_Widget();


/**
 * BNS Early Adopter Update Message
 *
 * @package BNS_Early_Adopter
 * @since   1.0
 *
 * @uses    get_transient
 * @uses    is_wp_error
 * @uses    set_transient
 * @uses    wp_kses_post
 * @uses    wp_remote_get
 *
 * @param $args
 */
function bnsea_in_plugin_update_message( $args ) {

	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	$bnsea_data = get_plugin_data( __FILE__ );

	$transient_name = 'bnsea_upgrade_notice_' . $args['Version'];
	if ( false === ( $upgrade_notice = get_transient( $transient_name ) ) ) {

		/** @var string $response - get the readme.txt file from WordPress */
		$response = wp_remote_get( 'https://plugins.svn.wordpress.org/bns-early-adopter/trunk/readme.txt' );

		if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {
			$matches = null;
		}
		$regexp         = '~==\s*Changelog\s*==\s*=\s*(.*)\s*=(.*)(=\s*' . preg_quote( $bnsea_data['Version'] ) . '\s*=|$)~Uis';
		$upgrade_notice = '';

		if ( preg_match( $regexp, $response['body'], $matches ) ) {
			$version = trim( $matches[1] );
			$notices = (array) preg_split( '~[\r\n]+~', trim( $matches[2] ) );

			if ( version_compare( $bnsea_data['Version'], $version, '<' ) ) {

				/** @var string $upgrade_notice - start building message (inline styles) */
				$upgrade_notice = '<style type="text/css">
							.bnsea_plugin_upgrade_notice { padding-top: 20px; }
							.bnsea_plugin_upgrade_notice ul { width: 50%; list-style: disc; margin-left: 20px; margin-top: 0; }
							.bnsea_plugin_upgrade_notice li { margin: 0; }
						</style>';

				/** @var string $upgrade_notice - start building message (begin block) */
				$upgrade_notice .= '<div class="bnsea_plugin_upgrade_notice">';

				$ul = false;

				foreach ( $notices as $index => $line ) {

					if ( preg_match( '~^=\s*(.*)\s*=$~i', $line ) ) {

						if ( $ul ) {
							$upgrade_notice .= '</ul><div style="clear: left;"></div>';
						}
						/** End if - unordered list created */

						$upgrade_notice .= '<hr/>';
						continue;

					}
					/** End if - non-blank line */

					/** @var string $return_value - body of message */
					$return_value = '';

					if ( preg_match( '~^\s*\*\s*~', $line ) ) {

						if ( ! $ul ) {
							$return_value = '<ul">';
							$ul           = true;
						}
						/** End if - unordered list not started */

						$line = preg_replace( '~^\s*\*\s*~', '', htmlspecialchars( $line ) );
						$return_value .= '<li style=" ' . ( $index % 2 == 0 ? 'clear: left;' : '' ) . '">' . $line . '</li>';

					} else {

						if ( $ul ) {
							$return_value = '</ul><div style="clear: left;"></div>';
							$return_value .= '<p>' . $line . '</p>';
							$ul = false;
						} else {
							$return_value .= '<p>' . $line . '</p>';
						}
						/** End if - unordered list started */

					}
					/** End if - non-blank line */

					$upgrade_notice .= wp_kses_post( preg_replace( '~\[([^\]]*)\]\(([^\)]*)\)~', '<a href="${2}">${1}</a>', $return_value ) );

				}
				/** End foreach - line parsing */

				$upgrade_notice .= '</div>';

			}
			/** End if - version compare */

		}
		/** End if - response message exists */

		/** Set transient - minimize calls to WordPress */
		set_transient( $transient_name, $upgrade_notice, DAY_IN_SECONDS );

	}
	/** End if - transient check */

	echo $upgrade_notice;

}

/** End function - in plugin update message */
add_action( 'in_plugin_update_message-' . plugin_basename( __FILE__ ), 'bnsea_in_plugin_update_message' );