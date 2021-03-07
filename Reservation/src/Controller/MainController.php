<?php

namespace App\Controller;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Symfony\Component\HttpFoundation\Cookie;
use App\Repository\BookingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     * @param BookingRepository $calendar
     * @return Response
     */
    public function index(BookingRepository $calendar)
    {
        $events = $calendar->findAll();

        $rdvs = [];

        foreach($events as $event){
            $rdvs[] = [
                'id' => $event->getId(),
                'start' => $event->getBeginAt()->format('Y-m-d H:i:s'),
                'end' => $event->getEndAt()->format('Y-m-d H:i:s'),
                'title' => $event->getTitle(),
                'description' => $event->getDescription(),
                'backgroundColor' => $event->getBackgroundColor(),
                'borderColor' => $event->getBorderColor(),
                'textColor' => $event->getTextColor(),
                'allDay' => $event->getAllDay(),
            ];
        }

        $data = json_encode($rdvs);

        return $this->render('main/index.html.twig', compact('data'));
    }


    /**
     * @Route("/", name="token")
     */
    public function token()
    {
        $username = $this->getUser()->getUsername();
        $token = (new Builder())
            ->withClaim('mercure', ['subscribe' => [sprintf("/%s", $username)]])
            ->getToken(
                new Sha256(),
                new Key($this->getParameter('mercure_secret_key'))
            )
        ;

        $response =  $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);

        $response->headers->setCookie(
            new Cookie(
                'mercureAuthorization',
                $token,
                (new \DateTime())
                    ->add(new \DateInterval('PT2H')),
                '/.well-known/mercure',
                null,
                false,
                true,
                false,
                'strict'
            )
        );

        return $response;
    }
}
