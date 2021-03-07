<?php

namespace App\Controller;

use App\Repository\BookingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Repository\UserRepository;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("admin-calendar", name="admin_calendar") methods={"GET"})
     * @param BookingRepository $calendar
     * @return Response
     */
    public function booking(BookingRepository $calendar)
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

        return $this->render('admin/calendar.html.twig', compact('data'));
    }





    /**
     * @Route("/admin-user", name="admin_user")
     */
    public function user( UserRepository $repository,
                          Request $request, PaginatorInterface $paginator)
    {




        $allusers = $repository->findAll();

        $users = $paginator->paginate(
        // Doctrine Query, not results
            $allusers,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            7
        );



        return $this->render('admin/user.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/admin-{id}-roles", name="admin.roles")
     */
    public function roles($id, UserRepository $repository,Request $request, ObjectManager $manager)
    {




        $user = $repository->findOneBy(['id' => $id]);
        $user->getRoles();
        $user->setRoles(array('ROLE_ADMIN','ROLE_PRESTA'));
        $form = $this->createForm(UserformType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /*
                        $image = $form->get('image')->getData();
                        $imageName = md5(uniqid()).'.'.$image->guessExtension();
                        $user->setImage($imageName);
                        $image->move(
                            $this->getParameter('image_directory'), $imageName);
                        $user->setImage($imageName);*/

            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute("admin.user");

        }

        return $this->render('admin/roles.html.twig', [
            'UserformType' => $form->createView()
        ]);

    }

    /**
     * @Route("admin-reservation", name="admin_reservation", methods={"GET"})
     */
    public function reservation(BookingRepository $bookingRepository): Response
    {
        return $this->render('admin/reservation.html.twig', [
            'bookings' => $bookingRepository->findAll(),
        ]);
    }

}
