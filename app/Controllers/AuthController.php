<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use Exception;

class AuthController extends ResourceController
{
    public function register()
    {
        $rules = [
            'username' => 'required|min_length[4]|max_length[100]',
            'password' => 'required|min_length[4]|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $userModel = new UserModel();
        $data = [
            'username' => $this->request->getVar('username'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT)
        ];

        $userModel->save($data);

        return $this->respondCreated(['message' => 'User created successfully']);
    }

    public function login()
    {
        $rules = [
            'username' => 'required|min_length[4]|max_length[100]',
            'password' => 'required|min_length[4]|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $userModel = new UserModel();
        $user = $userModel->where('username', $this->request->getVar('username'))->first();

        if (!$user || !password_verify($this->request->getVar('password'), $user['password'])) {
            return $this->fail('Invalid credentials', ResponseInterface::HTTP_UNAUTHORIZED);
        }

        $key = getenv('JWT_SECRET');
        $payload = [
            'iat' => time(),
            'exp' => time() + 3600, // Token válido por 1 hora
            'uid' => $user['id']
        ];

        $token = JWT::encode($payload, $key, 'HS256');

        return $this->respond(['token' => $token]);
    }
}
