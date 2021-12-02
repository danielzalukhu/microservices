<?php
use Illuminate\Support\Facades\Http;

function giveAccessPremiumClass($data) {
    $url = env('SERVICE_COURSE_URL').'api/my_course/premium';
    
    try {
        $response = Http::post($url, $data);
        $data = $response->json();
        return $data;
        $data['http_code'] = $response->getStatusCode();

        return $data;
    } catch (\Throwable $th) {
        return [
            'status' => 'error',
            'http_code' => 500,
            'message' => 'service course unavailable'
        ];
    }
}