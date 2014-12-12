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

		$settings_sections['pages-fields'] 		= __( 'General', 'features-by-woothemes' );

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

					echo '<a href="' . admin_url( 'edit.php?post_type=project&page=features-settings-page&tab=' . $key ) . '" class="nav-tab' . $class . '">' . $value . '</a>';
				} // End For Loop
				?>
			</h2>
			<form action="options.php" method="post">

				<?php
				settings_fields( 'features-settings-' . $tab );
				do_settings_sections( 'features-' . $tab );
				?>

				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" />
				</p>

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
					register_setting( 'features-settings-' . $k, 'features-' . $k, array( $this, 'features_' . $callback_array[0] . '_settings_validate' ) );
					add_settings_section( $k, $v, array( $this, 'features_' . $callback_array[0] . '_settings' ), 'features-' . $k );
				} elseif( function_exists( 'features_' . $callback_array[0] . '_settings_validate' ) ) {
					register_setting( 'features-settings-' . $k, 'features-' . $k, 'features_' . $callback_array[0] . '_settings_validate' );
					add_settings_section( $k, $v, 'features_' . $callback_array[0] . '_settings', 'features-' . $k );
				} // End If Statement
			} // End For Loop
		} // End If Statement

	} // End features_options_init()

	public function features_pages_settings() {
		?>

		<p><?php _e( 'Configure Features plugin label.', 'features-by-woothemes' ); ?></p>

		<table class="form-table">
		<tbody>
		<tr valign="top">
			<th scope="row" class="titledesc"><?php _e( 'Plugin Label', 'features-by-woothemes' ) ?></th>
		    <td class="forminp">
				<table class="features_options widefat" cellspacing="0">
					<thead>
						<tr>
							<?php
								$columns = apply_filters( 'woothemes_features_label_setting_columns', array(
									'default'  => __( 'Default', 'features-by-woothemes' ),
									'name'     => __( 'Label', 'features-by-woothemes' )
								) );

								foreach ( $columns as $key => $column ) {
									echo '<th class="' . esc_attr( $key ) . '">' . esc_html( $column ) . '</th>';
								}
							?>
						</tr>
					</thead>
					<tbody>
						<?php
						$labels = array(
							'features' => 'Features',
							'services' => 'Services',
						);

						foreach ( $labels as $label_key => $label_value ) {

							echo '<tr>';

							foreach ( $columns as $key => $column ) {

								switch ( $key ) {

									case 'default' :
										echo '<td width="1%" class="default">
											<input type="radio" name="default_label" value="' . $label_key . '" />
										</td>';
									break;

									case 'name' :
										echo '<td class="name">
											' . $label_value . '
										</td>';
									break;

									default :
										do_action( 'woothemes_features_label_setting_column_' . $key, $gateway );
									break;
								}
							}

							echo '</tr>';
						}
						?>
					</tbody>
				</table>
			</td>
		</tr>
		</tbody>
		</table>
		<?php
	} // End features_pages_settings()

	/**
	 * features_pages_settings_validate validates pages settings form data
	 * @param  array $input array of form data
	 * @since  1.4.4
	 * @return array $input array of sanitized form data
	 */
	public function features_pages_settings_validate( $input ) {

		$input['features_page_id']				= absint( $input['features_page_id'] );

		return $input;
	} // End features_pages_settings_validate()

} // End Class

new Features_Settings();