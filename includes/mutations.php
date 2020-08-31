<?php
/**
 * Form GraphQL mutation.
 *
 * @package wp_boilerplate_leadform
 */

use \WPGraphQL\Registry\TypeRegistry;

// Function to check the recaptcha.
if ( ! function_exists( 'wp_boilerplate_leadform_check_recaptcha_token' ) ) {
	function wp_boilerplate_leadform_check_recaptcha_token( $token ) {
		$session = curl_init( 'https://www.google.com/recaptcha/api/siteverify' );
		curl_setopt( $session, CURLOPT_POST, true );
		curl_setopt(
			$session,
			CURLOPT_POSTFIELDS,
			array(
				'secret'   => get_option( 'google_secret_key' ),
				'response' => $token,
				'remoteip' => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '127.0.0.1',
			)
		);
		curl_setopt( $session, CURLOPT_HEADER, false );
		curl_setopt( $session, CURLOPT_ENCODING, 'UTF-8' );
		curl_setopt( $session, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $session, CURLOPT_SSL_VERIFYPEER, false );
		$response = curl_exec( $session );
		curl_close( $session );

		try {
			$results = json_decode( $response, true );
		} catch ( \Exception $e ) {
			return false;
		}

		if ( true === $results['success'] ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'wp_boilerplate_leadform_register_mutations' ) ) {
	function wp_boilerplate_leadform_register_mutations( TypeRegistry $type_registry ) {
		$forms = apply_filters( 'wp_boilerplate_leadform_fields', array() );

		foreach ( $forms as $mutation_name => $fields ) {
			$defaultArgs = array(
				'wpNonce' => array(
					'type'        => 'String',
					'description' => __( 'Wp nonce to pass back through for validation', 'wp-boilerplate-leadform' ),
				),
				'gToken'  => array(
					'type'        => 'String',
					'description' => __( 'Recaptcha Token', 'wp-boilerplate-leadform' ),
				),
			);

			$merged_fields = array_merge( $defaultArgs, $fields );

			register_graphql_mutation(
				sprintf( '%sFormMutation', $mutation_name ),
				array(
					'inputFields'         => $merged_fields,
					'outputFields'        => array(
						'success'      => array(
							'type'        => 'Boolean',
							'description' => __( 'Description of the output field', 'wp-boilerplate-leadform' ),
							'resolve'     => function ( $payload, $args, $context, $info ) {
								return isset( $payload['success'] ) ? $payload['success'] : false;
							},
						),
						'errorMessage' => array(
							'type'        => 'String',
							'description' => 'Error message if relevant',
							'resolve'     => function ( $payload, $args, $context, $info ) {
								return isset( $payload['errorMessage'] ) ? $payload['errorMessage'] : '';
							},
						),
					),
					'mutateAndGetPayload' => function ( $input, $context, $info ) use ( $mutation_name ) {
						$success   = true;
						$error     = '';
						$nonce     = isset( $input['wpNonce'] ) ? sanitize_text_field( wp_unslash( $input['wpNonce'] ) ) : '';
						$gToken    = isset( $input['gToken'] ) ? sanitize_text_field( wp_unslash( $input['gToken'] ) ) : '';
						$site_key  = get_option( 'google_site_key' ) ?: '';
						$actions   = apply_filters( 'wp_boilerplate_leadform_nonce_actions', array() );

						// Verify Nonce.
						if ( ! wp_verify_nonce( $nonce, $actions[ $mutation_name ] ) ) {
							$success = false;
							$error = __( 'Internal Error 100', 'wp-boilerplate-leadform' );
						}

						unset( $input['wpNonce'] );

						// Check Google Recaptcha.
						if ( $success && ! empty( $site_key ) && ! wp_boilerplate_leadform_check_recaptcha_token( $gToken ) ) {
							$success = false;
							$error = __( 'Internal Error 200', 'wp-boilerplate-leadform' );
						}

						if ( isset( $input['gToken'] ) ) {
							unset( $input['gToken'] );
						}

						// Process Form filters.
						if ( $success ) {
							unset( $input['clientMutationId'] );
							$success = apply_filters( 'wp_boilerplate_leadform_success_' . $mutation_name, $success, $input );

							if ( is_wp_error( $success ) ) {
								$error = $success->get_error_message();
								$success = false;
							}
						}

						return array(
							'success'      => $success,
							'errorMessage' => $error,
						);
					},
				)
			);
		}
	}
}

add_action( 'graphql_register_types', 'wp_boilerplate_leadform_register_mutations' );
