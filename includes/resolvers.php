<?php
/**
 * Form GraphQL resolver.
 *
 * @package wp_boilerplate_leadform
 */

 use \WPGraphQL\Registry\TypeRegistry;

if ( ! function_exists( 'wp_boilerplate_leadform_register_fields' ) ) {
	function wp_boilerplate_leadform_register_fields( TypeRegistry $type_registry ) {
		register_graphql_field(
			'RootQuery',
			'formData',
			array(
				'type'        => 'FormType',
				'description' => __( 'Handles form pre-population data', 'wp-boilerplate-leadform' ),
				'resolve'     => function ( $source ) {
					$res = array(
						'id'              => \GraphQLRelay\Relay::toGlobalId( 'formdata', 1 ),
						'wpNonce'         => array(),
						'recatchaSiteKey' => get_option( 'google_site_key' ) ?: '',
					);

					foreach ( apply_filters( 'wp_boilerplate_leadform_nonce_actions', array() ) as $form => $action ) {
						$res['wpNonce'][] = array(
							'id'      => \GraphQLRelay\Relay::toGlobalId( 'nonce', $action ),
							'form'    => $form,
							'wpNonce' => wp_create_nonce( $action ),
						);
					}

					return $res;
				},
			)
		);
	}
}

add_action( 'graphql_register_types', 'wp_boilerplate_leadform_register_fields' );
