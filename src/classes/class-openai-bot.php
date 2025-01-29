<?php

class OpenAIBot
{
    private $apiKey;
    private $assistantId;
    private $threadId;  //este mejor se pasa al front
    private $apiUrl = 'https://api.openai.com/v1';

    public function __construct($apiKey, $assistantId, $threadId)
    {
        $this->apiKey = $apiKey;
        $this->assistantId = $assistantId;
    }

    public function sendMessage($prompt)
    {
        $url = $this->apiUrl . "/threads/{$this->threadId}/messages";

        $data = ['role' => 'user', 'content' => $prompt];

        return $this->makeRequest($url, $data);
    }

    public function retrieveMessage($messageId)
    {
        $url = $this->apiUrl . "/threads/{$this->threadId}/messages/{$messageId}";

        return $this->makeRequest($url, null);

    }

    public function listMessages($params)
    {
        $url = $this->apiUrl . "/threads/{$this->threadId}/messages";

        return $this->makeRequest($url, $params);
        /**
         * limit, order, after, before, run_id
         */
    }

    public function createRun()
    {
        $url = $this->apiUrl . "/threads/{$this->threadId}/runs";

        return $this->makeRequest($url, null);
    }

    public function retrieveRun($runId)
    {
        $url = $this->apiUrl . "/threads/{$this->threadId}/runs/{$runId}";

        return $this->makeRequest($url, null);
    }

    public function threadAndRun($prompt)
    {
        $url = $this->apiUrl . '/threads/runs';

        $data = [
            'assistant_id' => $this->assistantId,
            'thread' => [
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ]
            ]
        ];

        return $this->makeRequest($url, $data);

    }

    private function makeRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        if ($data)
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey,
            'OpenAI-Beta: assistants=v2'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public function sendMessageGeneral($prompt)
    {
        if($this->threadId) {
            $this->sendMessage($prompt);
            $resp = $this->createRun();
            $status = $resp['status'];
            $runId = $resp['id'];
        } else {
            $resp = $this->threadAndRun($prompt);
            $this->threadId = $resp['thread_id'];
            $status = $resp['status'];
            $runId = $resp['id'];
        }

        while( $status == "queued" || $status == "in-progress"){
            $run = $this->retrieveRun($runId);

            $runId = $run['id'];
            sleep(0.2);
        }

        /* while ($status != "completed") {

            if($status == "queued") sleep(0.3);
            elseif($status == "in-progress"){
                
            }
        } */

        if($status == 'completed'){
            $assistant_message = $this->listMessages($this->threadId, [
                'order' => 'desc',
                'limit' => 1
            ]);

            if (!empty($assistant_message->data)) {

                $message = $assistant_message->data[0]->content[0]->text->value ?? '';
                $message = preg_replace("/【\d+:\d+†source】/", "", $message);

                $response = [
                    'msg' => $message,
                    'thread' => $this->threadId
                ];

                return $response;

            } else {
                
                return 'No assistant message found.';
            }

        }
    }
}
