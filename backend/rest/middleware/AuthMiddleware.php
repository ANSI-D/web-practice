<?php

class AuthMiddleware {
    
    public static function authenticate() {
        // Simple check - just require any API key
        $headers = getallheaders();
        $apiKey = isset($headers['X-API-Key']) ? $headers['X-API-Key'] : null;
        
        if (!$apiKey) {
            http_response_code(401);
            echo "Authentication required";
            exit;
        }
    }
}

?>
