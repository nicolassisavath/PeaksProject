<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

class CoreTestController extends TestCase
{
	/**
     * Description : make request to symfony api.
     */
    protected function curlRequest($method, $route, $postfields = null)
    {
    	if (!is_null($postfields))
    		$postfields = json_encode($postfields);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://localhost:8000/api/".$route,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $postfields,
			CURLOPT_HTTPHEADER => array(
				"Content-Type: application/json",
				"cache-control: no-cache"
			),
        ));

        $header =  array(
            "Cache-Control: no-cache"
        );
        if(in_array($method, ['PUT', 'POST']))
        {
            $header[] = "Accept: application/json";
            $header[] = "Content-Type: application/json";
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        $response['decoded'] = json_decode(curl_exec($curl));
        $response['http_code'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        return $response;
    }
}