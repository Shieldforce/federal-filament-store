<?php

namespace Shieldforce\FederalFilamentStore\Services;

class BuscarViaCepService {

    public static function getData(string $zipcode)
    {
        $zipcode = str_replace([" ",".","-"], ["","",""], $zipcode);
        $url = "https://viacep.com.br/ws/{$zipcode}/json/";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }
}
