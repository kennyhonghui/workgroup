<?php
/**
 * bootstrap.php
 */

/* Absolute root path. */
define('WGROOT', dirname(__FILE__) . '/');

/* Includes directory. */
define('WGINC', WGROOT . 'includes/');

/* Includes directory. */
define('WGCLASS', WGROOT . 'includes/library/');

/* Includes directory. */
define( 'WGPLC', WGROOT . 'includes/public/');


if(file_exists(  WGPLC . 'wg-loader.php' ))
    require_once( WGPLC . 'wg-loader.php' );
