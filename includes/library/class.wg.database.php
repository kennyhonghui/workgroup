<?php
/**
 * Interface - Database
 */
interface WG_Database{
    public function connect($dsn, $user, $pwd);    //connect to database
    public function preare($sql);          //preare a transactions
    public function select($sql);          //select statement
    //public function close();
}
