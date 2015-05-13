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

    private $transaction;
    private $pdo;

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
     * @param $param
     * @return bool|int|string
     */
    public function query( $param ){
        if( empty($param) ) return false;

        $statement = '';
        if( is_string($param) ){
            $statement = $param;
        } elseif( is_array($param) ) {
            $statement = $this -> arrayToSQLStatement( $param );
        }
        if( $statement ) {
            try {
                $stmt = $this -> pdo -> query( $statement );
                if ($stmt){
                    return $stmt -> rowCount();
                } else {
                    return 'Fail to execute SQL statement: ' . $statement;
                }
            } catch ( PDOException $e ){
                return $e -> getMessage();
            }
        } else {
            return false;
        }
    }

    /**
     *  begin a transaction process, and reset transaction pool.
     */
    public function beginTransaction(){
        $this -> pdo -> beginTransaction();
    }

    /**
     * Try to execute the SQL statements and commit it.
     * @return string
     */
    public function commit(){
        $this -> pdo -> commit();
    }

    /**
     * @param $query
     * @return bool|PDOStatement
     */
    public function prepare( $query ){
        if( empty($query) ) return false;

        $statement = '';
        if( is_array($query) ){
            $statement = $this -> arrayToSQLStatement( $query );
        } elseif (is_string($query)) {
            $statement = $query;
        } else {
            return false;
        }

        $this -> transaction = $this -> pdo -> prepare( $statement );
        return $this -> transaction;
    }

    /**
     * Execute SQL statement is prepared.
     */
    public function execute(){
        $params = func_get_args();
        $params = empty($params) ? array(null) : $params;

        if( empty($params) || empty($this -> transaction ))  return false;

        try {
            $rs = $this -> transaction -> execute( $params );
            if( $rs ) {
                return $this -> transaction -> rowCount();
            } else {
                return $rs;
            }
        } catch ( PDOException $e ) {
            return $e -> getMessage();
        }
    }

    public function select( $param, $returns = 'object' ){
        return $this -> _select($param, $returns, false);
     }

    public function selectOne( $param, $returns = 'object' ){
        return $this -> _select( $param, $returns, true);
    }

    public function close(){

    }

    public function __construct(){
        $this -> user    = DB_USER;
        $this -> pwd     = DB_PASSWORD;
        $this -> host    = DB_HOST;
        $this -> port    = DB_PORT;
        $this -> dbname  = DB_NAME;
        $this -> charset = DB_CHARSET;
        $this -> dsn     = "mysql:host=" . $this -> host . ";port=" . $this -> port . ";charset=" . $this -> charset . ";dbname=" . $this -> dbname;
        $this -> pdo     = $this -> connect( $this->dsn, $this->user, $this->pwd );
    }

    /**
     * Get a SQL statements through a setting array.
     * @param $setting
     * @return bool|string
     */
    private function arrayToSQLStatement( $setting ){
        if( !is_array($setting) || empty($setting['action']) || empty($setting) ) return false;

        $table    = '';
        if( !empty($setting['from']) ) $table = trim( $setting['from'] );
        elseif( !empty($setting['table']) ) $table = trim( $setting['table'] );
        else return false;

        $action = trim( $setting['action'] );
        $fields = trim( $setting['fields'] );
        $where  = trim( $setting['where'] );
        $order  = trim( $setting['order'] );
        $limit  = trim( $setting['limit'] );
        $values = trim( $setting['values'] );

        $sql     = '';

        switch( $action ) {
            case 'select':
                //SELECT `id`, `test`, `test2`, `test3` FROM `wg_test` WHERE 1
                //SELECT * FROM `wg_test` WHERE 1
                if( empty($fields) ) $fields = '*';

                $sql = 'select ' . $fields . ' from ' . $table;

                if( !empty($where) ) {
                    $sql .= ' where ' . $where;
                } else {
                    return false;
                }

                if( !empty($order) )
                    $sql .= ' order by ' . $order;

                if( !empty($limit) )
                    $sql .= ' limit ' . $limit;
                break;

            case 'insert':
                //INSERT INTO `wg_test`(`id`, `test`, `test2`, `test3`) VALUES ([value-1],[value-2],[value-3],[value-4])
                $sql = "insert into " . $table;
                if( empty($fields) ) return false;
                else $sql .= ' (' . $fields . ')';

                if( empty($values) ) return false;
                else $sql .= ' values (' . $values . ')';

                break;

            case 'update':
                if( empty($fields) || empty($values) )  return false;
                $fieldsArr   = explode(',', $fields);
                $valuesArr   = explode(',', $values);
                $filedsCount = count($fieldsArr);
                $valuesCount = count($valuesArr);

                if( empty($fieldsArr) || empty($valuesArr) || $filedsCount > $valuesCount ) return false;

                //UPDATE `wg_test` SET `id`=[value-1],`test`=[value-2],`test2`=[value-3],`test3`=[value-4] WHERE 1
                $sql = "update " . $table . ' set ';

                foreach( $fieldsArr as $n => $f ) {
                    $sql .= $f . ' = ' . $valuesArr[$n] . ', ';
                }
                $sql = substr( $sql, 0, strlen($sql)-2);

                if( !empty($where) ) {
                    $sql .= ' where ' . $where;
                } else {
                    return false;
                }

                break;

            case 'delete':
                //DELETE FROM `wg_test` WHERE 1
                $sql = 'delete from ' . $table;

                if( !empty($where) ) {
                    $sql .= ' where ' . $where;
                } else {
                    return false;
                }

                break;
        }

        return $sql;
    }

    private function _select( $param, $returns, $single = 'true' ){
        if( empty($param) ) return false;
        $statement = '';
        if( is_array($param) ) {
            $param['action'] = 'select';
            $statement = $this -> arrayToSQLStatement($param);
        } elseif( is_string($param) ) {
            $statement = $param;
        }

        try {
            $stmt = $this -> pdo -> query( $statement );
            if( $stmt ){
                $type = strtolower($returns) === 'array' ? PDO::FETCH_ASSOC : PDO::FETCH_OBJ;
                $stmt -> setFetchMode( $type );
                if( $single === false ){
                    $rs = $stmt ->fetchAll();
                    return $rs;
                } else {
                    $rs = $stmt -> fetch();
                    if( false === $rs ){
                       return array();
                    }else{
                       return $rs;
                    }
                }
            } else {
                return 'Fail to execute SQL statement: ' . $statement;
            }
        } catch ( PDOException $e ) {
            return $e -> getMessage();
        }

    }
}
