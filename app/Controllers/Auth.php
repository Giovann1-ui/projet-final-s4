<?php

namespace App\Controllers;

use App\Models\DepartementModel;
use App\Models\EmployeModel;

class Auth extends BaseController
{
    public function index()
    {
        if ($this->employe) {
            return redirect()->to($this->redirectByRole());
        }

        return view('auth/login', $this->viewData());
    }

    public function login()
    {
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $model   = new EmployeModel();
        $employe = $model->where('email', $email)->first();

        if (!$employe || !password_verify($password, $employe['password'])) {
            return redirect()->back()->with('error', 'Email ou mot de passe incorrect.');
        }

        if (!$employe['actif']) {
            return redirect()->back()->with('error', 'Votre compte est désactivé. Contactez l\'administrateur.');
        }

        $this->session->set('employe', [
            'id'             => (int) $employe['id'],
            'nom'            => $employe['nom'],
            'prenom'         => $employe['prenom'],
            'email'          => $employe['email'],
            'role'           => $employe['role'],
            'departement_id' => (int) $employe['departement_id'],
        ]);

        $this->session->setFlashdata('success', 'Bienvenue, ' . $employe['prenom'] . ' !');

        return redirect()->to($this->redirectByRole());
    }

    public function register()
    {
        if ($this->employe) {
            return redirect()->to($this->redirectByRole());
        }

        $departementModel = new DepartementModel();

        return view('auth/register', $this->viewData([
            'departements' => $departementModel->findAll(),
            'validation'   => null,
        ]));
    }

    public function store()
    {
        if ($this->employe) {
            return redirect()->to($this->redirectByRole());
        }

        $rules = [
            'nom'             => 'required|max_length[100]',
            'prenom'          => 'required|max_length[100]',
            'email'           => 'required|valid_email|is_unique[employes.email]',
            'password'        => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
            'departement_id'  => 'required|integer|is_not_unique[departements.id]',
        ];

        if (!$this->validate($rules)) {
            $departementModel = new DepartementModel();

            return view('auth/register', $this->viewData([
                'departements' => $departementModel->findAll(),
                'validation'   => $this->validator,
            ]));
        }

        $model = new EmployeModel();
        $model->insert([
            'nom'             => $this->request->getPost('nom'),
            'prenom'          => $this->request->getPost('prenom'),
            'email'           => $this->request->getPost('email'),
            'password'        => $this->request->getPost('password'),
            'role'            => 'employe',
            'departement_id'  => (int) $this->request->getPost('departement_id'),
            'date_embauche'   => date('Y-m-d'),
            'actif'           => 1,
        ]);

        return redirect()->to('login')->with('success', 'Compte créé avec succès. Vous pouvez maintenant vous connecter.');
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('login');
    }

    private function redirectByRole(): string
    {
        return match ($this->session->get('employe')['role']) {
            'admin'   => 'admin',
            'rh'      => 'rh',
            default   => 'employe',
        };
    }
}
