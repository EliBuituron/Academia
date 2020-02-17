<?php

namespace Tuiter\Services;

class ReshardingService {
    
    public function reshardingUsers(array $from, array $to){
        foreach($from as $collection){
            $data = $collection->find();
            foreach($data as $content){
                $us = new \Tuiter\Services\UserService($to);
                $us->register(
                    $content['userId'],
                    $content['name'],
                    $content['password']
                );
            }
            $collection->deleteMany([]);
        }
    }
}