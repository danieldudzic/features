<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * WooThemes Features Settings Class
 *
 * All functionality pertaining to the features settings.
 *
 * @package WordPress
 * @subpackage Features
 * @category Plugin
 * @author Danny
 * @since 1.4.4
 */
class Features_Settings {
	/**
	 * Constructor function.
	 *
	 * @access public
	 * @since 1.4.4
	 * @return void
	 */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'features_add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'features_options_init' ) );

	} // End __construct()

	public function features_add_settings_page() {
		add_submenu_page( 'edit.php?post_type=feature', __( 'Settings', 'features-by-woothemes' ), __( 'Settings', 'features-by-woothemes' ), 'publish_posts', 'features-settings-page', array( $this, 'features_settings_page' ) );
	} // End features_add_settings_page()

	/**
	 * Retrieve the settings fields details
	 * @access  public
	 * @since   1.4.4
	 * @return  array        Settings fields.
	 */
	public function get_settings_sections () {
		$settings_sections = array();

		$settings_sections['label'] = __( 'General', 'features-by-woothemes' );

		return (array)apply_filters( 'features_settings_sections', $settings_sections );
	} // End get_settings_sections()

	public function features_settings_page() {
		$sections = $this->get_settings_sections();
		if ( isset ( $_GET['tab'] ) ) {
			$tab = $_GET['tab'];
		} else {
			list( $first_section ) = array_keys( $sections );
			$tab = $first_section;
		} // End If Statement
		?>
		<div class="wrap">

			<h2 class="nav-tab-wrapper">
				<?php
				foreach ( $sections as $key => $value ) {
					$class = '';

					if ( $tab == $key ) {
						$class = ' nav-tab-active';
					} // End If Statement

					echo '<a href="' . admin_url( 'edit.php?post_type=feature&page=features-settings-page&tab=' . $key ) . '" class="nav-tab' . $class . '">' . $value . '</a>';
				} // End For Loop
				?>
			</h2>
			<form action="options.php" method="post">
				<?php
					settings_fields( 'features-settings-' . $tab );
					do_settings_sections( 'features-' . $tab );
				?>

				<?php submit_button(); ?>

			</form>

		</div>
		<?php
	} // End features_settings_page()

	public function features_options_init(){
		$sections = $this->get_settings_sections();
		if ( 0 < count( $sections ) ) {
			foreach ( $sections as $k => $v ) {
				$callback_array = explode( '-', $k );
				if ( method_exists( $this, 'features_' . $callback_array[0] . '_settings_validate' ) ) {
					register_setting( 'features-settings-' . $k, 'features_' . $k, array( $this, 'features_' . $callback_array[0] . '_settings_validate' ) );
					add_settings_section( $k, $v, array( $this, 'features_' . $callback_array[0] . '_settings' ), 'features-' . $k );
				} elseif( function_exists( 'features_' . $callback_array[0] . '_settings_validate' ) ) {
					register_setting( 'features-settings-' . $k, 'features_' . $k, 'features_' . $callback_array[0] . '_settings_validate' );
					add_settings_section( $k, $v, 'features_' . $callback_array[0] . '_settings', 'features-' . $k );
				} // End If Statement
			} // End For Loop
		} // End If Statement

	} // End features_options_init()

	public function features_label_settings() {
		?>

		<p><?php _e( 'Configure Features plugin label.', 'features-by-woothemes' ); ?></p>

		<?php

			$labels = array(
				'features' => 'Features',
				'services' => 'Services',
			);

			$labels = apply_filters( 'woothemes_features_labels', $labels );

		?>
		<table class="form-table features_options">
			<tbody>
			<?php

				$saved_label = get_option( 'features_label' );

				if ( empty ( $saved_label ) ) {
					$saved_label = 'features';
				}

				foreach ( $labels as $label_key => $label_value ) {

					if( $saved_label == $label_key ) {
						$checked = 'checked';
					} else {
						$checked = '';
					}

					echo '<tr>';
					echo '<th><label><input type="radio" value="' . $label_key . '" name="features_label" ' . $checked . '>' . $label_value . '</label></th>';
					echo '<td><code>' . get_site_url() . '/' . $label_key . '</code></td>';
					echo '</tr>';
				}
			?>
			</tbody>
		</table>

		<?php
	} // End features_label_settings()

	/**
	 * validates label settings form data
	 * @param  array $input array of form data
	 * @since  1.4.4
	 * @return array $input array of sanitized form data
	 */
	public function features_label_settings_validate( $input ) {
		return $input;
	} // End features_label_settings_validate()

} // End Class

new Features_Settings();