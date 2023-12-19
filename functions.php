<?php
function afzaliwp_plazadigital_autoload( $class_name ) {
	if ( ! str_contains( $class_name, 'AfzaliWP\PlazaDigital' ) ) {
		return;
	}

	$class_name = strtolower( $class_name );
	$file = str_replace( [ '_', strtolower( 'AfzaliWP\PlazaDigital' ), '\\' ], [ '-', __DIR__, '/' ], $class_name ) . '.php';

	require_once $file;
}