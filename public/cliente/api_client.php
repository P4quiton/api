<?php

function apiRequest(
    string $endpoint,
    string $method = 'GET',
    ?array $data = null,
    ?string $token = null
): array {
    //Cambiar en server
    $baseUrl = 'http://localhost/api/public/api/v2';

    $url = $baseUrl . $endpoint;

    $ch = curl_init($url);

    $headers = [
        'Accept: application/json'
    ];

    if ($data !== null) {
        $headers[] = 'Content-Type: application/json';
    }

    if ($token !== null) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 20
    ]);

    if ($data !== null) {
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            json_encode($data)
        );
    }

    $body = curl_exec($ch);

    if ($body === false) {
        $error = curl_error($ch);
        curl_close($ch);

        return [
            'status' => 0,
            'data' => null,
            'error' => $error
        ];
    }

    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    return [
        'status' => $status,
        'data' => json_decode($body, true),
        'error' => null
    ];
}