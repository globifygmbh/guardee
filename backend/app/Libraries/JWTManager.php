<?php

namespace App\Libraries;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Exception;

class JWTManager
{
    private string $secret;
    private int $expiry;

    public function __construct()
    {
        $this->secret = env('JWT_SECRET', 'default-secret');
        $this->expiry = 60 * 60 * 24; // 24 hours
    }

    /**
     * Encode a JWT token with user data.
     */
    public function encode(array $user): string
    {
        $issuedAt = time();
        $payload = [
            'iss' => base_url(),
            'iat' => $issuedAt,
            'exp' => $issuedAt + $this->expiry,
            'sub' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role'] ?? null,
        ];

        return JWT::encode($payload, $this->secret, 'HS256');
    }

    /**
     * Decode a JWT token and return the payload.
     */
    public function decode(string $token): ?object
    {
        try {
            return JWT::decode($token, new Key($this->secret, 'HS256'));
        } catch (ExpiredException $e) {
            return null;
        } catch (Exception $e) {
            return null;
        }
    }
}
