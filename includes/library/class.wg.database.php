<?php
/**
 * Interface - Database
 *
 * @param: Accept a Array | SQL statement
 *  array['action']: select|update|insert|delete;
 *  array['table']|array['from'],
 *  array['fields'], array['values'], array['where'], array['order'], array['limit']
 */
interface WG_Database{
    public function connect($dsn, $user, $pwd);
    public function query($param);
    public function select($param, $returns='object');
    public function selectOne($params, $returns='object');
    //public function backup();
    ///public function close();
}
