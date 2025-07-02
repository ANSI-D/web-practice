<?php
require_once __DIR__ . '/../services/AuthService.class.php';

Flight::set('authService', new AuthService());

Flight::group('/auth', function(){

    Flight::route('POST /login', function(){
        $payload = Flight::request()->data->getData();

        $user = Flight::get('authService')->getUserByEmail($payload['email']);
        if (!$user || !password_verify($payload['password'], $user['password'])) {
            Flight::halt(401, "Invalid email or password");
        }

        unset($user['password']); // Remove password from response

        // For now, just return user data without JWT
        Flight::json($user);
    });

    Flight::route('POST /logout', function(){
        // Simple logout - just return success message
        Flight::json([
            'message' => 'Logout successful'
        ]);
    });
});

?>