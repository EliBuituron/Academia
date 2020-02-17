<?php

namespace Tuiter\Services;

class UserService {

    private $collections = array();

    public function __construct(array $collections){
        $this->collections = $collections;
    }
    
    public function register(string $userId, string $name, string $password) {
        $user = $this->getUser($userId);
        if($user instanceof \Tuiter\Models\UserNull){
            $usuarios= array();
            $id = md5($userId);
            $numero = 0;
            for($i=0;$i<strlen($userId);$i++){
                $numero += ord($id[$i]);
            }
            $usuarios['userId']= $userId;
            $usuarios['name']= $name;
            $usuarios['password']=$password;
            $this->collections[$numero%count($this->collections)]->insertOne($usuarios);
            return true;
        } else {
            return false;
        }
    }
    public function getUser($userId){
        $id = md5($userId);
        $numero = 0;
        for($i=0;$i<strlen($userId);$i++){
            $numero += ord($id[$i]);
        }
        $cursor= $this->collections[$numero%count($this->collections)]->findOne(['userId'=> $userId]);
        if (is_null($cursor)){
            $user = new \Tuiter\Models\UserNull('','','');
            return $user;
        }
        $user = new \Tuiter\Models\User($cursor['userId'],$cursor['name'], $cursor['password']);
        return $user;
    }
}
