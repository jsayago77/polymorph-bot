<?php

class BotpressBot {
    private $apiUrl = 'https://chat.botpress.cloud/';
    private $xSecureKey;        //este se va para el front
    private $conversationID;    // este tambien
    private $apiKey;

    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }

    public function createUser()
    {
        $url = $this->apiUrl . $this->apiKey . '/users';
        return $this->makeRequest($url, null, null);
    }

    public function createConversation($xUserKey)
    {
        $url = $this->apiUrl . $this->apiKey . '/conversations';
        return $this->makeRequest($url, $xUserKey, null);
    }

    public function createMessage($data)
    {
        $url = $this->apiUrl . $this->apiKey . '/messages';

        $payload = array(
            'payload' => array(
                'type' => 'text',
                'text' => $data['message']
            ),
            'conversationId' => $data['cID']
        );

        return $this->makeRequest($url, $data['userKey'], $payload);
    }

    public function listMessages($xUserKey, $cID)
    {
        $url = $this->apiUrl . $this->apiKey . '/conversations/'.$cID.'/messages';

        return $this->makeRequest($url, $xUserKey, null);
    }

    private function makeRequest($url, $userKey, $data)
    {
        if($userKey)
            $xUserKey = "x-user-key: {$userKey}";
        else $xUserKey = '';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        if ($data)
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            $xUserKey
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

}
?>