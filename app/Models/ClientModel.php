<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table         = 'client';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['num_tel', 'date_inscription'];
    protected $useTimestamps = false;

    protected $validationRules = [
        'num_tel' => 'required|max_length[15]|is_unique[client.num_tel,id,{id}]',
    ];

    protected $validationMessages = [
        'num_tel' => [
            'required'   => 'Le numéro de téléphone est requis.',
            'is_unique'  => 'Ce numéro est déjà inscrit.',
        ],
    ];

    public function findByNumTel(string $numTel): ?array
    {
        return $this->where('num_tel', $numTel)->first();
    }

    public function getOrCreate(string $numTel): array
    {
        $client = $this->findByNumTel($numTel);

        if ($client !== null) {
            return $client;
        }

        $id = $this->insert([
            'num_tel'          => $numTel,
            'date_inscription' => date('Y-m-d H:i:s'),
        ]);

        return $this->find($id);
    }
}
