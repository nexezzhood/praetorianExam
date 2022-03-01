<?php

namespace App\Controller;

use App\Utils\Cache\MyRedis;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/index', name: 'app_index')]
    public function index(): Response
    {
      $redis = phpiredis_connect('127.0.0.1', 6379);
        $a = new MyRedis();
        dd($a);
//        $response = phpiredis_command_bs($redis, array('DEL', 'test'));
//
//        $response = phpiredis_multi_command_bs($redis, array(
//            array('SET', 'test', '1'),
//            array('GET', 'test'),
//        ));
      dd($response);
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
}
