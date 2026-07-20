<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeModel extends Model
{
    protected $table = 'employes';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nom', 'prenom', 'email', 'password',
        'role', 'departement_id', 'date_embauche', 'actif',
    ];
    protected $useTimestamps = false;
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected $validationRules = [
        'nom'            => 'required|max_length[100]',
        'prenom'         => 'required|max_length[100]',
        'email'          => 'required|valid_email|max_length[255]|is_unique[employes.email,id,{id}]',
        'password'       => 'required|min_length[6]',
        'role'           => 'required|in_list[employe,rh,admin]',
        'departement_id' => 'required|integer|is_not_unique[departements.id]',
        'date_embauche'  => 'required|valid_date',
        'actif'          => 'permit_empty|in_list[0,1]',
    ];

    protected $validationMessages = [
        'nom' => [
            'required'   => 'Le nom est requis.',
            'max_length' => 'Le nom ne peut dépasser 100 caractères.',
        ],
        'prenom' => [
            'required'   => 'Le prénom est requis.',
            'max_length' => 'Le prénom ne peut dépasser 100 caractères.',
        ],
        'email' => [
            'required'    => 'L\'email est requis.',
            'valid_email' => 'Veuillez fournir un email valide.',
            'is_unique'   => 'Cet email est déjà utilisé.',
        ],
        'password' => [
            'required'   => 'Le mot de passe est requis.',
            'min_length' => 'Le mot de passe doit faire au moins 6 caractères.',
        ],
        'role' => [
            'required' => 'Le rôle est requis.',
            'in_list'  => 'Rôle invalide (employe, rh, admin).',
        ],
        'departement_id' => [
            'required'      => 'Le département est requis.',
            'is_not_unique' => 'Le département sélectionné n\'existe pas.',
        ],
        'date_embauche' => [
            'required'   => 'La date d\'embauche est requise.',
            'valid_date' => 'Date invalide.',
        ],
    ];

    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }
}
