<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class Adsforwp_Admin_Settings {


	public function __construct() {

			add_action( 'admin_menu', array( $this, 'adsforwp_add_menu_links' ) );
			add_action( 'admin_init', array( $this, 'adsforwp_settings_init' ) );
			add_action( 'upload_mimes', array( $this, 'adsforwp_custom_upload_mimes' ) );
			add_filter( 'pre_update_option_adsforwp_settings', array( $this, 'adsforwp_pre_update_settings' ), 10, 3 );
	}
	public function adsforwp_add_menu_links() {
		// Settings page - Same as main menu page
		add_submenu_page(
			'edit.php?post_type=adsforwp',
			esc_html__( 'Ads for wp', 'ads-for-wp' ),
			esc_html__( 'Settings', 'ads-for-wp' ),
			'manage_options',
			'adsforwp',
			array( $this, 'adsforwp_admin_interface_render' )
		);

		if ( ! defined( 'ADSFORWP_PRO_VERSION' ) && current_user_can( 'manage_options' ) ) {

			add_submenu_page( 'edit.php?post_type=adsforwp', 'AdsForWP', '<span class="adsforwp-upgrade-to-pro">' . esc_html__( 'Upgrade To Premium', 'ads-for-wp' ) . '</span>', 'manage_options', 'adsforwp_data_premium', array( $this, 'adsforwp_premium_interface_render' ) );
		}
	}


	public function adsforwp_admin_interface_render() {
		// Authentication
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		// Handing save settings
     //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason : Only Checking if the form was submitted
		if ( isset( $_GET['settings-updated'] ) ) {

				$settings      = adsforwp_defaultSettings();
				$file_creation = new Adsforwp_File_Creation();

			if ( isset( $settings['ad_blocker_support'] ) ) {
				$file_creation->adsforwp_create_adblocker_support_js();
			} else {
				$file_creation->adsforwp_delete_adblocker_support_js();
			}

			settings_errors();
		}
			$tab = adsforwp_get_tab( 'general', array( 'general', 'support', 'tools', 'advance', 'pro' ) );

		?>
		<div>                         
			<h1 style="display:inline-block;"><?php echo esc_html__( 'Ads for WP Settings', 'ads-for-wp' ); ?></h1>
			<?php
			if ( ! defined( 'ADSFORWP_PRO_VERSION' ) ) {
				?>
			<div id="redux-intro-text"><a class="technical_support_btn_txt" href="https://www.adsforwp.com/contact/" target="_blank">Technical Support</a> <a class="premium_features_btn" href="https://www.adsforwp.com/pricing/#pricings" target="_blank"><?php echo esc_html__( 'Upgrade to PREMIUM', 'ads-for-wp' ); ?></a> </div>
			<?php } ?>
		</div>
		<h2 class="nav-tab-wrapper adsforwp-tabs">
		<?php
		echo '<a href="' . esc_url( adsforwp_admin_link( 'general' ) ) . '" class="nav-tab ' . esc_attr( $tab == 'general' ? 'nav-tab-active' : '' ) . '"><span class=""></span> ' . esc_html__( 'General', 'ads-for-wp' ) . '</a>';

						echo '<a href="' . esc_url( adsforwp_admin_link( 'tools' ) ) . '" class="nav-tab ' . esc_attr( $tab == 'tools' ? 'nav-tab-active' : '' ) . '"><span class=""></span> ' . esc_html__( 'Tools', 'ads-for-wp' ) . '</a>';

						echo '<a href="' . esc_url( adsforwp_admin_link( 'advance' ) ) . '" class="nav-tab ' . esc_attr( $tab == 'advance' ? 'nav-tab-active' : '' ) . '"><span class=""></span> ' . esc_html__( 'Advanced', 'ads-for-wp' ) . '</a>';

						echo '<a href="' . esc_url( adsforwp_admin_link( 'support' ) ) . '" class="nav-tab ' . esc_attr( $tab == 'support' ? 'nav-tab-active' : '' ) . '"><span class=""></span> ' . esc_html__( 'Support', 'ads-for-wp' ) . '</a>';
		if ( ! defined( 'ADSFORWP_PRO_VERSION' ) ) {
			echo '<a href="' . esc_url( adsforwp_admin_link( 'pro' ) ) . '" class="nav-tab afw-license-tab ' . esc_attr( $tab == 'pro' ? 'nav-tab-active' : '' ) . '"><span class=""></span> ' . esc_html__( 'Upgrade to  Premium', 'ads-for-wp' ) . '</a>';
		} else {
			echo '<a href="' . esc_url( adsforwp_admin_link( 'pro' ) ) . '" class="nav-tab afw-license-tab ' . esc_attr( $tab == 'pro' ? 'nav-tab-active' : '' ) . '"><span class=""></span> ' . esc_html__( 'License', 'ads-for-wp' ) . '</a>';
		}

		?>
		</h2>
				<form action="options.php" method="post" enctype="multipart/form-data" class="adsforwp-settings-form" id="adsforwp-settings-form">        
			<div class="form-wrap">
		<?php
		// Output nonce, action, and option_page fields for a settings page.
		settings_fields( 'adsforwp_setting_dashboard_group' );

		echo "<div class='adsforwp-general' " . ( $tab != 'general' ? 'style="display:none;"' : '' ) . '>';
		// general Application Settings
				do_settings_sections( 'adsforwp_general_section' );    // Page slug
		echo '</div>';

						echo "<div class='adsforwp-tools' " . ( $tab != 'tools' ? 'style="display:none;"' : '' ) . '>';
		// Status
		do_settings_sections( 'adsforwp_tools_section' );    // Page slug
		echo '</div>';

						echo "<div class='adsforwp-advance' " . ( $tab != 'advance' ? 'style="display:none;"' : '' ) . '>';
		// Status
		do_settings_sections( 'adsforwp_advance_section' );    // Page slug
		echo '</div>';

						echo "<div class='adsforwp-support' " . ( $tab != 'support' ? 'style="display:none;"' : '' ) . '>';
		// general Application Settings
				do_settings_sections( 'adsforwp_support_section' );    // Page slug
		echo '</div>';

		echo "<div class='adsforwp-pro' " . ( $tab != 'pro' ? 'style="display:none;"' : '' ) . '>';
		// general Application Settings
				do_settings_sections( 'adsforwp_pro_section' );    // Page slug
		echo '</div>';

		?>
						</div>
			<div class="button-wrapper">                            
		<?php
		// Output save settings button
		submit_button( esc_html__( 'Save Settings', 'ads-for-wp' ) );
		?>
			</div>
		</form>    
		
		<?php
	}
	/*
	WP Settings API
	*/
	public function adsforwp_settings_init() {
		register_setting( 'adsforwp_setting_dashboard_group', 'adsforwp_settings', array( $this, 'adsforwp_handle_file_upload' ) );

			add_settings_section( 'adsforwp_tools_section', 'Migration', '__return_false', 'adsforwp_tools_section' );
					add_settings_field(
						'adsforwp_import_status',                                // ID
						'',            // Title
						array( $this, 'adsforwp_import_callback' ),                    // Callback
						'adsforwp_tools_section',                            // Page slug
						'adsforwp_tools_section'                            // Settings Section ID
					);

				add_settings_section( 'adsforwp_advance_section', 'Advance Settings', '__return_false', 'adsforwp_advance_section' );
					add_settings_field(
						'adsforwp_advance_status',                                // ID
						'',            // Title
						array( $this, 'adsforwp_advance_callback' ),                    // Callback
						'adsforwp_advance_section',                            // Page slug
						'adsforwp_advance_section'                            // Settings Section ID
					);

					add_settings_field(
						'adsforwp_adstxt_manager',                                // ID
						'',            // Title
						array( $this, 'adsforwp_adstxt_manager_callback' ),                    // Callback
						'adsforwp_advance_section',                            // Page slug
						'adsforwp_advance_section'                            // Settings Section ID
					);

				add_settings_section( 'adsforwp_general_section', 'Settings', '__return_false', 'adsforwp_general_section' );
					add_settings_field(
						'adsforwp_ad_blocker_support',                                // ID
						'Ad Blocker Support',            // Title
						array( $this, 'adsforwp_ad_blocker_support_callback' ),                    // Callback
						'adsforwp_general_section',                            // Page slug
						'adsforwp_general_section'                            // Settings Section ID
					);
					add_settings_field(
						'adsforwp_ad_blocker_notice',                                // ID
						'Notice For Ad Blocker',            // Title
						array( $this, 'adsforwp_ad_blocker_notice_callback' ),                    // Callback
						'adsforwp_general_section',                            // Page slug
						'adsforwp_general_section',                            // Settings Section ID
						array( 'class' => 'child-opt' )
					);

					add_settings_field(
						'adsforwp_ad_blocker_notice_options',                                // ID
						'',            // Title
						array( $this, 'adsforwp_ad_blocker_notice_opt_callback' ),                    // Callback
						'adsforwp_general_section',                            // Page slug
						'adsforwp_general_section',                            // Settings Section ID
						array( 'class' => 'subchild-opt' )
					);
					add_settings_field(
						'adsforwp_ad_performance_tracking',                                // ID
						'Ad Performance Tracking',            // Title
						array( $this, 'adsforwp_ad_performance_tracking_callback' ),                    // Callback
						'adsforwp_general_section',                            // Page slug
						'adsforwp_general_section'                            // Settings Section ID
					);
					add_settings_field(
						'adsforwp_ad_performance_tracking_for_admin',                                // ID
						'',            // Title
						array( $this, 'adsforwp_ad_performance_tracking_for_admin_callback' ),                    // Callback
						'adsforwp_general_section',                            // Page slug
						'adsforwp_general_section',                            // Settings Section ID
						array( 'class' => 'subchild-opt' )
					);
					add_settings_field(
						'adsforwp_ad_revenue_sharing',                                // ID
						'Revenue Sharing',            // Title
						array( $this, 'adsforwp_ad_revenue_sharing_callback' ),                    // Callback
						'adsforwp_general_section',                            // Page slug
						'adsforwp_general_section'                            // Settings Section ID
					);

					add_settings_field(
						'adsforwp_ad_sponsorship_label',                                // ID
						'Ad Label',            // Title
						array( $this, 'adsforwp_ad_sponsorship_label_callback' ),                    // Callback
						'adsforwp_general_section',                            // Page slug
						'adsforwp_general_section'                            // Settings Section ID
					);
					add_settings_field(
						'adsforwp_ad_label_options',                                // ID
						'',            // Title
						array( $this, 'adsforwp_ad_label_options_callback' ),                    // Callback
						'adsforwp_general_section',    // Page slug
						'adsforwp_general_section',    // Settings Section ID
						array( 'class' => 'adlabel-child-opt' )
					);
					add_settings_field(
						'adsforwp_ad_fraud_protection',                                // ID
						'Click Fraud Protection',            // Title
						array( $this, 'adsforwp_ad_fraud_protection_callback' ),                    // Callback
						'adsforwp_general_section',                            // Page slug
						'adsforwp_general_section'                            // Settings Section ID
					);

					add_settings_field(
						'adsforwp_ad_fraud_protection_options',                                // ID
						'',            // Title
						array( $this, 'adsforwp_ad_fraud_protection_options_callback' ),                    // Callback
						'adsforwp_general_section',    // Page slug
						'adsforwp_general_section',    // Settings Section ID
						array( 'class' => 'adlabel-child-opt' )
					);
					add_settings_section( 'adsforwp_support_section', 'Contact Us', '__return_false', 'adsforwp_support_section' );
					add_settings_field(
						'adsforwp_contact_us_form',                                // ID
						'',            // Title
						array( $this, 'adsforwp_contact_us_form_callback' ),                    // Callback
						'adsforwp_support_section',                            // Page slug
						'adsforwp_support_section'                            // Settings Section ID
					);

		add_settings_section( 'adsforwp_pro_section', '', '__return_false', 'adsforwp_pro_section' );
					add_settings_field(
						'adsforwp_pro_content',                                // ID
						'',            // Title
						array( $this, 'adsforwp_pro_content_callback' ),                    // Callback
						'adsforwp_pro_section',                            // Page slug
						'adsforwp_pro_section'                            // Settings Section ID
					);
	}


	public function adsforwp_custom_upload_mimes( $mimes = array() ) {

		$mimes['json'] = 'application/json';

		return $mimes;
	}
	public function adsforwp_handle_file_upload( $option ) {

		if ( ! current_user_can( 'upload_files' ) ) {
					return $option;
		}
		if ( ! check_admin_referer( 'adsforwp_setting_dashboard_group-options' ) ) {
			return $option;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$fileInfo = isset( $_FILES['adsforwp_import_backup']['name'] ) ? wp_check_filetype( basename( $_FILES['adsforwp_import_backup']['name'] ) ) : array();

		if ( ! empty( $fileInfo['ext'] ) && $fileInfo['ext'] == 'json' ) {

			if ( ! empty( $_FILES['adsforwp_import_backup']['tmp_name'] ) ) {

				$urls = wp_handle_upload( $_FILES['adsforwp_import_backup'], array( 'test_form' => false ) );
				$url  = $urls['url'];
				update_option( 'adsforwp-file-upload_url', esc_url( $url ) );

			}
		}

		return $option;
	}

	public function adsforwp_check_data_imported_from( $plugin_post_type_name ) {
		
		$cc_args       = array(
			'posts_per_page' => -1,
			'post_type'      => 'adsforwp',
			'meta_key'       => 'imported_from',//phpcs:ignore -- WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- just using in import button click not all the time
			'meta_value'     => $plugin_post_type_name,//phpcs:ignore -- WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- just using in import button click not all the time
		);
		
		$imported_from = new WP_Query( $cc_args );
		return $imported_from;
	}

	/**
	 * since v1.9.3
	 *
	 * @param  string $error
	 * @return type
	 */
	public function adsforwp_format_error( $error ) {

		$messages = $this->adsforwp_get_error_messages();

		if ( ! isset( $messages[ $error['type'] ] ) ) {
			return __( 'Unknown error', 'ads-for-wp' );
		}

		if ( ! isset( $error['value'] ) ) {
			$error['value'] = '';
		}

		$message = sprintf( esc_html( $messages[ $error['type'] ] ), '<code>' . esc_html( $error['value'] ) . '</code>' );

		$message = sprintf(
		/* translators: Error message output. 1: Line number, 2: Error message */
			__( 'Line %1$s: %2$s', 'ads-for-wp' ),
			esc_html( $error['line'] ),
			$message // This is escaped piece-wise above and may contain HTML (code tags) at this point
		);

		return $message;
	}

	/**
	 * since v1.9.3
	 * Get all non-generic error messages, translated and with placeholders intact.
	 *
	 * @return array Associative array of error messages.
	 */
	public function adsforwp_get_error_messages() {
		$messages = array(
			'invalid_variable'     => esc_html__( 'Unrecognized variable', 'ads-for-wp' ),
			'invalid_record'       => esc_html__( 'Invalid record', 'ads-for-wp'),
			'invalid_account_type' => esc_html__( 'Third field should be RESELLER or DIRECT', 'ads-for-wp' ),
			/* translators: %s: Subdomain */
			'invalid_subdomain'    => esc_html__( '%s does not appear to be a valid subdomain', 'ads-for-wp' ),
			/* translators: %s: Exchange domain */
			'invalid_exchange'     => esc_html__( '%s does not appear to be a valid exchange domain', 'ads-for-wp' ),
			/* translators: %s: Alphanumeric TAG-ID */
			'invalid_tagid'        => esc_html__( '%s does not appear to be a valid TAG-ID', 'ads-for-wp' ),
		);

		return $messages;
	}

	/**
	 * since v1.9.3
	 * Validate a single line.
	 *
	 * @param string $line        The line to validate.
	 * @param string $line_number The line number being evaluated.
	 *
	 * @return array {
	 * @type   string $sanitized Sanitized version of the original line.
	 * @type   array  $errors    Array of errors associated with the line.
	 * }
	 */
	public function adsforwp_validate_line( $line, $line_number ) {

		$domain_regex = '/^((?=[a-z0-9-]{1,63}\.)(xn--)?[a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,63}$/i';
		$errors       = array();

		if ( empty( $line ) ) {
			$sanitized = '';
		} elseif ( 0 === strpos( $line, '#' ) ) { // This is a full-line comment.
			$sanitized = wp_strip_all_tags( $line );
		} elseif ( 1 < strpos( $line, '=' ) ) { // This is a variable declaration.
			// The spec currently supports CONTACT and SUBDOMAIN.
			if ( ! preg_match( '/^(CONTACT|SUBDOMAIN)=/i', $line ) ) {
				$errors[] = array(
					'line' => $line_number,
					'type' => 'invalid_variable',
				);
			} elseif ( 0 === stripos( $line, 'subdomain=' ) ) { // Subdomains should be, well, subdomains.
				// Disregard any comments.
				$subdomain = explode( '#', $line );
				$subdomain = $subdomain[0];

				$subdomain = explode( '=', $subdomain );
				array_shift( $subdomain );

				// If there's anything other than one piece left something's not right.
				if ( 1 !== count( $subdomain ) || ! preg_match( $domain_regex, $subdomain[0] ) ) {
					$subdomain = implode( '', $subdomain );
					$errors[]  = array(
						'line'  => $line_number,
						'type'  => 'invalid_subdomain',
						'value' => $subdomain,
					);
				}
			}

			$sanitized = wp_strip_all_tags( $line );

			unset( $subdomain );
		} else { // Data records: the most common.
			// Disregard any comments.
			$record = explode( '#', $line );
			$record = $record[0];

			// Record format: example.exchange.com,pub-id123456789,RESELLER|DIRECT,tagidhash123(optional).
			$fields = explode( ',', $record );

			if ( 3 <= count( $fields ) ) {
					$exchange     = trim( $fields[0] );
					$pub_id       = trim( $fields[1] );
					$account_type = trim( $fields[2] );

				if ( ! preg_match( $domain_regex, $exchange ) ) {
					$errors[] = array(
						'line'  => $line_number,
						'type'  => 'invalid_exchange',
						'value' => $exchange,
					);
				}

				if ( ! preg_match( '/^(RESELLER|DIRECT)$/i', $account_type ) ) {
					$errors[] = array(
						'line' => $line_number,
						'type' => 'invalid_account_type',
					);
				}

				if ( isset( $fields[3] ) ) {
					$tag_id = trim( $fields[3] );

					// TAG-IDs appear to be 16 character hashes.
					// TAG-IDs are meant to be checked against their DB - perhaps good for a service or the future.
					if ( ! empty( $tag_id ) && ! preg_match( '/^[a-f0-9]{16}$/', $tag_id ) ) {
						$errors[] = array(
							'line'  => $line_number,
							'type'  => 'invalid_tagid',
							'value' => $fields[3],
						);
					}
				}

				$sanitized = wp_strip_all_tags( $line );
			} else {
					// Not a comment, variable declaration, or data record; therefore, invalid.
					// Early on we commented the line out for safety but it's kind of a weird thing to do with a JS AYS.
					$sanitized = wp_strip_all_tags( $line );

					$errors[] = array(
						'line' => $line_number,
						'type' => 'invalid_record',
					);
			}

					unset( $record, $fields );
		}

		return array(
			'sanitized' => $sanitized,
			'errors'    => $errors,
		);
	}

	public function adsforwp_pre_update_settings( $value, $old_value, $option ) {

		if ( $option == 'adsforwp_settings' ) {

			$lines     = preg_split( '/\r\n|\r|\n/', $value['adsforwp_adstxt'] );
			$sanitized = array();
			$errors    = array();
			$response  = array();

			foreach ( $lines as $i => $line ) {
				$line_number = $i + 1;
				$result      = $this->adsforwp_validate_line( $line, $line_number );

				$sanitized[] = $result['sanitized'];
				if ( ! empty( $result['errors'] ) ) {
						$errors = array_merge( $errors, $result['errors'] );
				}
			}

			$sanitized = implode( PHP_EOL, $sanitized );

			$value['adsforwp_adstxt']        = $sanitized;
			$value['adsforwp_adstxt_errors'] = $errors;

		}
		return $value;
	}

	public function adsforwp_adstxt_manager_callback() {

		$settings = adsforwp_defaultSettings();
		$errors   = array();

		if ( isset( $settings['adsforwp_adstxt_errors'] ) && is_array( $settings['adsforwp_adstxt_errors'] ) ) {
			$errors = $settings['adsforwp_adstxt_errors'];
		}

		?>
			<ul>
				<li>
					<div class="adsforwp-tools-field-title">
						
						<div class="adsforwp-tooltip"><strong><?php echo esc_html__( 'Ads Txt Manager', 'ads-for-wp' ); ?></strong>
						</div>     
						
						<fieldset style="display: inline-block;">
							<input type="checkbox" id="adsforwp_ads_txt" name="adsforwp_settings[adsforwp_ads_txt]" <?php echo isset( $settings['adsforwp_ads_txt'] ) ? 'checked' : ''; ?> > 
						</fieldset>
						</div>
						<div class="adsforwp-ads-txt-section <?php echo isset( $settings['adsforwp_ads_txt'] ) ? 'checked' : 'afw_hide'; ?>">                                             
						
						<?php if ( ! empty( $errors ) ) : ?>
					<div class="notice notice-error adsforw-padstxt-notice">
		<p><strong><?php echo esc_html__( 'Your Ads.txt contains the following issues:', 'ads-for-wp' ); ?></strong></p>
		<ul>
							<?php
							foreach ( $errors as $error ) {
								echo '<li>';

								// Errors were originally stored as an array
								// This old style only needs to be accounted for here at runtime display
								if ( isset( $error['message'] ) ) {
									$message = sprintf(
									/* translators: Error message output. 1: Line number, 2: Error message */
										__( 'Line %1$s: %2$s', 'ads-for-wp' ),
										$error['line'],
										$error['message']
									);

									echo esc_html( $message );
								} else {
        //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped	-- Reason: output is already escaped  
									echo $this->adsforwp_format_error( $error );
								}

								echo '</li>';
							}
							?>
		</ul>
	</div>
						<?php endif; ?>
						
						<div class="adsforwp_adstxt_div">                                               
						<textarea class="widefat code" rows="10" name="adsforwp_settings[adsforwp_adstxt]" id="adsforwp_adstxt"><?php echo ( isset( $settings['adsforwp_adstxt'] ) ? esc_html( $settings['adsforwp_adstxt'] ) : '' ); ?></textarea>
						</div>
														
						</div>   
						
				</li> 
				
			</ul>
		
		<?php
	}
	public function adsforwp_advance_callback() {

		$settings = adsforwp_defaultSettings();

		?>
			<ul>
				<li><div class="adsforwp-tools-field-title">
						<div class="adsforwp-tooltip"><strong><?php echo esc_html__( 'IP Geolocation API', 'ads-for-wp' ); ?></strong>
						</div>
						<input type="text" value="<?php echo isset($settings['adsforwp_geolocation_api'])?esc_attr( $settings['adsforwp_geolocation_api'] ):'';?>" id="adsforwp-geolocation-api" name="adsforwp_settings[adsforwp_geolocation_api]">                        
						<span style="font-weight: 500;"> <?php esc_html__( 'Today, Request Made -: ', 'ads-for-wp' ); ?>  <?php echo esc_attr( get_option( 'adsforwp_ip_request_' . gmdate( 'Y-m-d' ) ) ); ?></span>
						<p><?php echo esc_html__( 'Note : They have free plan which gives you 50K requests per month. For all that you need to', 'ads-for-wp' ); ?> <a href="https://ipgeolocation.io" target="_blank"><?php echo esc_html__( 'Signup', 'ads-for-wp' ); ?></a></p>
					</div>
				</li> 
				
			</ul>
		<?php
	}

	public function adsforwp_import_callback() {

		$message                   = '<p>' . esc_html__( 'This plugin\'s data already has been imported. Do you want to import again?. click on button above button.', 'ads-for-wp' ) . '</p>';
		$schema_message            = 0;
		$ampforwp_ads_message      = 0;
		$ampforwp_advanced_ads_msg = 0;
		$ad_inserter_message       = 0;
		$quick_adsense_message     = 0;

		$schema_plugin         = $this->adsforwp_check_data_imported_from( 'advance_ads' );
		$ampforwp_ads          = $this->adsforwp_check_data_imported_from( 'ampforwp_ads' );
		$ampforwp_advanced_ads = $this->adsforwp_check_data_imported_from( 'ampforwp_advanced_ads' );
		$ad_inserter           = $this->adsforwp_check_data_imported_from( 'ad_inserter' );
		$quick_adsense         = $this->adsforwp_check_data_imported_from( 'quick_adsense' );

		if ( $schema_plugin->post_count != 0 ) {
			$schema_message = 1;
		}
		if ( $ampforwp_ads->post_count != 0 ) {
			$ampforwp_ads_message = 1;
		}
		if ( $ampforwp_advanced_ads->post_count != 0 ) {
			$ampforwp_advanced_ads_msg = 1;
		}

		if ( $ad_inserter->post_count != 0 ) {
			$ad_inserter_message = 1;
		}
		if ( $quick_adsense->post_count != 0 ) {
			$quick_adsense_message = 1;
		}

		?>
					<ul>
				<li><div class="adsforwp-tools-field-title"><div class="adsforwp-tooltip"><strong><?php echo esc_html__( 'Advanced Ads Plugin', 'ads-for-wp' ); ?></strong></div><button data-id="advanced_ads" class="button adsforwp-import-plugins"><?php echo esc_html__( 'Import', 'ads-for-wp' ); ?></button>
						<p class="adsforwp-imported-message"></p>
						<?php echo ( $schema_message == 1 ) ? '<p>' . esc_html__( 'This plugin\'s data already has been imported. Do you want to import again?. click on button above button.', 'ads-for-wp' ) . '</p>' : ''; ?>    
					</div>
				</li> 
				<li><div class="adsforwp-tools-field-title"><div class="adsforwp-tooltip"><strong><?php echo esc_html__( 'AMP for WP Ads', 'ads-for-wp' ); ?></strong></div><button data-id="ampforwp_ads" class="button adsforwp-import-plugins"><?php echo esc_html__( 'Import', 'ads-for-wp' ); ?></button>
						<p class="adsforwp-imported-message"></p>
						<?php echo ( $ampforwp_ads_message == 1 ) ? '<p>' . esc_html__( 'This plugin\'s data already has been imported. Do you want to import again?. click on button above button.', 'ads-for-wp' ) . '</p>' : ''; ?>
					</div>
				</li>
				<li><div class="adsforwp-tools-field-title"><div class="adsforwp-tooltip"><strong><?php echo esc_html__( 'AMP for WP Advanced Ads', 'ads-for-wp' ); ?></strong></div><button data-id="ampforwp_advanced_ads" class="button adsforwp-import-plugins"><?php echo esc_html__( 'Import', 'ads-for-wp' ); ?></button>
						<p class="adsforwp-imported-message"></p>
						<?php echo ( $ampforwp_advanced_ads_msg == 1 ) ? '<p>' . esc_html__( 'This plugin\'s data already has been imported. Do you want to import again?. click on button above button.', 'ads-for-wp' ) . '</p>' : ''; ?> 
					</div>
				</li>                
				<li><div class="adsforwp-tools-field-title"><div class="adsforwp-tooltip"><strong><?php echo esc_html__( 'Quick Adsense Ads', 'ads-for-wp' ); ?></strong></div><button data-id="quick_adsense" class="button adsforwp-import-plugins"><?php echo esc_html__( 'Import', 'ads-for-wp' ); ?></button>
						<p class="adsforwp-imported-message"></p>
						<?php echo ( $quick_adsense_message == 1 ) ? '<p>' . esc_html__( 'This plugin\'s data already has been imported. Do you want to import again?. click on button above button.', 'ads-for-wp' ) . '</p>' : ''; ?>
							
					</div>
				</li>
				<li><div class="adsforwp-tools-field-title"><div class="adsforwp-tooltip"><strong><?php echo esc_html__( 'Ad Inserter', 'ads-for-wp' ); ?></strong></div><button data-id="ad_inserter" class="button adsforwp-import-plugins"><?php echo esc_html__( 'Import', 'ads-for-wp' ); ?></button>
						<p><?php echo esc_html__( 'This will work perfectly with plugin which is available on wordpress.org', 'ads-for-wp' ); ?></p> 
						<p class="adsforwp-imported-message"></p>
						
						<?php echo ( $ad_inserter_message == 1 ) ? '<p>' . esc_html__( 'This plugin\'s data already has been imported. Do you want to import again?. click on button above button.', 'ads-for-wp' ) . '</p>' : ''; ?>
					</div>
				</li>
			</ul>                   
		<?php
		echo '<h2>' . esc_html__( 'Import / Export', 'ads-for-wp' ) . '</h2>';
		$url = wp_nonce_url( admin_url( 'admin-ajax.php?action=adsforwp_export_all_settings' ), '_wpnonce' );
		?>
		<ul>
				<li>
					<div class="adsforwp-tools-field-title"><div class="adsforwp-tooltip"><strong><?php echo esc_html__( 'Export All Ads For WP Data', 'ads-for-wp' ); ?></strong></div><a href="<?php echo esc_url( $url ); ?>"class="button adsforwp-export-data"><?php echo esc_html__( 'Export', 'ads-for-wp' ); ?></a>                         
					</div>
				</li> 
				<li>
					<div class="adsforwp-tools-field-title"><div class="adsforwp-tooltip"><strong><?php echo esc_html__( 'Import All Ads For WP Data', 'ads-for-wp' ); ?></strong></div><input type="file" name="adsforwp_import_backup" id="adsforwp_import_backup">                         
					</div>
				</li> 
		</ul>
		<?php
		echo '<h2>' . esc_html__( 'Delete All Settings and Data', 'ads-for-wp' ) . '</h2>';
		?>
		<ul>
				
				<li>
					<div class="adsforwp-tools-field-title">
						<div class="adsforwp-tooltip">
							<strong>
								<?php echo esc_html__( 'Delete', 'ads-for-wp' ); ?>
							</strong>
						</div>
						<a href="#"class="button adsforwp-reset-data">
									<?php echo esc_html__( 'Delete', 'ads-for-wp' ); ?>
						</a>                         
						<p><?php echo esc_html__( 'This will delete your settings and all ads', 'ads-for-wp' ); ?></p>
					</div>
				</li> 
		</ul>
		<?php
	}
	public function adsforwp_ad_blocker_notice_opt_callback() {
		$settings             = adsforwp_defaultSettings();
		$notice_type          = isset( $settings['notice_type'] ) ? esc_attr( $settings['notice_type'] ) : '';
		$notice_title         = esc_attr( $settings['notice_title'] );
		$notice_description   = esc_html( $settings['notice_description'] );
		$btn_txt              = esc_attr( $settings['btn_txt'] );
		$notice_bg_color      = sanitize_hex_color( $settings['notice_bg_color'] );
		$notice_txt_color     = sanitize_hex_color( $settings['notice_txt_color'] );
		$notice_btn_bg_color  = sanitize_hex_color( $settings['notice_btn_bg_color'] );
		$notice_btn_txt_color = sanitize_hex_color( $settings['notice_btn_txt_color'] );
		$cls_checked          = '';

		if ( isset( $settings['notice_close_btn'] ) ) {
			$cls_checked = 'checked';
		}

		?>
	<div class="afw_ad_blocker_notice" id="afw_ad_blocker_notice">
		<div class="adsfowp_notice_options">
			<div class="label-align notice-wrap">
				<label for="afw-bar"><?php echo esc_html__( 'Notice Type', 'ads-for-wp' ); ?></label>
				<div class="afw-nt-flds">
				<p>
					<input type="radio" id="afw-bar" class="notice_type" name="adsforwp_settings[notice_type]" value="bar" <?php echo ( $notice_type == 'bar' ) ? 'checked' : ''; ?> >
				<label for="afw-bar" class="afw-radio-label"><?php echo esc_html__( 'Bar', 'ads-for-wp' ); ?></label>
				</p>
				<p>
					<input type="radio" id="afw-popup" class="notice_type" name="adsforwp_settings[notice_type]" value="popup" <?php echo ( $notice_type == 'popup' ) ? 'checked' : ''; ?>>
				<label for="afw-popup" class="afw-radio-label"><?php echo esc_html__( 'Popup', 'ads-for-wp' ); ?></label>
				</p>
				<p>
					<input type="radio" id="afw-page-redirect" class="notice_type" name="adsforwp_settings[notice_type]" value="page_redirect" <?php echo ( $notice_type == 'page_redirect' ) ? 'checked' : ''; ?>>
				<label for="afw-page-redirect" class="afw-radio-label"><?php echo esc_html__( 'Page Redirection', 'ads-for-wp' ); ?></label>
				</p>
				</div>
			</div>
			<div class="label-align allow_cookies">
				<label for="allow_cookies"><?php echo esc_html__( 'Notice Behaviour', 'ads-for-wp' ); ?> </label>
				<select name="adsforwp_settings[allow_cookies]">
					<option value="2" <?php selected( $settings['allow_cookies'], 2 ); ?>>Show on Every Visit</option>
					<option value="1" <?php selected( $settings['allow_cookies'], 1 ); ?>>Show Only Once</option>
					 
				</select>
			</div>
			<div class="label-align page_redirect">
				<label for="page_redirect"><?php echo esc_html__( 'Target Page', 'ads-for-wp' ); ?> </label>
					<select name="adsforwp_settings[page_redirect]">
		<?php
		$pages = get_pages();
		echo '<option value="0">--Select Page--</option>';
		foreach ( $pages as $page ) {
			$title = $page->post_title;
			if ( empty( $title ) ) {
				/* translators: %s is the page ID */
				$title = sprintf( esc_html__( 'Untitled %s', 'ads-for-wp' ), '(ID #' . $page->ID . ')' );
			}
			?>
						<option value="<?php echo esc_attr( $page->ID ); ?>" <?php selected( $settings['page_redirect'], $page->ID ); ?>><?php echo esc_attr( $title ); ?></option>
			<?php
		}
		?>
				</select>
			</div>
		</div>
		<div class="adsfowp_notice_content">
			<h2 class="title"><?php echo esc_html__( 'Notice Content', 'ads-for-wp' ); ?></h2>
			<div class="label-align notice_title">
			<label for="notice_title"> <?php echo esc_html__( 'Title', 'ads-for-wp' ); ?> </label>
				<input id="notice_title" name="adsforwp_settings[notice_title]" type="text" class="regular-text" value="<?php echo esc_attr( $notice_title ); ?>"/>
			</div>
			<div class="label-align notice_description">
				<label for="notice_description" ><?php echo esc_html__( 'Description', 'ads-for-wp' ); ?> </label>
				<fieldset>
				<textarea name="adsforwp_settings[notice_description]" id="notice_description" type="text" class="large-text code notice_description" rows="5" cols="70"><?php echo esc_html( $notice_description ); ?></textarea>
				</fieldset>
			</div>
			<div class="label-align notice_close_btn">
				<label for="notice_close_btn" > <?php echo esc_html__( 'Close Button', 'ads-for-wp' ); ?> </label>
				<input name="adsforwp_settings[notice_close_btn]" id="notice_close_btn" type="checkbox" value="1" <?php echo esc_attr( $cls_checked ); ?>/>
			</div>
			<div class="label-align adsforwp_close_btn_txt">
				<label for="btn_txt"><?php echo esc_html__( 'Button Text', 'ads-for-wp' ); ?> </label>
				<input name="adsforwp_settings[btn_txt]" id="btn_txt" type="text" class="btn_txt" value="<?php echo esc_attr( $btn_txt ); ?>" />
			</div>
			</div>
			<div class="adsfowp_notice_design">
				<h2 class="title"><?php echo esc_html__( 'Notice Design', 'ads-for-wp' ); ?></h2>
				<div class="label-align notice_txt_color">
					<label for="notice_txt_color"> <?php echo esc_html__( 'Content Color', 'ads-for-wp' ); ?> </label>
					<input type="text" value="<?php echo esc_attr( $notice_txt_color ); ?>" name="adsforwp_settings[notice_txt_color]" id="notice_txt_color" class="adsforwp_cp" data-default-color="#ffffff"/> 
				</div>
				<div class="label-align notice_bg_color">
					<label for="notice_bg_color"> <?php echo esc_html__( 'Background Color', 'ads-for-wp' ); ?> </label>
					<input type="text" value="<?php echo esc_attr( $notice_bg_color ); ?>" name="adsforwp_settings[notice_bg_color]" id="notice_bg_color" class="adsforwp_cp" data-default-color="#1e73be"/>
				</div>
				<div class="label-align notice_btn_txt_color">
					<label for="notice_btn_txt_color"> <?php echo esc_html__( 'Button Text Color', 'ads-for-wp' ); ?> </label>
					<input type="text" value="<?php echo esc_attr( $notice_btn_txt_color ); ?>" name="adsforwp_settings[notice_btn_txt_color]" id="notice_btn_txt_color" class="adsforwp_cp" data-default-color="#ffffff"/>
				</div>
				<div class="label-align notice_btn_bg_color">
				<label for="notice_btn_bg_color"><?php echo esc_html__( 'Button Background Color', 'ads-for-wp' ); ?></label>
					<input type="text" value="<?php echo esc_attr( $notice_btn_bg_color ); ?>" name="adsforwp_settings[notice_btn_bg_color]" id="notice_btn_bg_color" class="adsforwp_cp" data-default-color="#f44336"/>
				</div>
			</div>
	</div>
		<?php
	}
	public function adsforwp_ad_blocker_notice_callback() {
		$settings = adsforwp_defaultSettings();
		?>
			<fieldset>
		<?php
		$checked = '';
		if ( isset( $settings['ad_blocker_notice'] ) ) {
			$checked = 'checked';
		}
		echo '<input type="checkbox" name="adsforwp_settings[ad_blocker_notice]" class="afw_advnc_ad_blocker_notice" value="1" ' . esc_attr( $checked ) . '><p>Notice to users Disable AdBlocker is active <a href="https://adsforwp.com/docs/article/what-is-disable-adblocker-to-use-our-adsforwp-plugin-smoothly-notice-in-dashboard/" target="_blank">' . esc_html__( 'Learn more', 'ads-for-wp' ) . '</a></p>';
		?>
	</fieldset>
		<?php
	}
	public function adsforwp_ad_blocker_support_callback() {
		$settings = adsforwp_defaultSettings();
		?>
			<fieldset>
		<?php
		$checked = '';
		if ( isset( $settings['ad_blocker_support'] ) ) {
			$checked = 'checked';
		}
		echo '<input type="checkbox" name="adsforwp_settings[ad_blocker_support]" class="afw_advnc_ad_blocker_support" value="1" ' . esc_attr( $checked ) . '><p>' . esc_html__( 'Once you check this option blocked ads will be displayed', 'ads-for-wp' ) . ' <a href="https://adsforwp.com/docs/article/how-to-use-ad-blocker-support-in-wordpress-and-amp/" target="_blank">' . esc_html__( 'Learn more', 'ads-for-wp' ) . '</a></p>';
		?>
	</fieldset>
	 
		<?php
	}
	public function adsforwp_ad_performance_tracking_callback() {
		$settings = adsforwp_defaultSettings();
		?>
	<fieldset>
			<?php
			if ( isset( $settings['ad_performance_tracker'] ) ) {
				echo '<input type="checkbox" name="adsforwp_settings[ad_performance_tracker]" class="regular-text afw_ad_performance_click" value="1" checked><p>' . esc_html__( 'You can enable the Ad Performance Tracking functionality which displays the Ad Impression and Ad Clicks', 'ads-for-wp' ) . ' <a href="https://adsforwp.com/docs/article/ad-performance-tracking-with-ads-for-wp/" target="_blank">' . esc_html__( 'Learn more', 'ads-for-wp' ) . '</a></p>';
			} else {
				echo '<input type="checkbox" name="adsforwp_settings[ad_performance_tracker]" class="regular-text" value="1" ><p>' . esc_html__( 'You can enable the Ad Performance Tracking functionality which displays the Ad Impression and Ad Clicks', 'ads-for-wp' ) . ' <a href="https://adsforwp.com/docs/article/ad-performance-tracking-with-ads-for-wp/" target="_blank">' . esc_html__( 'Learn more', 'ads-for-wp' ) . '</a></p>';
			}

			?>
		 
	</fieldset>
	 
		<?php
	}
	public function adsforwp_ad_performance_tracking_for_admin_callback() {
		$settings = adsforwp_defaultSettings();
		$is_parent_enabled =  'style="display:none"';
		if ( isset( $settings['ad_performance_tracker'] ) && $settings['ad_performance_tracker']==1) {
			$is_parent_enabled =  '';
		}
		$is_child_checked = '';
		if ( isset( $settings['ad_performance_tracker_for_admin'] ) && $settings['ad_performance_tracker_for_admin']==1) {
			$is_child_checked =  'checked';
		}
		?>
	<fieldset>
			<?php

			if ( isset( $settings['ad_performance_tracker_for_admin'] ) ) {
				echo '<span '.esc_attr($is_parent_enabled).' id="afw_ad_performance_admin_blk"><input type="checkbox" name="adsforwp_settings[ad_performance_tracker_for_admin]" class="regular-text" value="1" '.esc_attr($is_child_checked).'><p>' . esc_html__( 'You can enable for Logged in Users', 'ads-for-wp' ) . ' <a href="https://adsforwp.com/docs/article/ad-performance-tracking-with-ads-for-wp/" target="_blank">' . esc_html__( 'Learn more', 'ads-for-wp' ) . '</a></p></span>';
			} else {
				echo '<span '.esc_attr($is_parent_enabled).' id="afw_ad_performance_admin_blk"><input type="checkbox" name="adsforwp_settings[ad_performance_tracker_for_admin]" class="regular-text" value="1"><p>' . esc_html__( 'You can enable for Logged in Users', 'ads-for-wp' ) . ' <a href="https://adsforwp.com/docs/article/ad-performance-tracking-with-ads-for-wp/" target="_blank">' . esc_html__( 'Learn more', 'ads-for-wp' ) . '</a></p></span>';
			}

			?>
		 
	</fieldset>
	 
		<?php
	}

	public function adsforwp_ad_revenue_sharing_callback() {
		$settings = adsforwp_defaultSettings();

		?>
			<fieldset>
			<?php

			if ( isset( $settings['ad_revenue_sharing'] ) ) {
				echo '<input type="checkbox" name="adsforwp_settings[ad_revenue_sharing]" class="regular-text afw_ad_revenue_sharing" value="1" checked><p>' . esc_html__( 'Revenue sharing is the distribution of the total amount of income generated by the Ads', 'ads-for-wp' ) . '  <a href="https://adsforwp.com/docs/article/setup-revenue-sharing-in-wordpress-and-amp/" target="_blank">' . esc_html__( 'Learn more', 'ads-for-wp' ) . '</a></p>';
			} else {
				echo '<input type="checkbox" name="adsforwp_settings[ad_revenue_sharing]" class="regular-text afw_ad_revenue_sharing" value="1" ><p>' . esc_html__( 'Revenue sharing is the distribution of the total amount of income generated by the Ads', 'ads-for-wp' ) . '  <a href="https://adsforwp.com/docs/article/setup-revenue-sharing-in-wordpress-and-amp/" target="_blank">Learn more</a></p>';
			}

			?>
				</fieldset>
		<div class="afw_revenue_divider">
			<h2 class="title"><?php echo esc_html__( 'Enter the percentage of revenue that you would like to share', 'ads-for-wp' ); ?></h2>

			<div class="label-align ad_label">
				<label for="ad_label"><?php echo esc_html__( 'Owner', 'ads-for-wp' ); ?> </label>
				<input type="number" placeholder="<?php echo esc_attr__("percentage","ads-for-wp");?>" id="adsforwp_owner_revenue_per" name="adsforwp_settings[ad_owner_revenue_per]" value="<?php echo isset( $settings['ad_owner_revenue_per'] ) ? esc_attr( $settings['ad_owner_revenue_per'] ) : ''; ?>">
			</div>
			<div class="label-align ad_label">
				<label for="ad_label"><?php echo esc_html__( 'Author', 'ads-for-wp' ); ?> </label>
				<input type="number"  placeholder="<?php echo esc_attr__("percentage","ads-for-wp");?>" id="adsforwp_author_revenue_per" name="adsforwp_settings[ad_author_revenue_per]" value="<?php echo isset( $settings['ad_author_revenue_per'] ) ? esc_attr( $settings['ad_author_revenue_per'] ) : ''; ?>">
			</div> 
		</div>
		<?php
	}

	public function adsforwp_ad_sponsorship_label_callback() {
		$settings = adsforwp_defaultSettings();
		$checked  = '';
		if ( isset( $settings['ad_sponsorship_label'] ) && $settings['ad_sponsorship_label'] ) {
			$checked = 'checked';
		}
		?>
			<fieldset>
		<input type="checkbox" id="afw_ad_label" name="adsforwp_settings[ad_sponsorship_label]" class="regular-text afw_ad_label" value="1" <?php echo esc_attr( $checked ); ?> >
		<p><?php echo esc_html__( 'Add Sponsorship Label in the Ads', 'ads-for-wp' ); ?> <a href="https://adsforwp.com/docs/article/add-sponsorship-label-in-ads/" target="_blank"><?php echo esc_html__( 'Learn more', 'ads-for-wp' ); ?></a></p>
	</fieldset>
		<?php
	}

	public function adsforwp_ad_label_options_callback() {
		$settings           = adsforwp_defaultSettings();
		$ad_label_txt_color = $settings['ad_label_txt_color'];

		?>
	<div class="afw_ad_blocker_notice" id="afw_ad_label_settings">
		<div class="adsfowp_ad_label_options">
			<h2 class="title"><?php echo esc_html__( 'Ad Label Settings', 'ads-for-wp' ); ?></h2>
			<div class="label-align ad_label">
				<label for="ad_label"><?php echo esc_html__( 'Label', 'ads-for-wp' ); ?> </label>
				<input id="afw_ad_label_text" placeholder="<?php echo esc_attr__("Enter the label","ads-for-wp");?>" name="adsforwp_settings[ad_sponsorship_label_text]" type="text" class="regular-text afw_ad_label_text" value="<?php echo isset( $settings['ad_sponsorship_label_text'] ) ? esc_attr( $settings['ad_sponsorship_label_text'] ) : ''; ?>"/>
			</div>
			<div class="label-align ad_label_postion">
				<label for="ad_label_postion"><?php echo esc_html__( 'Position', 'ads-for-wp' ); ?> </label>
					<select name="adsforwp_settings[ad_label_postion]">
		<?php
		$positions = array(
			'above' => 'Above Ad',
			'below' => 'Below Ad',
		);
		foreach ( $positions as $key => $value ) {
			?>
						<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $settings['ad_label_postion'], $key ); ?>><?php echo esc_html( $value ); ?></option>
			<?php
		}
		?>
				</select>
			</div>
			<div class="label-align ad_label_txt_color">
					<label for="ad_label_txt_color"> <?php echo esc_html__( 'Text Color', 'ads-for-wp' ); ?> </label>
					<input type="text" value="<?php echo esc_attr( $ad_label_txt_color ); ?>" name="adsforwp_settings[ad_label_txt_color]" id="ad_label_txt_color" class="adsforwp_cp" data-default-color="#cccccc"/>
				</div>
		</div>
	</div>
		<?php
	}

	public function adsforwp_ad_fraud_protection_callback() {

		$settings = adsforwp_defaultSettings();

		$is_checked  = false;
		$is_disabled = true;
		$is_pro      = defined( 'ADSFORWP_PRO_VERSION' );

		if ( $is_pro ) {
			$is_disabled = false;
		}
		if ( isset( $settings['ad_fraud_protection'] ) && $settings['ad_fraud_protection'] ) {
			$is_checked = true;
		}

		?>
		
	<fieldset>

		<input type="checkbox" id="afw_ad_fraud_protection" name="adsforwp_settings[ad_fraud_protection]" class="regular-text afw_ad_fraud_protection" value="1" <?php echo ( $is_checked === true ) ? esc_attr( 'checked' ) : ''; ?> <?php echo ( $is_disabled == true ) ? esc_attr( 'disabled' ) : ''; ?>>
		<?php if ( ! $is_pro ) { ?>
			<a target="_blank" href="https://www.adsforwp.com/pricing/#pricings" style="text-decoration: none;color: white;font-weight: bold;margin-left: 0px;font-size: 13px !important; padding: 7px 9px;letter-spacing: 0.1px;border-radius: 60px;margin-right: 0px; background: linear-gradient(to right,#eb3349,#f45c43);"><?php esc_html_e( 'Upgrade to Premium', 'ads-for-wp' ); ?></a>;
		<?php } ?>
		 
		<p class="fra-pro-p"><?php echo esc_html__( 'Prevent spam users to click on ads multiple times.', 'ads-for-wp' ); ?></p>
	</fieldset>

		<?php
	}

	public function adsforwp_ad_fraud_protection_options_callback() {

		$settings = adsforwp_defaultSettings();
		if ( defined( 'ADSFORWP_PRO_VERSION' ) ) {
			?>
	<div class="afw_ad_blocker_notice" id="afw_ad_fraud_protection_settings">

		<div class="adsfowp_ad_fraud_protection_options">

			<h2 class="title">
			<?php
			echo esc_html__(
				'Click Fraud Protection

 Settings',
				'ads-for-wp'
			);
			?>
								</h2>

			<div class="label-align ad_allowed_click">

				<label for="ad_label"><?php echo esc_html__( 'Allowed clicks', 'ads-for-wp' ); ?> </label>

				<input id="afw_ad_allowed_click" placeholder="<?php echo esc_attr__("3","ads-for-wp");?>" name="adsforwp_settings[ad_allowed_click]" type="text" class="regular-text afw_ad_allowed_click" value="<?php echo isset( $settings['ad_allowed_click'] ) ? esc_attr( $settings['ad_allowed_click'] ) : ''; ?>"/>

			</div>

			<div class="label-align ad_click_limit">

				<label for="ad_label"><?php echo esc_html__( 'Click limit (in hours)', 'ads-for-wp' ); ?> </label>

				<input id="afw_ad_click_limit" placeholder="<?php echo esc_attr__("3","ads-for-wp");?>" name="adsforwp_settings[ad_click_limit]" type="text" class="regular-text afw_ad_click_limit" value="<?php echo isset( $settings['ad_click_limit'] ) ? esc_attr( $settings['ad_click_limit'] ) : ''; ?>"/>

			</div>

			<div class="label-align ad_ban_duration">

				<label for="ad_label"><?php echo esc_html__( 'Ban duration (in days)', 'ads-for-wp' ); ?> </label>

				<input id="afw_ad_ban_duration" placeholder="<?php echo esc_attr__("3","ads-for-wp");?>" name="adsforwp_settings[ad_ban_duration]" type="text" class="regular-text afw_ad_ban_duration" value="<?php echo isset( $settings['ad_ban_duration'] ) ? esc_attr( $settings['ad_ban_duration'] ) : ''; ?>"/>

			</div>    
			 
			<h2 class="title"><?php echo esc_html__( 'Blocked ip addresses', 'ads-for-wp' ); ?><button class="button afw-dlt-blockedip"><?php echo esc_html__( 'Clear All', 'ads-for-wp' ); ?></button></h2>
			<div class="label-align">
			<style>
				.afw-ads-ipblock, .afw-ads-ipblock th, .afw-ads-ipblock td {
					border: 1px solid black;
					border-collapse: collapse;
				}
			</style>                
				<table class="afw-ads-ipblock">
						<tr>
							<th><?php echo esc_html__( 'ID', 'ads-for-wp' ); ?></th>
							<th><?php echo esc_html__( 'Date/Time', 'ads-for-wp' ); ?></th>
							<th><?php echo esc_html__( 'IP', 'ads-for-wp' ); ?></th>
						</tr>
			<?php
			$afw_ip = get_option( 'afw_add_blocked_ip' ) ? get_option( 'afw_add_blocked_ip' ) : array();
			if ( count( $afw_ip ) ) {
				$id = 1;
				foreach ( $afw_ip as $key => $val ) {
					?>
							<tr>
								<td><?php echo esc_html( $id ); ?></td>
								<td><?php echo esc_html( $val['time'] ); ?></td>
								<td><?php echo esc_html( $val['ip'] ); ?></td>
							</tr>
					<?php
					++$id;
				}
			}
			?>
				</table>
			</div>                

		</div>

	</div>

			<?php
		}
	}

	public function adsforwp_contact_us_form_callback() {
		?>
				<div class="afw_contact_us_div">
			<strong><?php echo esc_html__( 'If you have any query, please write the query in below box or email us at', 'ads-for-wp' ); ?> <a href="mailto:team@magazine3.in">team@magazine3.in</a>. <?php echo esc_html__( 'We will reply to your email address shortly', 'ads-for-wp' ); ?></strong>
			<ul>
				<li>
					<label class="support-label"><?php esc_html_e( 'Email', 'ads-for-wp' ); ?><span class="afw-query-error">*</span></label>
					<div class="support-input">
						<input type="text" id="adsforwp_query_email" name="adsforwp_query_email"
								placeholder="<?php echo esc_attr__( 'Enter your Email', 'ads-for-wp' ); ?>" />
					</div>
				</li>
				<li>
					<textarea rows="5" cols="60" id="adsforwp_query_message" name="adsforwp_query_message"> </textarea>
					<br>
					<span class="afw-query-success afw_hide"><?php echo esc_html__( 'Message sent successfully, Please wait we will get back to you shortly', 'ads-for-wp' ); ?></span>
					<span class="afw-query-error afw_hide"><?php echo esc_html__( 'Message not sent. please check your network connection', 'ads-for-wp' ); ?></span>
				</li> 
				<li>
					<strong><?php echo esc_html__( 'Are you a premium customer ?', 'ads-for-wp' ); ?></strong>  
					<select id="afw_query_premium_cus" name="afw_query_premium_cus">                       
						<option value=""><?php echo esc_html__( 'Select', 'ads-for-wp' ); ?></option>
						<option value="yes"><?php echo esc_html__( 'Yes', 'ads-for-wp' ); ?></option>
						<option value="no"><?php echo esc_html__( 'No', 'ads-for-wp' ); ?></option>
					</select>                      
				</li>
				<li><button class="button afw-send-query"><?php echo esc_html__( 'Send Message', 'ads-for-wp' ); ?></button></li>
				<li><span class="afw-admin-suform" style="color: red;font-size: 15px;"></span></li>
			</ul>  
		</div>
		<?php
	}
	public function adsforwp_pro_content_callback() {
		?>
				<div class="adsforwp-pro-tab-content">
			<?php if ( ! defined( 'ADSFORWP_PRO_VERSION' ) ) { ?>

			<p style="font-weight: bold;font-size: 30px;color: #000;"><?php esc_html_e( 'Thank YOU for using Ads for WP', 'ads-for-wp' ); ?></p>
		<p style="font-size: 18px;padding: 0 10%;line-height: 1.7;color: #000;"><?php esc_html_e( 'We strive to create the best ADSFORWP solution in WordPress. Our dedicated development team does continuous development and innovation to make sure we are able to meet your demand.', 'ads-for-wp' ); ?></p>
		<p style="font-size: 16px;font-weight: 600;color: #000;"><?php esc_html_e( 'Please support us by Upgrading to Premium version.', 'ads-for-wp' ); ?></p>
		<a target="_blank" href="https://www.adsforwp.com/pricing/">
			<button class="button-adspro" style="display: inline-block;font-size: 20px;" onclick="window.open('https://www.adsforwp.com/pricing')">
				<span><?php esc_html_e( 'YES! I want to Support by UPGRADING.', 'ads-for-wp' ); ?></span></button>
		</a>
		<a href="<?php echo esc_url( add_query_arg( 'page', 'adsforwp', admin_url( 'admin.php' ) ) ); ?>"
			style="text-decoration: none;">
			<button class="button-toc1"
					style="display: block;text-align: center;border: 0;margin: 0 auto;background: none;">                
			</button>
		</a>
				<?php
			} else {
				do_action( 'afw_admin_upgrade_license_page' );
			}
			?>
		</div>
		<?php
	}
}
if ( class_exists( 'Adsforwp_Admin_Settings' ) ) {
	new Adsforwp_Admin_Settings();
}

