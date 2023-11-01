<?php
function afzaliwp_plazadigital_autoload( $class_name ) {
	if ( ! str_contains( $class_name, 'AfzaliWP\PlazaDigital' ) ) {
		return;
	}

	$file = str_replace( [ '_', 'AfzaliWP\PlazaDigital', '\\' ], [ '-', __DIR__, '/' ], $class_name ) . '.php';

	require_once strtolower( $file );
}