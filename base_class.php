<?php

include_once("interface.php");



// Base class
class DbConnect implements iDbConnectable {
    
    public $attr; // Table row
    protected $table;  // Table name

    public function __construct($attributes = array(), $tbl){
        //Connection
        try {

            $this->dbh = new PDO("mysql:host=localhost;dbname=instaclone", 'root', '');
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }

        $this->attr = $attributes;
        $this->table = $tbl;

    }

    // Dont repeat yourself
    private function getPk(){
        $sql = "SHOW KEYS FROM `$this->table` WHERE Key_name = 'PRIMARY'";
        $q = $this->dbh->prepare($sql);
        $q->execute();
	$res = $q->fetch(PDO::FETCH_ASSOC);
	return $res['Column_name'];
	//var_dump($res);
    }

    // Work

    public function findByPk($pk) {
        // Find pk

        $id = $this->getPk();
	// Use pk
        $sql = "SELECT * FROM `$this->table` WHERE `$id` = ?";
        $q = $this->dbh->prepare($sql);
        $q->execute(array($pk));
        $res = $q->fetch(PDO::FETCH_ASSOC);
        /*var_dump($res);*/
        return new DbConnect($res, $this->table);
    }


    // Work fine
    public function save(){

        //[bydlocode on]
        // Build sql query for template
        $attribs = implode(", ", array_keys($this->attr));
        $vals = ":";
        foreach(explode(", ",$attribs) as $v){
            $v .= ", :";
            $vals .= $v;
        }
        $vals = substr_replace($vals, "", -3);
        //[bydlocode off]


        if($this->attr[array_keys($this->attr)[0]] == ""){ // attr[0] = id_elem!
            // inserting
            $sql = "INSERT INTO $this->table ($attribs) VALUES ($vals)"; 

            $q = $this->dbh->prepare($sql);
            /*var_dump($this->attr);*/
            $q->execute($this->attr);
        }
        else {

            // Build sql query for updating 
            $id = array_keys($this->attr)[0];


            $up = "";
            foreach($this->attr as $key => $value){
                $up .= $key . " = '" . $value. "', ";
            }
            $up = substr_replace($up, "", -2);

            // Do work
            $ident =  $this->attr[array_keys($this->attr)[0]];
            $sql = "UPDATE $this->table SET $up WHERE $id = $ident";

            $q = $this->dbh->prepare($sql);
            $q->execute($this->attr);
        }

        return $this;
    }


    // Tested
    public function where_one($attribute, $value){
        // Simple select
        $sql = "SELECT * FROM $this->table WHERE `$attribute` = ?";
        $q = $this->dbh->prepare($sql);
        $q->execute(array($value));
        $resArr = array();
        foreach($q->fetchAll(PDO::FETCH_ASSOC) as $res){
            /*var_dump($res);*/
            $tmp = new DbConnect($res, $this->table);

            array_push($resArr, $tmp);
        }
        return $resArr;
    }



    // More complex select
    public function where($attribute, $value, $with = false){
        
        $resArr = array(); // Result
        


        if(!$with){
            // Like old version
            $sql = "SELECT * FROM $this->table WHERE `$attribute` = ?";
            $q = $this->dbh->prepare($sql);
            $q->execute(array($value));

            foreach($q->fetchAll(PDO::FETCH_ASSOC) as $res){
                /*var_dump($res);*/
                $tmp = new DbConnect($res, $this->table);

                array_push($resArr, $tmp);
            }
        }
        // inner join 
        else {
	    $id = $this->getPk();
            // I've should done it like this?  
	    // NOTE: Only for linked tables!
            $sql = "SELECT * FROM $with INNER JOIN $this->table ON `$with`.`$id` = `$this->table`.`$id` WHERE `$with`.`$id` = ?";
            $q = $this->dbh->prepare($sql);
            $q->execute(array($value)); // M-M-MONSTER KILL
            //echo $sql. "\n";
            foreach($q->fetchAll(PDO::FETCH_ASSOC) as $res){
                $tmp = new DbConnect($res, $with);

                array_push($resArr, $tmp);
            }

        }
        return $resArr;
    }

}





//Children, each have it's own constructor for columns and table name


// Look
// users.php
// photos.php


?>