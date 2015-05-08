<?php
/**
 * @param $classname
 */
function __autoload( $classname ){
    //classname: WG_PDO;
    $filename = classname_filter($classname);
    require_once( WGCLASS . $filename );
}

/**
 * @param $classname
 * @return string
 */
function classname_filter( $classname ){
    $class_prefix = 'WG_';
    $module       = strtolower( str_replace($class_prefix, '', $classname) );
    $class_file_format = "class.wg.$module.php";
    return $class_file_format;
}