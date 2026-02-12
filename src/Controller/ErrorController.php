<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;

class ErrorController extends AbstractController
{

    private array $codigos_error = [
        'CUENTA_NOT_FOUND' => [
            'message' => 'El iban proporcionado no existe.'
        ],
        'UNKNOWN_ERROR' => [
            'message' => 'Ha habido un error inesperado.'
        ]
    ];
    
    public function main(Request $request): Response
    {
        if (!$this->getUser())         
            return $this->redirectToRoute('home',);

        $user = $this->getUser();

        $error_code = $request->attributes->get('code');

        $error_message = $this->codigos_error[$error_code]['message'] ?? $this->codigos_error['UNKNOWN_ERROR']['message'];

        return $this->render('panel/error.html.twig', [
            'title' => 'Página de error',
            'error_message' => $error_message,
            'user' => $user
        ]); 
    }

}
