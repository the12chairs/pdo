<?php


@include_once('base_class.php');

class Users extends DbConnect {

  public function __construct($attributes = array(), $tbl){
        parent::__construct($attributes, "users");
        /*$this->table = "users";*/
    }



}


// Tests
$lol = new Users(["id_user" => "", "login" => "Gimldfghdfhdfghi", "email" => "sometext@t.t", "password" => "q1w2e3r4t5y6"], "users");

$lol = $lol->findByPk(2);
$lol->attr['login'] = "Пробаsdrgsdghsdg";
var_dump ($lol);
$lol->save();




?>