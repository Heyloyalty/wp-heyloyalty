<?php


namespace Heyloyalty\Services;

use Heyloyalty\Services\HeyloyaltyServices as heyloyaltyServices;

class AdminServices {

    protected $HlServices;

    public function __construct()
    {
        $this->HlServices = new heyloyaltyServices();
    }

    /**
     * @param $id
     * @return string
     */
    public function getEmail($id)
    {
        $email = '';
        //TODO
        return $email;
    }

    /**
     * @return array
     */
    public function mapUserFields()
    {
        //TODO
        return array();
    }

    /**
     * @return int
     */
    public function addHeyloyaltyMember()
    {
        //TODO
        return $id = 0;
    }

    /**
     * @param $id
     * @return int
     */
    public function updateHeyloyaltyMember($id)
    {
        //TODO
        return $id = 0;
    }

    /**
     * @param $id
     * @return bool
     */
    public function deleteHeyloyaltyMember($id)
    {
        //TODO
        return true;
    }
}