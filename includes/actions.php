<?php
/**
 * Form success filters.
 *
 * @package  wp_boilerplate_leadform
 */

/**
 * These should mirror the forms in $react/components/forms.
 * Add the nonce actions to this filter.
 */
if ( ! function_exists( 'wp_boilerplate_leadform_nonce_default' ) ) {
	function wp_boilerplate_leadform_nonce_default( $forms ) {
		$forms['default'] = 'default-contact-form';

		return $forms;
	}
}

add_filter( 'wp_boilerplate_leadform_nonce_actions', 'wp_boilerplate_leadform_nonce_default' );

/**
 * ... and append to this array for the fields...
 */
if ( ! function_exists( 'wp_boilerplate_leadform_fields_default' ) ) {
	function wp_boilerplate_leadform_fields_default( $fields ) {
		$fields['default'] = array(
			'yourName' => array(
				'type'        => 'String',
				'description' => __( 'Form submitter\'s name', 'wp-boilerplate-leadform' ),
			),
			'email'    => array(
				'type'        => 'String',
				'description' => __( 'Form submitter\'s email', 'wp-boilerplate-leadform' ),
			),
			'phone'    => array(
				'type'        => 'String',
				'description' => __( 'Form submitter\'s phone', 'wp-boilerplate-leadform' ),
			),
			'message'  => array(
				'type'        => 'String',
				'description' => __( 'Form submitter\'s message', 'wp-boilerplate-leadform' ),
			),
		);

		return $fields;
	}
}

add_filter( 'wp_boilerplate_leadform_fields', 'wp_boilerplate_leadform_fields_default' );

/**
 * ...and another filter 'wp_boilerplate_leadform_success_%FORMNAME%'
 * to handle your success. You can take this function and
 * reuse it.
 */
if ( ! function_exists( 'wp_boilerplate_leadform_success_default' ) ) {
	function wp_boilerplate_leadform_success_default( $success, $input ) {
		$message = wp_boilerplate_leadform_input_to_text( $input );

		if ( $success ) {
			$success = wp_mail(
				apply_filters( 'wp_boilerplate_leadform_default_to', get_option( 'admin_email' ) ),
				'Form Email',
				$message
			);
		}

		return $success;
	}
}

add_filter( 'wp_boilerplate_leadform_success_default', 'wp_boilerplate_leadform_success_default', 10, 2 );

// Converts the key value input pairs to something that can be read in a mail message.
if ( ! function_exists( 'wp_boilerplate_leadform_input_to_text' ) ) {
	function wp_boilerplate_leadform_input_to_text( $input ) {
		$walked = $input;
		array_walk(
			$walked,
			function( &$value, $key ) {
				$value = sprintf( "%s: %s\n", ucwords( $key ), $value );
			}
		);

		return implode( "\n", $walked );
	}
}
