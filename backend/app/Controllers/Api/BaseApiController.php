<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class BaseApiController extends BaseController
{
    /**
     * Return a success JSON response.
     */
    protected function jsonSuccess($data = null, int $code = 200): ResponseInterface
    {
        return $this->response
            ->setStatusCode($code)
            ->setJSON([
                'status' => 'success',
                'data'   => $data,
            ]);
    }

    /**
     * Return an error JSON response.
     */
    protected function jsonError(string $message, int $code = 400): ResponseInterface
    {
        return $this->response
            ->setStatusCode($code)
            ->setJSON([
                'status'  => 'error',
                'message' => $message,
            ]);
    }

    /**
     * Get the current authenticated user data from the X-User header.
     */
    protected function currentUser(): ?object
    {
        $userHeader = $this->request->getHeaderLine('X-User');

        if (empty($userHeader)) {
            return null;
        }

        return json_decode($userHeader);
    }

    /**
     * Get the current authenticated user's ID.
     */
    protected function currentUserId(): ?int
    {
        $user = $this->currentUser();

        return $user ? (int) $user->id : null;
    }

    /**
     * Get the current authenticated user's role.
     */
    protected function currentUserRole(): ?string
    {
        $user = $this->currentUser();

        return $user ? $user->role : null;
    }
}
