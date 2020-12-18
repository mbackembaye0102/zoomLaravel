<?php

namespace App\Traits;

use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;


trait ZoomJWT
{

    private function generateZoomToken()
    {
        $key = env('ZOOM_API_KEY', '');
        $secret = env('ZOOM_API_SECRET', '');
        $payload = [
            'iss' => $key,
            'exp' => strtotime('+1 minute'),
        ];
        return JWT::encode($payload, $secret, 'HS256');
    }

    private function retrieveZoomUrl()
    {
        return env('ZOOM_API_URL', '');
    }

    private function zoomRequest()
    {
        $jwt = $this->generateZoomToken();
        $client = new Client(['headers' => [ 'authorization' => 'Bearer ' . $jwt,
        'content-type' => 'application/json']]);
        return $client;
    }


    public function zoomGet(string $path, array $query = [])
{
    $url = $this->retrieveZoomUrl();
    $request = $this->zoomRequest();
    return $request->get($url . $path, $query);
}

public function zoomPost(string $path, array $body = [])
{
    $url = $this->retrieveZoomUrl();
    $request = $this->zoomRequest();
    return $request->post($url . $path, $body);
}

public function zoomPatch(string $path, array $body = [])
{
    $url = $this->retrieveZoomUrl();
    $request = $this->zoomRequest();
    return $request->patch($url . $path, $body);
}

public function zoomDelete(string $path, array $body = [])
{
    $url = $this->retrieveZoomUrl();
    $request = $this->zoomRequest();
    return $request->delete($url . $path, $body);
}


public function toZoomTimeFormat(string $dateTime)
{
    try {
        $date = new \DateTime($dateTime);
        return $date->format('Y-m-d\TH:i:s');
    } catch(\Exception $e) {
        Log::error('ZoomJWT->toZoomTimeFormat : ' . $e->getMessage());
        return '';
    }
}

public function toUnixTimeStamp(string $dateTime, string $timezone)
{
    try {
        $date = new \DateTime($dateTime, new \DateTimeZone($timezone));
        return $date->getTimestamp();
    } catch (\Exception $e) {
        Log::error('ZoomJWT->toUnixTimeStamp : ' . $e->getMessage());
        return '';
    }
}
}