<?php


@include_once('base_class.php');

class Photos extends DbConnect {
  public function __construct($attributes = array(), $tbl){
        parent::__construct($attributes, "photos");
        /*$this->table = "photos";*/
    } 
}



@include_once('users.php');

$lol = new Users(["id_user" => "", "login" => "Gimldfghdfhdfghi", "email" => "sometext@t.t", "password" => "q1w2e3r4t5y6"], "users");

$photo = $lol->where("id_user", 1, "photos");

var_dump($photo);



$photo = new Photos(["id_photo" => "", "picture" => "xxx.png", "id_user" => 1, "private" => true], "photos");
$photo = $photo->findByPk(1);
$photo->save();

?>