<?php

namespace TestTuiter\Services;

use \Tuiter\Services\UserService;

final class UserServiceTest extends \PHPUnit\Framework\TestCase {
    private $collection;

    protected function setUp(): void{
        $conn = new \MongoDB\Client("mongodb://localhost");
        $this->collection = $conn->Tuiter->usuarios;
        $this->collection2 = $conn->Tuiter2->usuarios;
        $this->collection3 = $conn->Tuiter3->usuarios;
        $this->collections = array($this->collection,$this->collection2,$this->collection3);
        $this->collections[0]->drop();
        $this->collections[1]->drop();
        $this->collections[2]->drop();
    }


    public function testExisteClase() {
        $this->assertTrue(class_exists("\Tuiter\Services\UserService"));
    }
    public function testRegisterOk(){
        $us = new UserService($this->collections);
        $user= $us->register("mati23", "1234", "matias");
        $this->assertTrue($user);

    }
    public function testRegisterUsers(){
        $us = new UserService($this->collections);
        $user= $us->register("mati23", "1234", "matias");
        $this->assertTrue($user);
        $user2= $us->register("lucho23", "1234", "luciano");
        $this->assertTrue($user2);
    }
    public function testRegisterSameUser(){
        $us = new UserService($this->collections);
        $user= $us->register("mati23", "1234", "matias");
        $this->assertTrue($user);
        $user2= $us->register("mati23", "1234", "luciano");
        $this->assertFalse($user2);
    }

    public function testGetUser(){
        $us = new UserService($this->collections);
        $us->register("mati23", "1234", "matias");
        $user=$us->getUser('mati23');
        $this->assertEquals($user->getUserId(), 'mati23');
    }

    public function testGetUserNotExist(){
        $us = new UserService($this->collections);
        $us->register("mati23", "1234", "matias");
        $user=$us->getUser('culo44');
        $this->assertEquals($user->getUserId(), 'Null');
    }

    public function testRegisterThousandsOfUsers(){
        $us = new UserService($this->collections);
        for($i=0;$i<1000;$i++){
            $this->assertTrue($us->register("mati".$i, "1234", "matias"));
            $user = new \Tuiter\Models\User("mati".$i, "1234", "matias");
            $this->assertEquals($user,$us->getUser($user->getUserId()));
        }
    }
}