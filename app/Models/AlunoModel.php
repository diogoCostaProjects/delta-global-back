<?php

namespace App\Models;

use CodeIgniter\Model;

class AlunoModel extends Model
{
    protected $table      = 'alunos';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['nome', 'email', 'telefone', 'endereco', 'foto'];
}
