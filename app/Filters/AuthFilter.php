<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = service('session');
        $employe = $session->get('employe');

        if (!$employe) {
            return redirect()->to('login')
                ->with('error', 'Veuillez vous connecter pour accéder à cette page.');
        }

        if (!empty($arguments)) {
            $role = $arguments[0];
            if ($employe['role'] !== $role) {
                return redirect()->to('/')
                    ->with('error', 'Accès non autorisé. Vous n\'avez pas les droits nécessaires.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
    {
    }
}
