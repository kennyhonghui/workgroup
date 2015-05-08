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
//$n = $wgdb -> query('insert into wg_test (`test`) values (3)');
//var_dump($n);

$wgdb -> beginTransaction();
$wgdb -> preare( 'insert into wg_test (test) values (1)' );
$wgdb -> preare( 'insert into wg_test (test) values (?)', 2,3,4 );
//$wgdb -> preare( 'insert into wg_test (test, test2) values (?,?)', [5,'a'],[6,'b'],[7,'c'] );
$wgdb -> preare( 'insert into wg_test (test, test2) values (?,?)', array(5,'a'), array(6,'b'), array(7,'c') );
//$wgdb -> preare( 'update wg_test set test2 = ? where test = ?', array(['d', 5]) );