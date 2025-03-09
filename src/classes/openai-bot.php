<?php

namespace Polymorph_Bot;

class OpenAI_Bot
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

        return $this->makeRequest($url, $params, 'GET');
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
		
        return $this->makeRequest($url, null, 'GET');
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

    private function makeRequest($url, $data, $method = 'POST')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		if($method == 'POST')
        	curl_setopt($ch, CURLOPT_POST, true);

        if ($method == 'POST' && !empty($data))
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
			
		if ($method == 'GET' && !empty($data)) {
			$queryString = http_build_query($data);
			$url .= '?' . $queryString;
		}
		
		curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey,
            'OpenAI-Beta: assistants=v2'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);
		
		$decoded = json_decode($response, true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			//error_log(print_r(array('error' => 'Invalid JSON', 'url' => $url, 'fields' => $data),true));
		}

        return $decoded;
    }

    public function sendMessageGeneral($prompt)
    {
		$runId = '';
		$status = '';
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

			$status = $run['status'];
            $runId = $run['id'];
            sleep(2);
        }

        /* while ($status != "completed") {

            if($status == "queued") sleep(0.3);
            elseif($status == "in-progress"){
                
            }
        } */

        if($status == 'completed'){
            $assistant_message = $this->listMessages([
                'order' => 'desc',
                'limit' => 1
            ]);

            if (!empty($assistant_message['data'])) {

                $message = $assistant_message['data'][0]['content'][0]['text']['value'] ?? '';
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
