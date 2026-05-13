<?php
namespace App\Filters;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface {
    public function before(RequestInterface $request, $arguments = null) {
        $session = session();
        $user = $session->get('user');
        // $arguments contient le(s) rôle(s) autorisé(s)
        // ex: ['admin'] ou ['admin', 'bibliothecaire']
        if (!$user || !in_array($user['role'], $arguments ?? [])) {
            return redirect()->to('/livre')->with('error', 'Accès refusé :
            droits insuffisants');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {
        // Rien à faire après
    }
}