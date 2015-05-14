<?php
/**
 * HHHIdea System works simple.
 **/

/*
 * Front to the application.
 * Loads the Environment
 */
require_once(dirname(__FILE__) . '/../wg-bootstrap.php');

$wgdb = new WG_PDO();


/*$query = array(
    'action' => 'select',
    'table'  => 'wg_test',
    'fields' => 'test,test2,test3',
    'values' => '?,?,?',
    'where'  => 'id = 5',
    'order'  => 'id desc',
    'limit'  => '',
);
$rs = $wgdb -> query( $query );
var_dump($rs);
var_dump($wgdb::$queryString);
var_dump($wgdb::$queries);*/

/*$query = array(
    'action' => 'insert',
    'table'  => 'wg_test',
    'fields' => 'test,test2,test3',
    'values' => '?,?,?',
);
//$query = 'insert into wg_test (test, test2, test3) values ( ?,?,? )';
$p = $wgdb -> prepare($query);
$wgdb -> execute( 'a', 'b', date('Ymd', time()) );
$wgdb -> execute( 'c', 'd', date('Ymd', time()) );
$wgdb -> execute( 'e', 'f', date('Ymd', time()) );
$wgdb -> execute( 'g', 'h', date('Ymd', time()) );
//var_dump( $p );
var_dump($wgdb::$queryString);
var_dump($wgdb::$queries);*/


$wgdb -> beginTransaction();
$wgdb -> prepare( 'insert into wg_test (test, test2, test3) values ( ?,?,? )' );
$wgdb -> execute( 'a', 'b', date('Ymd', time()) );
$wgdb -> execute( 'c', 'd', date('Ymd', time()) );
$wgdb -> execute( 'e', '', date('Ymd', time()) );
$wgdb -> execute( 'g', 'h', date('Ymd', time()) );
var_dump($wgdb -> commit());
var_dump($wgdb::$queryString);
var_dump($wgdb::$queries);

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
    'fields' => 'test,test2,test3',
    'values' => '?,?,?',
    'where'  => 'test2',
    'order'  => 'id desc',
    'limit'  => '',
);
//$query = 'select test,test2,test3 from wg_test where 1 order by id desc';
//$rs = $wgdb -> select($query);
//$rs = $wgdb -> selectOne($query, 'array');
//$rs = $wgdb -> update( $query );
//var_dump($rs);*/



