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
    /**
     * @param $dsn
     * @param $user
     * @param $pwd
     * @return mixed
     */
    public function connect($dsn, $user, $pwd);

    /**
     * @param $param - Array|String of SQL statement accepted.
     * @return mixed - returns rowCount.
     */
    public function query($param);

    /**
     * @param $param - Array|String of SQL statement accepted.
     * @param $returns - Results returns by a Object|Array, default is Object
     * @return mixed
     */
    public function select($param, $returns);

    /**
     * @param $param - Array|String of SQL statement accepted.
     * @param $returns - Results returns by a Object|Array, default is Object
     * @return mixed
     */
    public function selectOne($param, $returns);

    public function update( $param );

    public function insert( $param );

    public function delete( $param );

    /**
     * @return mixed
     */
    public function close();

    //public function backup();
}
