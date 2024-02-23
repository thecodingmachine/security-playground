<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{

    public function __construct(private LoggerInterface $logger)
    {

    }

    #[Route('/posts/{method}/{id}', name: 'app_post_request')]
    public function request(string $method, ?int $id = null): Response
    {
        $httpClient = HttpClient::create();

        $url = 'https://jsonplaceholder.typicode.com/posts/' . ($id !== null ? $id : '');

        $response = $httpClient->request(strtoupper($method), $url);

        $this->logger->info("[METHOD $method] $url : Response " . $response->getStatusCode());

        $content = json_decode($response->getContent());

        $view = is_array($content) ? 'post/index.html.twig' : 'post/show.html.twig';

        return $this->render($view, ['data' => $content]);
    }
}
