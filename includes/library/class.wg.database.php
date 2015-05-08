<?php
/**
 * Interface - Database
 */
interface WG_Database{
    public function connect($dsn, $user, $pwd);    //connect to database
    //public function select( $ );
    //public function todo( $settingArr );         //actions by select/update/insert/delete
}
