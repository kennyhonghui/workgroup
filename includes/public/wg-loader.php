<?php
/* Loads the config file */
if( file_exists(WGROOT . 'wg-config.php') )
    require_once(WGROOT . 'wg-config.php');

/* Get Ready! */
if( file_exists(WGPLC . 'wg-functions.php') )
    require_once(WGPLC . 'wg-functions.php');

