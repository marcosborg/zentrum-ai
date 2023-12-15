<?php

namespace App\Http\Controllers\Traits;

trait OpenAi
{
    public function modifyAssistant($openaiApiKey, $assistant_id, $instructions)
    {
        $ch = curl_init('https://api.openai.com/v1/assistants/' . $assistant_id);

        $data = json_encode([
            "instructions" => $instructions,
        ]);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $openaiApiKey,
            'OpenAI-Beta: assistants=v1'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response);
    }

    public function createThreadAndRun($openaiApiKey, $assistant_id, $content)
    {

        $data = [
            'assistant_id' => $assistant_id,
            'thread' => [
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $content
                    ]
                ]
            ]
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.openai.com/v1/threads/runs',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'OpenAI-Beta: assistants=v1',
                'Authorization: Bearer ' . $openaiApiKey
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);

    }

    public function listMessages($openaiApiKey, $thread_id)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.openai.com/v1/threads/' . $thread_id . '/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'OpenAI-Beta: assistants=v1',
                'Authorization: Bearer ' . $openaiApiKey
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);

    }

    public function createMessage($openaiApiKey, $thread_id, $content)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.openai.com/v1/threads/' . $thread_id . '/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
            "role": "user",
            "content": "' . $content . '"
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'OpenAI-Beta: assistants=v1',
                'Authorization: Bearer ' . $openaiApiKey
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);

    }

    public function createRun($openaiApiKey, $assistant_id, $thread_id)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.openai.com/v1/threads/' . $thread_id . '/runs',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                "assistant_id": "' . $assistant_id . '"
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'OpenAI-Beta: assistants=v1',
                'Authorization: Bearer ' . $openaiApiKey,
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);

    }
}
