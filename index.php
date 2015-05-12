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
$wgdb -> execute( 'a', 'b', date('Ymd', time()) );
$wgdb -> execute( 'c', 'd', date('Ymd', time()) );
$wgdb -> execute( 'e', 'f', date('Ymd', time()) );
$wgdb -> commit();*/

/*$wgdb -> prepare('insert into wg_test (test, test2, test3) values ( ?,?,? )');
$wgdb -> execute( 'a', 'b', date('Ymd', time()) );
$wgdb -> execute( 'c', 'd', date('Ymd', time()) );
$wgdb -> execute( 'e', 'f', date('Ymd', time()) );
$wgdb -> execute( 'g', 'h', date('Ymd', time()) );*/


/*$query = array(
    'action' => 'select',
    'table'  => 'wg_test',
    'fields' => 'test,test2,test3',
    'values' => '?,?,?',
    'where'  => '1',
    'order'  => 'id desc',
    'limit'  => '',
);

$sql = $wgdb -> prepare( $query );
var_dump($sql);
$rs1 = $wgdb -> execute( '5' );
var_dump($rs1);*/



/*$query = array(
    'action' => 'select',
    'table'  => 'wg_test',
    'fields' => 'test,test2,test3',
    'values' => '?,?,?',
    'where'  => 'test2',
    'order'  => 'id desc',
    'limit'  => '',
);
$query = 'select test,test2,test3 from wg_test where 1 order by id desc';
$rs = $wgdb -> select($query, 'array');
var_dump($rs);*/



