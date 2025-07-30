<?php

namespace Sebihojda\Mbp\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ToolsController extends AbstractController
{
    #[Route('/', name: 'table_tools')]
    public function index(): Response
    {
        return $this->render('tools/index.html.twig', [
            'controller_name' => 'ToolsController',
        ]);
    }

    #[Route('/prepend-header', name: 'table_tools_prepend_header', methods: ['POST'])]
    public function prependHeader(): Response
    {
        // TODO

        return new Response('TO BE DONE');
    }

    #[Route('/merge', name: 'table_tools_merge', methods: ['POST'])]
    public function mergeTables(): Response
    {
        // TODO

        return new Response('TO BE DONE');
    }
}
