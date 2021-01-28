<?php

namespace App;

use GuzzleHttp\Client;

class HttpRequest {

	public function __construct()
	{
		$this->client = new Client();
	}

    /**
     * @param string $url
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
	public function getRequest($url, $params)
	{
        $separator = $paramString= '';
        foreach($params as $key => $value) {
            $paramString .= $separator . $key . '=' . $value;
            $separator = '&';
        }

	    $url = $url . '?' . $paramString;

        try {
            $response = $this->client->request('GET', $url);
            return ['response' => \GuzzleHttp\json_decode($response->getBody()->getContents()), 'status' => $response->getStatusCode()];
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            return ['response' =>  $response->getBody()->getContents(), 'status'=> $response->getStatusCode()];
        }

	}
}