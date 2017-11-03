<?php

namespace Heyloyalty\Services;

class WpUserServices
{
    public function getUser($email)
    {
        return get_user_by('email',$email);
    }

    public function upsert($member)
    {
        if ($user = $this->getUser($member['current_fields']['email'])) {
            $this->update($user->ID,$member);
        }
    }

    public function update($id,$member)
    {
        $userdata = array('ID' => $id, 'user_email' => $member['current_fields']['email']);
        $mappings = get_option('hl_mappings');

        if(!isset($mappings['updated_fields']))
            exit;
        foreach ($mappings['updated_fields'] as $wp => $hl) {
            if (isset($member['updated_fields'][$hl])) {
                update_user_meta($id, $wp, $member['updated_fields'][$hl]);
            }
        }
    }

    public function unsubscribe($member)
    {
        if ($user = $this->getUser($member['member']['fields']['email'])) {
            update_user_meta($user->ID, 'hl_permission', 'off');
        }
    }
    protected function write_log ( $log )  {
        if ( is_array( $log ) || is_object( $log ) ) {
            error_log( print_r( $log, true ) );
        } else {
            error_log( $log );
        }
    }

}
