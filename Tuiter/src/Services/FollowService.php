<?php

namespace Tuiter\Services;

use \Tuiter\Services\UserService;

class FollowService {

    private $collection;
    private $userService;

    public function __construct($collection, UserService $userService){
        $this->collection = $collection;
        $this->userService = $userService;
    }

    public function follow($followerId, $followedId): bool{
        $users = $this->getFollowed($followerId);
        $idUsers = array();
        foreach ($users as $user) {
            $idUsers[] = $user->getUserId();
        }
        if(!in_array($followedId, $idUsers)){
            $followId = md5(microtime());
            $this->collection->insertOne(
                array(
                    'followId' => $followId,
                    'followerId' => $followerId,
                    'followedId' => $followedId
                    )
            );
            return true;
        }
        return false;
    }

    public function getFollowers($userId): array{
        $raw = $this->collection->find(array('followedId' => $userId));
        $followers = array();
        foreach($raw as $follow){
            $followers[] = $this->userService->getUser($follow['followerId']);
        }
        return $followers;
    }

    public function getFollowed($userId): array{
        $raw = $this->collection->find(array('followerId' => $userId));
        $followed = array();
        foreach($raw as $follow){
            $followed[] = $this->userService->getUser($follow['followedId']);
        }
        return $followed;
    }
}