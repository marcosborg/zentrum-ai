<?php

namespace App\Http\Controllers\Traits;

trait PrestashopApi
{

    public function zentrumSearch($website, $search)
    {
        $encodedSearch = urlencode($search);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $website . '/api/search?query=' . $encodedSearch . '&language=1&output_format=JSON&display=full',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic SlRVUjNDS0JXN0dWSzFVUUdKNzlSMk5VVU1QWUZNNkI6'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }

    public function categories()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://techniczentrum.com/api/categories?language=1&output_format=JSON&display=full',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic SlRVUjNDS0JXN0dWSzFVUUdKNzlSMk5VVU1QWUZNNkI6',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }
}
