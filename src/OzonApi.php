<?php

namespace Tdkomplekt\OzonApi;

class OzonApi
{
    public function hello($name): string
    {
        return "Hello $name!";
    }

    public function loadCategories()
    {
        $url = "https://api-seller.ozon.ru/v1/categories/tree";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "X-Custom-Header: value",
            "Content-Type: application/json",
            "Client-Id: " . config('ozon-api.client_id'),
            "Api-Key: ". config('ozon-api.api_key'),
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        //for debug only!
//    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
//    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);


        curl_close($curl);

        return \Illuminate\Support\Facades\Response::json(json_decode($resp));
    }
}
