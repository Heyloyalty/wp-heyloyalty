<?php


namespace Heyloyalty\Services;

use Carbon\Carbon;
use Heyloyalty\Services\HeyloyaltyServices;

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
            return $user->user_email;

        return null;
    }

    /**
     * Get registered date
     *
     * @param $user_id
     * @return null|string
     */
    public function getRegisteredDate($user_id)
    {
        if($user = get_userdata($user_id))
        {
            $date = Carbon::createFromFormat('Y-m-d H:i:s',$user->user_registered)->toDateString();
            return $date;

        }


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
    protected function mapUserFields($metadata,$user_id)
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

            //TODO find another way of injecting user or other specifik fields.
            if($key == 'user_registered')
                $mapped[$value] = $this->getRegisteredDate($user_id);
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
        $params = $this->mapUserFields($metadata,$user_id);
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
        $field = $this->getPermissionField();
        $user = $this->getUser($user_id);
        if(get_user_meta($user_id,$field,true) == '') {
            $this->setError('error','permission field could not be found');
            return 0;
        }

        $list_id = $this->getListID();
        $params = $this->prepareMember($user_id);

        try{
            $response = $this->HlServices->createMember($params,$list_id);
            delete_user_meta($user_id,'member_id');
            $response = add_user_meta($user_id,'member_id',$response['id'],true);
            $this->setStatus('created',$user->user_email.' on list '.$list_id);
        }catch (\Exception $e)
        {
            $this->setError('error',$user->user_email.': could not be created');
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
        $field = $this->getPermissionField();
        $user = $this->getUser($user_id);
        if(get_user_meta($user_id,$field,true) == '') {
            $this->setError('error','permission field could not be found');
            return 0;
        }
        $list_id = $this->getListID();
        $params = $this->prepareMember($user_id);
        $member_id = $this->getMemberID($user_id);
        try{
            $response = $this->HlServices->updateMember($params,$list_id,$member_id);
            $this->setStatus('updated',$user->user_email.' on list '.$list_id);
        }catch (\Exception $e)
        {
            $this->setError('error',$user->user_email.': could not be updated');
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
        $user = $this->getUser($user_id);
        $list_id = $this->getListID();

        try{
            $response = $this->HlServices->deleteMember($list_id,$member_id);
            $this->setStatus('deleted',$user->user_email.' from list '.$list_id);
        }catch (\Exception $e)
        {
            $this->setError('error',$user->user_email.': could not be deleted');
            return 0;
        }
        return $user_id;
    }

    /**
     * Set error
     * @param $message
     */
    protected function setError($type = 'error',$message)
    {
        $errors = get_option('errors');
        $errors['entry-'.Carbon::now()] = array('type' => $type,'message'=> $message);
        update_option('errors',$errors);
    }

    /**
     * Set status
     * @param $type
     * @param $message
     */
    protected function setStatus($type,$message)
    {
        $status = get_option('status');
        $status['entry-'.Carbon::now()] = array('type' => $type,'message'=> $message);
        update_option('status',$status);
    }

    protected function getPermissionField()
    {
        $settings = get_option('hl_settings');
        $permission = (isset($settings['hl_permission'])) ? $settings['hl_permission'] : null;
        return $permission;
    }
    protected function getUser($user_id)
    {
        return get_user_by('id',$user_id);
    }
}