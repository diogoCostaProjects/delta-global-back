<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JwtFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getHeaderLine('Authorization');

        if (!$header) {
            return Services::response()
                ->setJSON(['success'=> false, 'message' => 'Token JWT em branco ou inválido'])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        $token = null;
        if (strpos($header, 'Bearer ') === 0) {
            $token = substr($header, 7);
        }

        if (!$token) {
            return Services::response()
                ->setJSON(['success'=> false, 'message' => 'Token JWT em branco ou inválido'])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        try {
            $key = getenv('JWT_SECRET');
            $decoded = JWT::decode($token, new Key($key, 'HS256'));

            // You can set the user ID to the request object if needed
            $request->user = $decoded->uid;
        } catch (Exception $e) {
            return Services::response()
                ->setJSON(['success'=> false, 'message' => 'Token inválido: ' . $e->getMessage()])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
