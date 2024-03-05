<?php

/**
 *
 * @wordpress-plugin
 * Plugin Name:       UBC CLF Whitney webfont
 * Plugin URI:        http://clf.ubc.ca
 * Description:       Add CLF Whitney webfont CSS request. <strong>Note: Required registration</strong>. Please sign up on <a href="http://brand.ubc.ca/font-request-form/" target="_blank">UBC Brand</a> website.
 * Version:           1.1
 * Author:            Flynn O'Connor & Michael Kam
 * Author URI:
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ubc-clf-whitney
 */

class UBC_CLF_Whitney {

	/**
	 * Initialize the plugin
	 */
	public function init() {
		// Add custom UBC CLF Whitney
		add_action( 'init', array( $this, 'init__add_clf_whitney' ) );

		add_action( 'admin_init', array( $this, 'ubc_clf_render_whitney_settings' ) );
		add_filter( 'ubc_collab_default_theme_options', array( __CLASS__, 'ubc_clf_default_values' ), 10, 1 );
		add_filter( 'ubc_collab_theme_options_validate', array( __CLASS__, 'cfm_whitney_validate' ), 10, 2 );
	}

	/**
	 * Enqueue UBC CLF Whitney CSS
	 */
	public function init__add_clf_whitney() {
		$source = UBC_Collab_Theme_Options::get( 'clf-whitney-source' );
		if ( $source && $source === 'development' ) {
			// Load development
			wp_enqueue_style( 'ubc-clf-whitney', 'https://cloud.typography.com/6804272/697806/css/fonts.css' );
		} else {
			// If the option has not been set or it is not set to development load production css.
			wp_enqueue_style( 'ubc-clf-whitney', 'https://cloud.typography.com/6804272/781004/css/fonts.css' );
		}
	}

	/**
	 * Render the Whitney Font Source settings section
	 */
	public function ubc_clf_whitney_settings_section() {

		?>
		<fieldset>
		<?php
		foreach ( self::ubc_clf_whitney_source_options() as $radio ) {
			$label = $radio['label'];
			?>
				<div class="radio-wrapper">
			<?php UBC_Collab_Theme_Options::radio( 'clf-whitney-source', $radio['value'], $label ); ?>
				</div>
				<?php
		}
		?>
			 
			<p class="description">Select whether the website is for development/testing or production purposes.</p>
		</fieldset>
		<?php

	}

	/**
	 * Add Whitney Font Source settings field
	 */
	public function ubc_clf_render_whitney_settings() {
		add_settings_field(
			'whitney-source',
			'Whitney Font Source',
			array( $this, 'ubc_clf_whitney_settings_section' ),
			'theme_options', // Replace with your settings page
			'clf'
		);
	}
	static function ubc_clf_default_values( $options ) {

		if ( ! is_array( $options ) ) {
			$options = array();
		}

		$defaults = array(
			'clf-whitney-source' => 'production',
		);

		$options = array_merge( $options, $defaults );

		return $options;
	}
	/**
	 * Define Whitney Font Source options
	 */
	public static function ubc_clf_whitney_source_options() {
		return array(
			'production'  => array(
				'value' => 'production',
				'label' => __( 'Production Whitney', 'ubc-clf' ),
			),
			'development' => array(
				'value' => 'development',
				'label' => __( 'Development Whitney', 'ubc-clf' ),
			),
		);
	}

	/**
	 * Validate form data
	 */
	public static function cfm_whitney_validate( $output, $input ) {

		// Grab default values as base
		$starter = UBC_Full_Width_Theme_Options::default_values( array() );

		// Validate Colour Theme
		if ( isset( $input['clf-whitney-source'] ) && array_key_exists( $input['clf-whitney-source'], self::ubc_clf_whitney_source_options() ) ) {
			$starter['clf-whitney-source'] = $input['clf-whitney-source'];
		}

		$output = array_merge( $output, $starter );

		return $output;
	}
}

/**
 * Initialize the plugin on plugins_loaded hook
 */
function plugins_loaded__init_ubcclfwhitney() {
	$clf_whitney = new UBC_CLF_Whitney();
	$clf_whitney->init();
}

add_action( 'plugins_loaded', 'plugins_loaded__init_ubcclfwhitney' );
