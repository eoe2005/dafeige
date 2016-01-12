<?php
namespace g\db;
/**
 * @author eoe2005@qq.com
 * 
 */
class GDnPdo {
    protected static $_pdos = [];
    /**
     *
     * @var PDO 
     */
    protected $con;
    private function __construct($dsn,$username = null,$password = null,$options = null) {
        $this->con = new \PDO($dsn, $username, $passwd, $options);
    }
    public static function ins($dsn,$username = null,$password = null,$options = null){
        $key = md5($dsn.$username,$password,$options);
        if(isset(self::$_pdos[$key]) === false){
            self::$_pdos[$key] = new Gdb($dsn, $username, $password,$options);
        }
        return self::$_pdos[$key];
    }
    public function insert($table,$data){
        $args = [];
        foreach ($data AS $k => $v){
            $args[':'.$k] = $v;
        }
        $sth = $this->con->prepare(sprintf("INSERT INTO %s(%s) VALUES(%s)",$table,  implode(',', array_keys($data),  implode(',', array_keys($args)))));
        $sth->execute($args);
        return $this->con->lastInsertId();
    }
    public function update($table,$data,$where = null){
        $args = [];
        $set = [];
        foreach ($data AS $k->$v){
            $args[':'.$k] = $v;
            $set[] = sprintf("%s=:%s",$k,$k);
        }
        $sql = sprintf("UPDATE %s SET %s %s",$table,  implode(',', $set),(isset($where) ? 'WHERE '.$where : ""));
        if(func_num_args() > 3){
            $argv = func_get_args();
            array_shift($argv);
            array_shift($argv);
            array_shift($argv);
            foreach ($argv AS $k=>$v){
                $args[$k] = $v;
            }
        }
        $sth = $this->con->prepare($sql);
        $sth->execute($args);
        return $sth->rowCount();
    }
    public function delete($table,$where = null){
        $sql = sprintf("DELETE FROM %s %s",$table ,(isset($where) ? 'WHERE '.$where : ""));
        $sth = $this->con->prepare($sql);
        if(func_num_args() > 2){
            $args = func_get_args();
            array_shift($args);
            array_shift($args);
            $sth->execute($args);
        }else{
            $sth->execute();
        }
        return $sth->rowCount();
    }
    public function fetchAll($sql){
        $sth = $this->con->prepare($sql);
        if(func_num_args() > 1){
            $args = func_get_args();
            array_shift($args);
            $sth->execute($args);
        }else{
            $sth->execute();
        }
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }
    public function fetchOne($sql){
        $sth = $this->con->prepare($sql);
        if(func_num_args() > 1){
            $args = func_get_args();
            array_shift($args);
            $sth->execute($args);
        }else{
            $sth->execute();
        }
        return $sth->fetch(PDO::FETCH_ASSOC);
    }
    public function begin(){
        return $this->con->beginTransaction ();
    }
    public function rollBack(){
        return $this->con->rollBack();
    }
    public function commit(){
        return $this->con->commit ();
    }
    public function inTransaction(){
        return $this->con->inTransaction();
    }
    public function ddl($sql){
        
    }
    
}
