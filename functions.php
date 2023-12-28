<?php
function afzaliwp_plazadigital_autoload( $class_name ) {
	if ( ! str_contains( $class_name, 'AfzaliWP\PlazaDigital' ) ) {
		return;
	}

	$class_name = strtolower( $class_name );
	$file = str_replace( [ '_', strtolower( 'AfzaliWP\PlazaDigital' ), '\\' ], [ '-', __DIR__, '/' ], $class_name ) . '.php';

	require_once $file;
}

function mylog($data, $other_data = '') {
	error_log( PHP_EOL . '-----------------------------------' );
	error_log( '-------------$data: .'. $other_data .'---------------' );
	if ( is_array($data) || is_object($data) ) {
		error_log( print_r($data, true) );
	} else {
		error_log($data);
	}
	error_log( '-----------------------------------' );
	error_log( '-----------------------------------'.PHP_EOL );
}