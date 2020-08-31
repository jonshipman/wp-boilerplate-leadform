<?php
/**
 * Form GraphQL type.
 *
 * @package wp_boilerplate_leadform
 */

if ( ! function_exists( 'wp_boilerplate_leadform_register_types' ) ) {
	function wp_boilerplate_leadform_register_types() {
		register_graphql_object_type(
			'WpNonce',
			array(
				'description' => __( 'Nonces for the forms', 'wp-boilerplate-leadform' ),
				'fields'      => array(
					'id'      => array(
						'type'        => 'ID',
						'description' => __( 'Unique id useful for cache merging', 'wp-boilerplate-leadform' ),
					),
					'form'    => array(
						'type'        => 'String',
						'description' => __( 'Form attached to nonce', 'wp-boilerplate-leadform' ),
					),
					'wpNonce' => array(
						'type'        => 'String',
						'description' => __( 'Nonce value', 'wp-boilerplate-leadform' ),
					),
				),
			)
		);

		register_graphql_object_type(
			'FormType',
			array(
				'description' => __( 'Support for the form actions over GraphQL', 'wp-boilerplate-leadform' ),
				'fields'      => array(
					'id'              => array(
						'type'        => 'ID',
						'description' => __( 'Unique id useful for cache merging', 'wp-boilerplate-leadform' ),
					),
					'wpNonce'         => array(
						'type'        => array( 'list_of' => 'WpNonce' ),
						'description' => __( 'Current nonce for session', 'wp-boilerplate-leadform' ),
					),
					'recatchaSiteKey' => array(
						'type'        => 'String',
						'description' => __( 'Recaptcha Site Key', 'wp-boilerplate-leadform' ),
					),
				),
			)
		);
	}
}

add_action( 'graphql_register_types', 'wp_boilerplate_leadform_register_types' );
