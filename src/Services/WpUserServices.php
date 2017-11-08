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
        $countries = array_flip($this->countryParser());
        if(!isset($mappings['fields']))
            exit;
        foreach ($mappings['fields'] as $wp => $hl) {
            if (isset($member['updated_fields'][$hl] )) {
                $val = $member['updated_fields'][$hl];
                if ($wp == 'billing_country' || $wp == 'shipping_country' ) {
                    $val = current($member['updated_fields'][$hl]);
                    $val = $countries[$val];
                }
                update_user_meta($id, $wp, $val);
            }
        }
    }

    public function unsubscribe($member)
    {
        if ($user = $this->getUser($member['member']['fields']['email'])) {
            update_user_meta($user->ID, 'hl_permission', 'off');
        }
    }
    protected function writelog ( $log )  {
        if ( is_array( $log ) || is_object( $log ) ) {
            error_log( print_r( $log, true ) );
        } else {
            error_log( $log );
        }
    }

    protected function countryParser()
    {
        return array(
            "DK" => 53,
            "AL" => 4,
            "AU" => 17,
            "BE" => 22,
            "CY" => 52,
            "EG" => 63,
            "FI" => 72,
            "FR" => 74,
            "DE" => 237,
            "HU" => 240,
            "IS" => 105,
            "IN" => 100,
            "IE" => 104,
            "JP" => 110,
            "MX" => 145,
            "HL" => 96,
            "NO" => 165,
            "PL" => 175,
            "PT" => 176,
            "RO" => 181,
            "RU" => 182,
            "ES" => 207,
            "SE" => 215,
            "TR" => 236,
            "GB" => 211,
            "US" => 242
        );
    }

}
