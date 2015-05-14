<?php
/**
 * PDO Database Driver Handle.
 */
class WG_PDO implements WG_Database{

    /**
     * @var string
     */
    private $dsn;
    /**
     * Database user.
     * @var string
     */
    private $user;
    /**
     * Database password required.
     * @var string
     */
    private $pwd;
    /**
     * Database host
     * @var string
     */
    private $host;
    /**
     * Port number
     * @var int
     */
    private $port;
    /**
     * Charset of database connection.
     * @var string
     */
    private $charset;
    /**
     * Database name which used.
     * @var string
     */
    private $dbname;
    /**
     * Save a PDOstatement if prepared.
     * @var PDOstatement
     */
    private $_prepare = null;
    /**
     * @var bool
     */
    private $_transaction = false;
    /**
     * Save SQL statements in a transaction process.
     * @var array
     */
    private $_transactionQueries = array();
    /**
     * PDO handler.
     * @var PDO|string
     */
    private $pdo;
    /**
     * @var bool
     */
    private $isSaveQueries = false;
    /**
     * Log the last SQL statement.
     * @var String
     * @access public
     */
    public static $queryString = null;
    /**
     * Log down SQL have been executed successful.
     * @var Array
     */
    public static $queries = null;
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

        $statement = $this -> getSQLStatement( $param );

        if( $statement ) {
            try {
                $stmt = $this -> pdo -> query( $statement );
                if ($stmt){
                    $this -> _queriesSaver( $statement );
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
     *  begin a transaction process.
     */
    public function beginTransaction(){
        $this -> pdo -> beginTransaction();
        $this -> _transaction = true;
    }
    /**
     * Try to execute the SQL statements and commit it.
     * @return string
     */
    public function commit(){
        return $this -> pdo -> commit();
       // $this -> _transaction = false;
    }
    /**
     * @param $query
     * @return bool|PDOStatement
     */
    public function prepare( $param ){
        if( empty($param) ) return false;

        $this -> _prepare = null;
        $statement = $this -> getSQLStatement( $param );
        $this -> _prepare = $this -> pdo -> prepare( $statement );
        return $this -> _prepare -> queryString;
    }
    /**
     * Execute a SQL statement is prepared.
     */
    public function execute(){
        $params = func_get_args();
        if( empty($params) || empty($this -> _prepare ))  return false;

        try {
            $rs = $this -> _prepare -> execute( $params );
            if( $rs ) {
                $this -> _queriesSaver( $this -> _prepare -> queryString, $params );
                return $this -> _prepare -> rowCount();
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

    public function update( $param ){

    }

    public function insert( $param ){

    }

    public function delete( $param ){

    }

    public function close(){
        $this -> pdo = null;
        $this -> _prepare = null;
        $this -> dsn = null;
    }

    public function __construct(){
        if(!class_exists( 'PDO' )) exit("PDO is not supproted, you can set DB_DRIVER = 'mysql' or 'mysqli' in wg-config.php");
        if( true === WG_SAVEQUERIES ){
            self::$queries = array();
            $this -> isSaveQueries = true;
        }

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
     * @param $param A SQL statement or Array data.
     * @return bool|string
     */
    private function getSQLStatement( $param ){
        if( is_string($param) ) return $param;

        if( empty($param['action']) || empty($param) ) return false;

        if( is_array( $param ) ){
            if( !empty($param['from']) ) $table = trim( $param['from'] );
            elseif( !empty($param['table']) ) $table = trim( $param['table'] );
            else return false;

            $action = trim( $param['action'] );
            $fields = trim( $param['fields'] );
            $where  = trim( $param['where'] );
            $order  = trim( $param['order'] );
            $limit  = trim( $param['limit'] );
            $values = trim( $param['values'] );

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
                default:
                    return false;
                    break;
            }

            return $sql;
        }

        return false;
    }


    /**
     * @param $query
     * @param array $params
     * @return bool
     */
    private function _queriesSaver( $query, $params = array() ){
        if( !$this -> isSaveQueries )    return false;
        $querystr = $query;

        if (!empty($params)) {
            foreach ($params as $p) {
                if (is_string($p)) $p = "'$p'";
                $querystr = preg_replace('/\?/', $p, $querystr, 1);
            }
        } else {
            $querystr = $query;
        }

        if( false === $this -> _transaction ) {
            array_push( self::$queries, $querystr );
            self::$queryString = $querystr;
        } else {
            array_push( $this -> _transactionQueries, $querystr );
        }

        return true;
    }

    /**
     * @param $param
     * @param $returns
     * @param string $single
     * @return array|bool|mixed|string
     */
    private function _select( $param, $returns, $single = 'true' ){
        if( empty($param) ) return false;
        $statement = '';
        if( is_array($param) ) {
            $param['action'] = 'select';
            $statement = $this -> getSQLStatement($param);
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
