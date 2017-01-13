<?php
/*
 * This file is part of wp-heyloyalty.
 *
 * Copyright (c) 2015 Heyloyalty.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Heyloyalty\Services;

use Carbon\Carbon;
use Heyloyalty\Services\HeyloyaltyServices;

/**
 * Class AdminServices
 * Service layer for interacting with wordpress api.
 * @package Heyloyalty\Services
 */
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
     * Get date.
     * @param $string
     * @return string
     */
    public function getDate($string)
    {
        $string = str_replace("/", "-", $string);
        return Carbon::parse($string)->toDateString();
    }

    /**
     * Get heyloyalty member id
     *
     * @param $user_id
     * @return null
     */
    public function getMemberID($user_id)
    {
        if($member_id = get_user_meta($user_id,'member_id',true)) {
            return $member_id;
        }else{
            $this->setMemberID($user_id);
        }

        if($member_id = get_user_meta($user_id,'member_id',true))
            return $member_id;

        return null;
    }

    public function setMemberID($user_id)
    {
        if($user = get_user_by('ID',$user_id ))
        {
            $list_id = $this->getListID();
            $filter = array('filter' => array('email' => array('eq' => array($user->user_email))));
            $HL_user = $this->HlServices->getMemberByFilter($list_id,$filter);

            if(isset($HL_user['members']['id']))
                update_user_meta($user_id, 'member_id', $HL_user['members']['id']);
        }
    }
    
    /**
     * Get user fields.
     * Gets all meta fields from any user except blacklist fields.
     * @return array
     */
    public function getUserFields()
    {
        $blacklist = [
            'rich_editing',
            'comment_shortcuts',
            'admin_color',
            'use_ssl',
            'show_admin_bar_front',
            'wp_capabilities',
            'wp_user_level',
            'dismissed_wp_pointers',
            'show_welcome_panel',
            'wp_dashboard_quick_press_last_post_id',
            'manageedit-shop_ordercolumnshidden',
            'wp_user-settings',
            'wp_user-settings-time',
            'manageedit-nf_subcolumnshidden',
            '_woocommerce_persistent_cart',
            'session_tokens',
            'member_id',
            'billing_email'
        ];

        global $wpdb;
        $usermeta = $wpdb->prefix.'usermeta';
        $query = 'select meta_key from '.$usermeta;
        $userMetaKeys = $wpdb->get_results($query);
        $metaKeys = [];
        foreach ($userMetaKeys as $key) {
            if(!in_array($key->meta_key,$metaKeys) && !in_array($key->meta_key,$blacklist))
            {
                array_push($metaKeys,$key->meta_key);
            }
        }
        return $metaKeys;
    }

    /**
     * save choice options.
     * @param $fields
     */
    public function saveListFieldChoiceOptions($fields)
    {
        $choices = [];
        foreach ($fields as $key => $value) {
            if($value['format'] == 'choice' || $value['format'] == 'multi' || $value['format'] == 'shop')
            {
                $choices[$key] = $value;
            }
        }
        update_option('choice_options',$choices);
    }

    /**
     * Map user fields.
     * gets the values to send as an array for a request.
     * @param $metadata
     * @return array
     */
    protected function mapUserFields($metadata,$user_id)
    {
        $mappings = get_option('hl_mappings');

        if(!isset($mappings['fields']) && !isset($mappings['formats']))
            return array();

        $fields = $mappings['fields'];
        $formats = $mappings['formats'];

        if(!is_array($metadata))
            return array();

        $mapped = [];
        foreach($fields as $key => $value)
        {
            if(isset($metadata[$key][0]))
            $mapped[$value] = $metadata[$key][0];

            if($key == 'user_registered')
                $mapped[$value] = $this->getRegisteredDate($user_id);

            switch ($formats[$value])
            {
                case 'date':
                $mapped[$value] = $this->getDate($metadata[$key][0]);
                    break;
                case 'choice':
                case 'multi':
                case 'shop':
                    $mapped[$value] = $this->getOptionIds($value,$metadata[$key][0]);
                    break;
            }
        }
        
        return $mapped;
    }

    /**
     * Get option ids.
     * Gets ids from field options 
     * @param $field
     * @param $values
     * @return array
     */
    protected function getOptionIds($field, $values)
    {
        $fields = get_option('choice_options');

        $options = $fields[$field];
        $arr = [];
        foreach(array_flip($options['options']) as $value)
        {
            if(is_array($values))
            {
                $key = array_search($values,$value);

                if($key)
                    array_push($arr,$values[$key]);
            }else{
                if($value == $values)
                    array_push($arr,$values);
            }
        }
        return $arr;
    }

    /**
     * prepare member.
     * Ensure email is sent with user fields.
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
            $this->setError('error',$user->user_email.'-'.$e->getMessage());
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
            $this->setError('error',$user->user_email.'-'.$e->getMessage());
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
            $e->detail->ExceptionDetail->InnerException->Message;
            $this->setError('error',$user->user_email.'-'.$e->getMessage());
            return 0;
        }
        return $user_id;
    }

    /**
     * Set error
     * @param $message
     */
    public function setError($type = 'error',$message)
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
    public function setStatus($type,$message)
    {
        $status = get_option('status');
        $status['entry-'.Carbon::now()] = array('type' => $type,'message'=> $message);
        update_option('status',$status);
    }
    
    /**
     * Get permission field.
     * @return null
     */
    protected function getPermissionField()
    {
        $settings = get_option('hl_settings');
        $permission = (isset($settings['hl_permission'])) ? $settings['hl_permission'] : null;
        return $permission;
    }
    
    /**
     * Get user by id.
     * @param $user_id
     * @return user
     */
    protected function getUser($user_id)
    {
        return get_user_by('id',$user_id);
    }
}