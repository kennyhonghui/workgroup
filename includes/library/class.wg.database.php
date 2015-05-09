<?php
/**
 * Interface - Database
 */
interface WG_Database{
    public function connect($dsn, $user, $pwd);
    public function query($sql);
    //public function backup();
    ///public function close();
}
