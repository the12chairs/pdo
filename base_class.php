<?php
@include_once("interface.php");


class DbConnect implements iDbConnectable{
    
    static private $tableLock;
    private $attr;
    private $dbh;
    public $id,$login,$email,$password;

    public function __construct($attributes = array()){
        //Connection
        try {
            $this->dbh = new PDO("mysql:host=localhost;dbname=instaclone", 'root', '123456');
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }

        //Make all
        self::$tableLock = 'users'; // Bad style
        $this->attr = $attributes;
        $this->login = $this->attr['login'];
        $this->email = $this->attr['email'];
        $this->password = $this->attr['password'];

    }

    public function findByPk($pk) {
        $sql = "SELECT * FROM users WHERE id_user = $pk";
        $q = $this->dbh->prepare($sql);
        $q->execute();
        $res = $q->fetch();
        $this->login = $res['login'];
        $this->email = $res['email'];
        $this->password = $res['password'];
        $this->id = $res['id_user'];
        return $this;
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

    public function where($attribute, $value){
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

?>