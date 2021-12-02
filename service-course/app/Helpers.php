<?php
use Illuminate\Support\Facades\Http;

function getUser($user_id) {
    $url = env('SERVICE_USER_URL').'users/'.$user_id;

    try {
        $response = Http::timeout(10)->get($url);
        $data = $response->json();
        $data['http_code'] = $response->getStatusCode();

        return $data;
    } catch (\Throwable $th) {
        return [
            'status' => 'error',
            'http_code' => 500,
            'message' => 'service user unavailable'
        ];
    }
}

function getUserByIds($user_ids = []) {
    $url = env('SERVICE_USER_URL').'users/';

    try {
        if (count($user_ids) === 0) {
            return [
                'status' => 'success',
                'http_code' => 200,
                'message' => []
            ];
        }   
        
        $response = Http::timeout(10)->get($url, [
            'user_ids' => $user_ids
        ]);

        $data = $response->json();
        $data['http_code'] = $response->getStatusCode();

        return $data;
    } catch (\Throwable $th) {
        return [
            'status' => 'error',
            'http_code' => 500,
            'message' => 'service user unavailable'
        ];
    }
}

function postOrder($params) {
    $url = env('SERVICE_ORDER_PAYMENT_URL').'api/order';

    try {
        $response = Http::post($url, $params);
        $data = $response->json();        
        $data['http_code'] = $response->getStatusCode();

        return $data;
    } catch (\Throwable $th) {
        return [
            'status' => 'error',
            'http_code' => 500,
            'message' => 'service order payment unavailable'
        ];
    }
}