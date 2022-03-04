<?php

namespace App\Controller;

use App\Form\SimpleForm;
use App\Services\Cache\CacheInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
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
    public function show(Request $request)
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
    public function store(Request $request)
    {
        if ($request->isMethod('POST')) {
            $form = $this->createForm(SimpleForm::class, [], [
                'action' => $this->generateUrl("store"),
                'method' => 'POST',
            ]);

            $form->handleRequest($request);

            $formData = $form->getData();

            if ($form->isSubmitted() && $form->isValid()) {
                if ($this->redis->pop('emailList')[$formData['email']]) {
                    // handle if this email already has submitted
                    return new Response(
                        'Your request has already been submitted at ' . $this->redis->pop('emailListDates')[$formData['email'] . '_date'],
                        400, array('Content-Type' => 'text/html')
                    );
                }

                // handle if it is new email
                $this->redis->push('emailList', [$formData['email'] => $formData['email']]);
                $this->redis->push('emailListDates', [$formData['email'] . '_date' => (new \DateTime())->format('Y-m-d H:i')]);

                return new Response(
                    'Your request has been added at ' . (new \DateTime())->format('Y-m-d H:i'),
                    200,
                    array('Content-Type' => 'text/html')
                );
            }
        }

        throw new HttpException(405, 'Method not allowed');
    }
}
