<?php

namespace Heyloyalty\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class HeyloyaltyServices {

    const HOST = 'https://api.heyloyalty.com';
    const ENDPOINTTYPE = '/loyalty/v1';

    public function createMember($params,$list_id)
    {
        $response = $this->sendRequest('POST','/lists/'.$list_id.'/members',$params);
        $response = $this->responseToArray($response);
        return $response;
    }
    public function getList($list_id)
    {
        $response = $this->sendRequest('GET','/lists/'.$list_id);
        $response = $this->responseToArray($response);
        return $response;
    }

    public function getLists()
    {
        $response = $this->sendRequest('GET','/lists/');
        $response = $this->responseToArray($response);
        return $response;
    }
    /**
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
     * @desc gets the guzzle version 6 client
     * @return object
     */
    private function getGuzzleClient()
    {
        $client = new Client(['base_uri' => self::HOST]);

        return $client;
    }

    /**
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
     * @param $response
     * @return array
     */
    private function responseToArray($response)
    {
        return json_decode($response, true);
    }

    /**
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