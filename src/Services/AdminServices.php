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
     * Get email.
     *
     * @param $id
     * @return string
     */
    public function getEmail($id)
    {
        if($user = get_userdata($id))
            return $user->email;

        return null;
    }

    /**
     * Get heyloyalty member id
     *
     * @param $user_id
     * @return null
     */
    public function getMemberID($user_id)
    {
        if($member_id = get_user_meta($user_id,'member_id',true))
            return $member_id;

        return null;
    }


    /**
     * Map user fields.
     *
     * @param $metadata
     * @return array
     */
    protected function mapUserFields($metadata)
    {
        $mappings = get_option('hl_mappings');

        if(!isset($mappings['fields']))
            return array();

        $mappings = $mappings['fields'];

        if(!is_array($metadata))
            return array();

        $mapped = [];
        foreach($mappings as $key => $value)
        {
            if(isset($metadata[$key][0]))
            $mapped[$value] = $metadata[$key][0];
        }

        return $mapped;
    }

    /**
     * @param $user_id
     * @return array
     */
    protected function prepareMember($user_id)
    {
        $email = $this->getEmail($user_id);

        if(is_null($email))
            return array();

        $metadata = get_user_meta($user_id);
        $params = $this->mapUserFields($metadata);
        $params['email'] = $email;

        return $params;
    }

    /**
     * Get list id.
     *
     * @return null
     */
    protected function getListID()
    {
        $mappings = get_option('hl_mappings');

        if(!isset($mappings['list_id']))
            return null;

        return $mappings['list_id'];
    }

    /**
     * Add heyloyalty member.
     *
     * @param $user_id
     * @return int
     */
    public function addHeyloyaltyMember($user_id)
    {
        $list_id = $this->getListID();
        $params = $this->prepareMember($user_id);

        try{
            $response = $this->HlServices->createMember($params,$list_id);
            $response = add_user_meta($user_id,'member_id',$response['id'],true);
        }catch (\Exception $e)
        {
            return 0;
        }

        return $user_id;
    }

    /**
     * Update heyloyalty member.
     *
     * @param $user_id
     * @return int
     */
    public function updateHeyloyaltyMember($user_id)
    {
        $list_id = $this->getListID();
        $params = $this->prepareMember($user_id);
        $member_id = $this->getMemberID($user_id);
        try{
            $response = $this->HlServices->updateMember($params,$list_id,$member_id);
        }catch (\Exception $e)
        {
            return 0;
        }
        return $user_id;
    }

    /**
     * Delete heyloyalty member.
     *
     * @param $user_id
     * @return int
     */
    public function deleteHeyloyaltyMember($user_id)
    {
        $member_id = $this->getMemberID($user_id);
        $list_id = $this->getListID();

        try{
            $response = $this->HlServices->deleteMember($list_id,$member_id);
        }catch (\Exception $e)
        {
            return 0;
        }
        return $user_id;
    }
}