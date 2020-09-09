<?php

namespace App\Traits;

use GuzzleHttp\Client;

trait ConsumeExternalService {

    public function perfomRequest($method, $requestUrl, $formParams = [], $headers = []) {

        $client = new Client([
            'base_uri' => $this->baseUri
        ]);
        
        if(isset($this->secret)) {
            $headers["Authorization"] = $this->secret;
        }
        //dd($headers["Authorization"]);
        $response = $client->request($method, $requestUrl, ['form_params' => $formParams,
                        'headers' => $headers ]);

        return $response->getBody()->getContents();
        
    }
}