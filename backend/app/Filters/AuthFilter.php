<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\JWTManager;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (empty($authHeader) || !str_starts_with($authHeader, 'Bearer ')) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Access denied. No token provided.',
                ]);
        }

        $token = trim(substr($authHeader, 7));

        $jwt = new JWTManager();
        $decoded = $jwt->decode($token);

        if ($decoded === null) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid or expired token.',
                ]);
        }

        // Store user data as JSON in a custom header for controllers
        $userData = json_encode([
            'id'    => $decoded->sub,
            'email' => $decoded->email,
            'role'  => $decoded->role,
        ]);

        $request->setHeader('X-User', $userData);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after the request
    }
}
