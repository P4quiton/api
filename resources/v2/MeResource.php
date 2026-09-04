<?php

require_once __DIR__ . '/../../middleware/AuthMiddleware.php';

class MeResource
{
    public function show()
    {
        $authUser = AuthMiddleware::validate();

        header("Content-Type: application/json");

        http_response_code(200);

        echo json_encode([
            "id" => $authUser["id"],
            "username" => $authUser["username"],
            "email" => $authUser["email"]
        ]);
    }
}