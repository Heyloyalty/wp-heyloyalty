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

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

/**
 * Class HeyloyaltyServices.
 * Uses the Heyloyalty api to create, update and delete members.
 * @package Heyloyalty\Services
 */
class HeyloyaltyServices {

    const HOST = 'https://api.heyloyalty.com';
    const ENDPOINTTYPE = '/loyalty/v1';
    
    /**
     * Create member.
     * @param $params
     * @param $list_id
     * @return array|mixed
     */
    public function createMember($params,$list_id)
    {
        $response = $this->sendRequest('POST','/lists/'.$list_id.'/members',$params);
        $response = $this->responseToArray($response);
        return $response;
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
        $response = $this->sendRequest('PUT','/lists/'.$list_id.'/members/'.$member_id,$params);
        $response = $this->responseToArray($response);
        return $response;
    }
    
    /**
     * Delete member.
     * @param $list_id
     * @param $member_id
     * @return array|mixed
     */
    public function deleteMember($list_id,$member_id)
    {
        $response = $this->sendRequest('DELETE','/lists/'.$list_id.'/members/'.$member_id);
        $response = $this->responseToArray($response);
        return $response;
    }
    
    /**
     * Get list by id.
     * @param $list_id
     * @return array|mixed
     */
    public function getList($list_id)
    {
        $response = $this->sendRequest('GET','/lists/'.$list_id);
        $response = $this->responseToArray($response);
        return $response;
    }
    
    /**
     * Get lists.
     * Gets all lists from an account.
     * @return array|mixed
     */
    public function getLists()
    {
        $response = $this->sendRequest('GET','/lists/');
        $response = $this->responseToArray($response);
        return $response;
    }

    public function getMemberByFilter($list_id,$filter)
    {
        return $this->responseToArray($this->sendRequest('GET', '/lists/'.$list_id.'/members',$filter));
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
     * Send request
     * @param $type
     * @param $url
     * @param array $query
     * @return mixed
     * @throws \Exception
     */
    private function sendRequest($type, $url, $query = [])
    {

        $cred = $this->getCredentials();
        $requestTimestamp = gmDate("D, d M Y H:i:s") . 'GMT';
        $requestSignature = base64_encode(hash_hmac('sha256', $requestTimestamp, $cred['api_secret']));

        $client = $this->getGuzzleClient();
        $request = new Request($type, self::ENDPOINTTYPE . $url);


        //add basic authorization for client
        $response = $client->send($request, [
            'timeout' => 2,
            'auth' => [$cred['api_key'], $requestSignature],
            'headers' => [
                'X-Request-Timestamp' => $requestTimestamp
            ],
            'query' => $query
        ]);


        $code = $response->getStatusCode();
        if ($this->responseHandler($code)) {
            $response = $response->getBody()->getContents();
            return $response;
        }
    }

    /**
     * Get guzzle client.
     * @desc gets the guzzle version 6 client
     * @return object
     */
    private function getGuzzleClient()
    {
        $client = new Client(['base_uri' => self::HOST]);

        return $client;
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

    /**
     * Get member by email.
     * @param $client
     * @param $email
     * @return null
     */
    private function getMemberByEmail($client, $email)
    {

        $members = $this->getMembersFromList($client);
        foreach ($members as $member) {
            if ($member['email'] === $email) {
                return $member;
            }
        }
        return null;
    }
}