<?php

namespace App\Controller;

use App\Form\SimpleForm;
use App\Services\Cache\CacheInterface;

use App\Services\LogsService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Request as RequestEntity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Router;

/**
 * @property CacheInterface $redis
 */
class IndexController extends AbstractController
{
    public function __construct(CacheInterface $cache)
    {
        $this->redis = $cache;
    }

    #[Route('/show', name: 'app_show')]
    public function show(Request $request,  LogsService $logsService, ManagerRegistry $doctrine)
    {
        $form = $this->createForm(SimpleForm::class, [], [
            'action' => $this->generateUrl("store"),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        return $this->render('index/show.html.twig', [
            'controller_name' => 'IndexController',
            'links' => [
                'store' => $this->generateUrl("store")
            ],
            'form' => $form->createView()
        ]);
    }

    #[Route('/store', name: 'app_store')]
    public function store(Request $request, LogsService $logsService, ManagerRegistry $doctrine)
    {
        if ($request->isMethod('POST')) {
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            $form = $this->createForm(SimpleForm::class, [], [
                'action' => $this->generateUrl("store"),
                'method' => 'POST',
            ]);

            $form->handleRequest($request);

            $formData = $form->getData();

            if ($form->isSubmitted() && $form->isValid()) {
                if ($this->redis->get('emailList') && isset($this->redis->get('emailList')[$formData['email']])) {
                    // handle if this email already has submitted
                    return new Response(
                        'Your request has already been submitted at ' . $this->redis->get('emailListDates')[$formData['email'] . '_date'],
                        400, array('Content-Type' => 'text/html')
                    );
                }

                // handle if it is new email
                $this->redis->set('emailList', [$formData['email'] => $formData['email']]);
                $this->redis->set('emailListDates', [$formData['email'] . '_date' => (new \DateTime())->format('Y-m-d H:i')]);

                $preRecords = $doctrine->getRepository(RequestEntity::class)->findOneBy(
                    ['ip_address' => $ip]
                );

                if ($preRecords) {
                    $logsService->log([
                        'method' => 'POST',
                        'operation_type' => 'update',
                        'entity' => [
                            'ip_address' => $ip,
                            'last_update' => new \DateTime(),
                            'cached_operations' => $preRecords ? $preRecords->getCachedOperations() + 1 : 1
                        ]
                    ]);
                } else {
                    $logsService->log([
                        'method' => 'POST',
                        'operation_type' => 'create',
                        'entity' => [
                            'ip_address' => $ip,
                            'last_update' => new \DateTime(),
                            'cached_operations' => 1
                        ]
                    ]);
                }

                return new Response(
                    'Your request has been added at ' . (new \DateTime())->format('Y-m-d H:i'),
                    200,
                    array('Content-Type' => 'text/html')
                );
            }
        }

        throw new HttpException(405, 'Method not allowed');
    }

    public function encryptPayload(Request $request)
    {
        if ($request->isMethod('POST')) {
            $data = json_decode($request->getContent(), true);

            $emailFromRedis = $this->redis->get('emailList');

            if (isset($data['email']) && in_array($data['email'], $emailFromRedis)) {
                $publicKeyFromRedis = $this->redis->get('publicKey_' . $data['email']);

                if ($publicKeyFromRedis) {
                    return new Response(
                        'encrypted payload',
                        200,
                        array('Content-Type' => 'text/html')
                    );
                }
            }

            return new Response(
                'no key was generated for this email',
                400,
                array('Content-Type' => 'text/html')
            );
        }

        throw new HttpException(405, 'Method not allowed');
    }
}
