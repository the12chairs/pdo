<?php
@include_once("interface.php");


class DbConnect /* implements iDbConnectable*/ {
    
    public $attr;
    protected $table;

    public function __construct($attributes = array()){
        //Connection
        try {
            $this->dbh = new PDO("mysql:host=localhost;dbname=instaclone", 'root', '123456');
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }

        $this->attr = $attributes;
        $this->table ="Never exists";

    }

    public function findByPk($pk) {
        $id = array_keys($this->attr)[0];
        $sql = "SELECT * FROM `$this->table` WHERE `$id` = ?";
        $q = $this->dbh->prepare($sql);
        $q->execute(array($pk));
        $res = $q->fetch();
        return new DbConnect($res);
    }

    public function save(){
        if($this->id != null){
            $sql = "INSERT INTO users (login, email, password) VALUES (:login, :email, :password)";
            $q = $this->dbh->prepare($sql);
            $q->execute($this->attr);
        }
        else {
            $sql = "UPDATE users SET (login, email, password) VALUES (:login, :email, :password)";
            $q = $this->dbh->prepare($sql);
            $q->execute($this->attr);
        }
        return $this;
    }

    public function where($attribute, $value, $with = false){
        $sql = "SELECT login, email, password FROM users WHERE `$attribute` = ?";
        $q = $this->dbh->prepare($sql);
        $q->execute(array($value));
        $resArr = array();
        foreach($q->fetchAll() as $res){
            $tmp = new DbConnect(["login" => $res['login'], "email" => $res['email'], "password" => $res["password"]]);

            array_push($resArr, $tmp);
        }
        return $resArr;
    }
}




class Users extends DbConnect {

    public function __construct($attributes = array()){
        parent::__construct($attributes);
        $this->table = "users";
    }



}

class Photos extends DbConnect {
    public function __construct($attributes = array()){
        parent::__construct($attributes);
        $this->table = "photos";
    } 
}

$lol = new Users(["id_user" => "", "login" => "Gimli", "email" => "sometext@t.t", "password" => "q1w2e3r4t5y6"]);

$lol = $lol->findByPk(2);

var_dump($lol);

$photo = new Photos(["id_photo" => "", "picture" => "xxx.png", "id_user" => 1, "private" => true]);
$photo = $photo->findByPk(1);
var_dump($photo);

?>