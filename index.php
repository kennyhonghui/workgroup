<?php
/**
 * HHHIdea System works simple.
**/

/*
 * Front to the application.
 * Loads the Environment
 */
require_once(dirname(__FILE__) . '/wg-bootstrap.php');

$wgdb = new WG_PDO();


/*$wgdb -> beginTransaction();
$wgdb -> prepare( 'insert into wg_test (test, test2, test3) values ( ?,?,? )' );
$wgdb -> exec( 'a', 'b', date('Ymd', time()) );
$wgdb -> exec( 'c', 'd', date('Ymd', time()) );
$wgdb -> exec( 'e', 'f', date('Ymd', time()) );
$wgdb -> commit();*/



$setting = array(
    'action'   => 'select',
    'fields'    => '*',
    'from'     => 'wg_test',
    'where'    => 'test1= ? and test2=?',
    'orderby'  => 'id',
    'order'    => 'desc',
    'limit'    => '1',
);
$wgdb -> prepare( $setting );
