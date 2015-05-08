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
    private  $transactionpool;

    public  $dbh;

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

    public function select( $sql ){
        if( empty($sql) )  return false;
        try {
            $args = func_get_args();
            var_dump($args);


            //return $rs;

        } catch( PDOException $e) {
            return $e;
        }
    }


    /**
     *  begin a transaction process, and reset transaction pool.
     */
    public function beginTransaction(){
        $this -> transactionpool = array();
    }

    /**
     * @param $statement
     * @param array $params
     * @return bool
     */
    public function preare($statement){
        if( gettype($statement) !== 'string' ) return false;
        $args = func_get_args();

        if( count($args) === 1 ) {
            array_push( $this -> transactionpool, $statement);
        } else {
            array_push( $this -> transactionpool, array( 'sql' => $statement, 'params' => (array)$params ) );
        }
        return true;
    }

    /**
     * Try to execute the SQL statements and commit it.
     */
    public function commit(){
        try {
            $statements =  $this -> transactionpool;
            $this -> dbh -> beginTransaction();

            foreach( $statements as $index => $statement ) {
                if( gettype($statement) === 'string' ){
                    $stmt =  $this -> dbh -> prepare( $statement );
                    $stmt -> execute();
                } elseif ( gettype($statement) === 'array' ) {
                    $stmt =  $this -> dbh -> prepare( $statement['sql'] );
                    foreach( $statement['params'] as $v ){
                       $stmt -> execute( (array)$v );
                    }
                }
            }
            $this -> dbh ->commit();
            $this -> transactionpool = array();
        } catch(PDOException $e) {
            $this -> dbh -> rollBack();
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
}
