<?php
/**
 * Interface - Database
 */
interface WG_Database{
    public function connect($dsn, $user, $pwd);
    public function query($sql);
    public function select($param, $returns='object');
    public function selectOne($params, $returns='object');
    //public function backup();
    ///public function close();
}
