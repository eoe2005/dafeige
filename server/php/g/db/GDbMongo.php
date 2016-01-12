<?php

/**
 * @author eoe2005@qq.com
 */
class GDbMongo {
    protected static $_slef = [];
    protected $mongo;
    /**
     * 
     * @param type $dns
     * @return GDbMongo
     */
    static function ins($dns){
        $key = md5($dns);
        if(isset(self::$_slef[$key]) === FALSE){
            self::$_slef[$key] = new GDbMongo($dns);
        }
        return self::$_slef[$key];
    }
    private function __construct($dns) {
        $this->mongo = new MongoDB\Driver\Manager($dns);
    }

    /**
     * 
     * @param type $dbname
     * @return GMongoDB
     */
    public function selectDB($dbname){
        return GMongoDB::ins($this->mongo, $dbname);
    }
}

class GMongoDB {

    protected static $_selfs = [];
    protected static $_tabs = [];
    protected $mongo;
    protected $dbname;
    protected $w;
    protected $r;
    
    /**
     * 
     * @param type $mogo
     * @param type $dbname
     * @return GMongoDB
     */
    public static function ins($mogo, $dbname) {
        $key = md5('%s_%s', spl_object_hash($mogo), $dbname);
        if (isset(self::$_selfs[$key]) === FALSE) {
            self::$_selfs[$key] = new GMongoDB($mogo, $dbname);
        }
        return self::$_selfs[$key];
    }

    private function __construct($mongo, $dbname) {
        $this->mongo = $mongo;
        $this->dbname = $dbname;
        $this->r = new MongoDB\Driver\ReadPreference(MongoDB\Driver\ReadPreference::RP_NEAREST);
        $this->w = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY);
    }

    public function selectDB($dbname) {
        return self::ins($this->mongo, $dbname);
    }

    protected function selectCollection($tname) {
        $key = md5(sprintf('%s_%s_%s', spl_object_hash($this->mongo), $this->dbname, $tname));
        if (isset(self::$_tabs[$key]) === false) {
            self::$_tabs[$key] = new GMongoCollection($this, $tname);
        }
        return self::$_tabs[$key];
    }

    private function getCName($tname) {
        return sprintf('%s.%s', $this->dbname, $tname);
    }

    public function insert($tname, $data) {
        $b = new MongoDB\Driver\BulkWrite();
        $b->insert($data);
        return $this->mongo->executeBulkWrite($this->getCName($tname), $b, $this->w);
    }

    public function find($tname, $filter, $options = []) {
        $query = new MongoDB\Driver\Query($filter, $options);
        $cur = $this->mongo->executeQuery($this->getCName($tname), $query, $this->r);
        return $cur->toArray();
    }

    public function delete($tname, $filter, $options = []) {
        $b = new MongoDB\Driver\BulkWrite();
        $b->delete($filter,$options);
        return $this->mongo->executeBulkWrite($this->getCName($tname), $b, $this->w);
    }
    public function update($tname, $filter,$data, $options = []) {
        $b = new MongoDB\Driver\BulkWrite();
        $b->update($filter,$data,$options);
        return $this->mongo->executeBulkWrite($this->getCName($tname), $b, $this->w);
    }

}

class GMongoCollection {

    protected $db;
    protected $tname;

    /**
     * 
     * @param type GMongoDB
     * @param type $tname
     */
    function __callStatic($db, $tname) {
        $this->db = $db;
        $this->tname = $tname;
    }

    public function find($filter, $options = []) {
        return $this->db->find($this->tname,$filter,$options);
    }

    public function findOne($filter, $options = []) {
        $options['limit'] = 1;
        $list = $this->find($filter, $options);
        if (count($list) > 0) {
            return $list[0];
        } else {
            return null;
        }
    }
    public function findOneByID($id){
        return  $this->findOne(['_id' => $this->getID($id)]);
    }

    public function update($data, $filter, $options = []) {
        return $this->db->update($this->tname,$filter,$data,$options);
    }

    public function insert($data) {
        return $this->db->insert($this->tname,$data);
    }

    public function delete($filter, $options = []) {
        return $this->db->delete($this->tname,$filter,$options);
    }
    public function getID($id){
        if($id instanceof  MongoDB\BSON\ObjectID){
            return $id;
        }else{
            return new MongoDB\BSON\ObjectID($id);
        }
    }

}
