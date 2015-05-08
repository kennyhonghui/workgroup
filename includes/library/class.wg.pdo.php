<?php
/**
 * PDO Database Driver Handle.
 */
class WG_PDO implements WG_Database{

    private $dsn;
    private $user;
    private $pwd;
    private $host;
    private $port;
    private $charset;
    private $dbname;

    private $stmt;
    private $dbh;

    /**
     * @param $dsn
     * @param $user
     * @param $pwd
     * @return PDO|string
     */
    public function connect($dsn, $user, $pwd){
        try {
            return new PDO( $dsn, $user, $pwd, array(PDO::ATTR_PERSISTENT));
        } catch ( PDOException $e ){
            return $e -> getMessage();
        }
    }

    /**
     * @param $sql
     * @return bool|string
     */
    public function query( $sql ){
        if( empty($sql) ) return false;

        if( strtolower(substr(trim($sql),0,6)) == 'select' ){
            try {
                $rs = '';
                $stmt = $this -> dbh -> query( $sql );
                if( $stmt ){
                    $stmt -> setFetchMode(PDO::FETCH_OBJ);
                    $rs = $stmt -> fetchAll();
                }
                return $rs;
            } catch ( PDOException $e ){
                return $e -> getMessage();
            }
        }else{
            try {
                return $this -> dbh -> exec( $sql );
            } catch ( PDOException $e ){
                return $e -> getMessage();
            }
        }
    }

    /**
     *  begin a transaction process, and reset transaction pool.
     */
    public function beginTransaction(){
        $this -> dbh -> beginTransaction();
    }

    /**
     * Try to execute the SQL statements and commit it.
     * @return string
     */
    public function commit(){
        $this -> dbh -> commit();
    }

    /**
     * @param $statement
     * @return bool
     */
    public function prepare($statement){
        if( empty($statement) ) return false;

        if( is_array( $statement ) ){
            echo $this -> arrayToSQLStatement( $statement );
        } else {
            $this -> stmt = $this -> dbh -> prepare( $statement );
            return $this -> stmt;
        }
    }

    /**
     * Execute SQL statement which prepared.
     */
    public function exec(){
        $params = func_get_args();

        if( empty($params) || empty($this -> stmt ))  return false;

        try {
            $rs = $this -> stmt -> execute( $params );
            return $rs;
        } catch ( PDOException $e ) {
            echo $e -> getMessage();
        }
    }

    public function __construct(){
        $this -> user    = DB_USER;
        $this -> pwd     = DB_PASSWORD;
        $this -> host    = DB_HOST;
        $this -> port    = DB_PORT;
        $this -> dbname  = DB_NAME;
        $this -> charset = DB_CHARSET;
        $this -> dsn     = "mysql:host=" . $this -> host . ";port=" . $this -> port . ";charset=" . $this -> charset . ";dbname=" . $this -> dbname;
        $this -> dbh     = $this -> connect( $this->dsn, $this->user, $this->pwd );
    }

    /**
     * Get a SQL statements through a setting array.
     * @param $setting
     * @return bool|string
     */
    private function arrayToSQLStatement( $setting ){
        if( gettype( $setting ) !== 'array' || empty($setting['action']) ) return false;

        $from    = '';
        if( !empty($setting['from']) ) $from = trim( $setting['from'] );
        elseif( !empty($setting['table']) ) $from = trim( $setting['table'] );
        else return false;

        $action  = trim( $setting['action'] );
        $fields  = trim( $setting['field'] );
        $where   = trim( $setting['where'] );
        $orderby = trim( $setting['orderby'] );
        $order   = trim( $setting['order'] );
        $limit   = trim( $setting['limit'] );

        $sql     = '';
        $orders  = array( 'asc', 'desc' );

        switch( $action ) {
            case 'select':
                if( empty($fields) ) $fields = '*';
                if( empty($order) || !in_array( strtolower($order), $orders) )  $order  = 'asc';

                $sql = 'select ' . $fields . ' from ' . $from . ' where ' . $where;

                if( !empty($orderby) )
                    $sql .= ' order by ' . $orderby . ' ' . $order;

                if( !empty($limit) )
                    $sql .= ' limit ' . $limit;
                break;

            case 'update':
                break;
            case 'insert':
                break;
            case 'delete':
                break;
        }

        return $sql;
    }
}
