<?php

// Adds the recaptcha keys to Headless WP theme.
if ( ! function_exists( 'wp_boilerplate_leadform_settings' ) ) {
	function wp_boilerplate_leadform_settings( $settings ) {
		$recaptcha_settings = array(
			'google_recaptcha' => array(
				'label'  => __( 'Google Recaptcha', 'wp-boilerplate-leadform' ),
				'fields' => array(
					'google_site_key'   => array(
						'label' => __( 'Site Key', 'wp-boilerplate-leadform' ),
					),
					'google_secret_key' => array(
						'label' => __( 'Secret Key', 'wp-boilerplate-leadform' ),
					),
				),
			),
		);

		return array_merge( $recaptcha_settings, $settings );
	}
}

add_filter( 'wp_boilerplate_nodes_settings', 'wp_boilerplate_leadform_settings' );
