<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;


class AlunoController extends ResourceController
{
    protected $modelName = 'App\Models\AlunoModel';
    protected $format    = 'json';
    public $upload = null;
    private $db = null;

    public function __construct()
    {
        $this->upload = \Config\Services::upload();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $data = $this->model->findAll();
        
        foreach ($data as $key => $d) 
        {
            if (!empty($d['foto'])){
                $data[$key]['foto'] = base_url('uploads/' . $d['foto']);
            }
        }
        
        return $this->respond($data);
    }

    public function show($id = null)
    {
        $data = $this->model->find($id);

        if ($data) {
            return $this->respond($data);
        }

        $message = [
            'success' => false,
            'message' => 'Não foi possível localizar o aluno'
        ];

        return $this->respond($message);
    }

    public function create()
    {
        $data = $this->request->getJSON();

        if ($this->model->insert($data)) {
            $message = [
                'success' => true,
                'message' => 'Aluno adicionado'
            ];

            return $this->respond($message);
        } else {
            $message = [
                'success' => false,
                'message' => 'Não foi possível inserir o aluno'
            ];

            return $this->respond($message);
        }
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON();

        if ($this->model->update($id, $data)) {
            $message = [
                'success' => true,
                'message' => 'Dados do aluno atualizados'
            ];

            return $this->respond($message);
        } else {
            $message = [
                'success' => false,
                'message' => 'Não foi possível atualizar os dados do aluno'
            ];

            return $this->respond($message);
        }

        return $this->failValidationErrors($this->model->errors());
    }

    public function delete($id = null)
    {
        if ($this->model->delete($id)) {
            $message = [
                'success' => true,
                'message' => 'Aluno removido'
            ];

            return $this->respond($message);
        } else {
            $message = [
                'success' => false,
                'message' => 'Não foi possível remover o aluno'
            ];

            return $this->respond($message);
        }
    }

    public function newImage()
    {
        $id = $this->request->getPost('id');


        $config = [
            'upload_path'   => './public/uploads/',
            'allowed_types' => 'gif|jpg|png',
            'max_size'      => 2048, // 2MB
            'max_width'     => 1024,
            'max_height'    => 768,
            'encrypt_name'  => TRUE, // Gerar um nome único
        ];

        $file = $this->request->getFile('image');

        if (!$file->isValid()) {
            $response = [
                'status' => 'error',
                'message' => $file->getErrorString()
            ];
            return $this->respond($response, 400);
        }

        // Gera um nome único para o arquivo
        $newName = $file->getRandomName();

        if ($file->move('../public/uploads/', $newName)) {
            $response = [
                'success' => true,
                'message' => 'Upload bem-sucedido',
                'data' => [
                    'file_name' => $newName,
                    'file_type' => $file->getClientMimeType(),
                    'file_path' => base_url('public/uploads/' . $newName),
                    'full_path' => './public/uploads/' . $newName,
                    'file_size' => $file->getSize(),
                ]
            ];

            $this->db->query("UPDATE alunos SET foto='{$newName}' WHERE id = '{$id}'");

            return $this->respond($response, 200);
        } else {
            $response = [
                'success' => false,
                'message' => 'Erro ao mover o arquivo para o diretório de destino.'
            ];
            return $this->respond($response, 500);
        }
    }
}
