<?php

namespace App\Controller;

use App\Services\Cache\CacheInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @property CacheInterface $redis
 */
class IndexController extends AbstractController
{
    public function __construct(CacheInterface $cache)
    {
        $this->redis = $cache;
    }

    #[Route('/index', name: 'app_index')]
    public function index(): Response
    {
        $asd = $this->redis->set('nenad', 'peder');

        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }


    #[Route('/get', name: 'app_get')]
    public function get(SessionInterface $session): Response
    {
        dd($this->redis->get('nenad'));

        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    #[Route('/show', name: 'app_show')]
    public function show()
    {
        return $this->render('index/show.html.twig', [
            'controller_name' => 'IndexController',
            'links' => [
                'store' => $this->generateUrl('store')
            ]
        ]);
    }

    #[Route('/store', name: 'app_store')]
    public function store()
    {

    }
}