if ( ! function_exists( 'adsforwp_subscribe_newsletter' ) ) {
	add_action( 'wp_ajax_adsforwp_subscribe_newsletter', 'adsforwp_subscribe_newsletter' );


	function adsforwp_subscribe_newsletter() {
		if ( ! current_user_can( 'manage_options' ) ) {
					return;
		}

		if ( ! isset( $_POST['adsforwp_security_nonce'] ) ) {
			return;
		}
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason Validating nonce so sanitization not needed
		if ( ! wp_verify_nonce( $_POST['adsforwp_security_nonce'], 'adsforwp_ajax_check_nonce' ) ) {
			return;
		}

		$api_url    = 'http://magazine3.company/wp-json/api/central/email/subscribe';
		$api_params = array(
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash --Reason Since data is not storing in the database it is not necessary to unslash the data
			'name'    => isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '',
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash --Reason Since data is not storing in the database it is not necessary to unslash the data
			'email'   => isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '',
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash --Reason Since data is not storing in the database it is not necessary to unslash the data
			'website' => isset( $_POST['website'] ) ? sanitize_text_field( $_POST['website'] ) : '',
			'type'    => 'adsforwp',
		);
		$response   = wp_remote_post(
			$api_url,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params,
			)
		);
		$response   = wp_remote_retrieve_body( $response );
		echo esc_html( $response );
		die;
	}
}
