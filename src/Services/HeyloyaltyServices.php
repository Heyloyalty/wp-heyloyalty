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

use Phpclient\HLClient;
use Phpclient\HLMembers;
use Phpclient\HLLists;

/**
 * Class HeyloyaltyServices.
 * Uses the Heyloyalty api to create, update and delete members.
 * @package Heyloyalty\Services
 */
class HeyloyaltyServices {

    const HOST = 'https://api.heyloyalty.com';
    const ENDPOINTTYPE = '/loyalty/v1';
    protected $client;
    protected $memberService;
    protected $listService;

    /**
     * HeyloyaltyServices constructor.
     */
    public function __construct()
    {
        $cred = $this->getCredentials();
        $this->client = new HLClient($cred['api_key'],$cred['api_secret']);
        $this->memberService = new HLMembers($this->client);
        $this->listService = new HLLists($this->client);

    }


    /**
     * Create member.
     * @param $params
     * @param $list_id
     * @return array|mixed
     */
    public function createMember($params,$list_id)
    {
        return $this->memberService->create($list_id,$params);
    }

    /**
     * Update member.
     * @param $params
     * @param $list_id
     * @param $member_id
     * @return array|mixed
     */
    public function updateMember($params,$list_id,$member_id)
    {
        return $this->memberService->update($list_id,$member_id,$params);
    }

    /**
     * Delete member.
     * @param $list_id
     * @param $member_id
     * @return array|mixed
     */
    public function deleteMember($list_id,$member_id)
    {
        return $this->memberService->delete($list_id,$member_id);
    }

    /**
     * Get list by id.
     * @param $list_id
     * @return array|mixed
     */
    public function getList($list_id)
    {
        return $this->listService->getList($list_id);
    }

    /**
     * Get lists.
     * Gets all lists from an account.
     * @return array|mixed
     */
    public function getLists()
    {
        return $this->listService->getLists();
    }

    public function getMemberByFilter($list_id,$filter)
    {
        return $this->memberService->getMembersByFilter($list_id,$filter);
    }
    /**
     * Get credentials.
     * @desc get credentials from client
     * @return array
     */
    private function getCredentials()
    {
        $credentials = get_option('hl_settings');
        if (isset($credentials['api_key']) && isset($credentials['api_secret'])) return $credentials;

        return null;
    }

    /**
     * Response handler.
     * @param $code
     * @return bool
     * @throws \Exception
     */
    private function responseHandler($code)
    {
        switch ($code) {
            case $code > 199 && $code < 299:
                return true;
                break;
            case $code == 400:
                throw new \Exception('Bad request', 400);
                break;
            case $code == 403:
                throw new \Exception('Not authorized', 403);
                break;
            default:
                throw new \Exception('Server error', 500);

        }
    }

    /**
     * Response to array.
     * @param $response
     * @return array
     */
    private function responseToArray($response)
    {
        return json_decode($response, true);
    }
}
