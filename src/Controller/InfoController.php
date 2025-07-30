<?php

namespace Sebihojda\Mbp\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class InfoController extends AbstractController
{
    /*#[Route('/api/info', name: 'api_info', methods: ['GET'])]
    public function info(Request $request): JsonResponse
    {
        // Preluam date din obiectul Request
        $clientIp = $request->getClientIp();
        $userAgent = $request->headers->get('User-Agent');

        // Preluam date din variabilele de mediu (din fisierul .env)
        $appEnv = $this->getParameter('kernel.environment'); // Metoda Symfony
        $appName = $_ENV['APP_NAME'] ?? 'N/A'; // Metoda PHP nativa

        // Construim array-ul de date
        $data = [
            'request_info' => [
                'ip_address' => $clientIp,
                'user_agent' => $userAgent,
            ],
            'environment_info' => [
                'symfony_environment' => $appEnv,
                'application_name' => $appName,
            ],
        ];

        // Returnam un obiect JsonResponse
        return new JsonResponse($data);
    }*/

    #[Route('/api/info', name: 'api_info', methods: ['GET'])]
    public function info(Request $request): JsonResponse
    {
        // Preluam date din obiectul Request
        $clientIp = $request->getClientIp();

        // Preluam TOATE headerele
        $allHeaders = $request->headers->all();

        // Preluam date din variabilele de mediu
        $appEnv = $this->getParameter('kernel.environment');
        $appName = $_ENV['APP_NAME'] ?? 'N/A';

        // Construim array-ul de date
        $data = [
            'request_info' => [
                'ip_address' => $clientIp,
                'headers' => $allHeaders, // Adaugam toate headerele in raspuns
            ],
            'environment_info' => [
                'symfony_environment' => $appEnv,
                'application_name' => $appName,
            ],
        ];

        return new JsonResponse($data);
    }

    #[Route('/wait-and-redirect/{seconds}', name: 'wait_and_redirect', requirements: ['seconds' => '\d+'])]
    public function waitAndRedirect(int $seconds): RedirectResponse
    {
        // Asteapta numarul de secunde specificat
        sleep($seconds);

        // Redirecteaza catre un URL extern
        return new RedirectResponse('https://www.google.com');
    }
}
