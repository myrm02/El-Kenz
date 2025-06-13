<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\ORM\EntityManagerInterface;

class SecurityController extends AbstractController
{

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, EntityManagerInterface $entityManager): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // if(explode("@", $lastUsername)[1] === 'admin.com') {
        //     // Redirect admin users to the admin dashboard
        //     return $this->redirectToRoute('admin_home');
        // } else {
        //     return $this->render('security/login.html.twig', [
        //     'last_username' => $lastUsername,
        //     'error' => $error,
        //     ]);
        // }

        $users = $entityManager->getRepository(User::class)->findAll();

        $expectedRole = 'ROLE_ADMIN';

        for ($x = 0; $x <= count($users); $x++) {

            foreach ($users as $user) {
                if ($user->getEmail() === $lastUsername) {

                    if (in_array($expectedRole, $user->getRoles(), true)) {

                        print_r("Found user with expected role: " . $user->getEmail() . "\n");
                        return $this->render('admin/dashboard.html.twig', [
                            'last_username' => $lastUsername,
                            'error' => $error,
                        ]);

                    } else {

                        print_r("It is a lambda user: " . $user->getEmail() . "\n");
                        return $this->render('security/login.html.twig', [
                            'last_username' => $lastUsername,
                            'error' => $error,
                        ]);
                    }

                }
            }

        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/admin/home', name: 'admin_home')]
    public function adminHome(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/dashboard.html.twig');
    }
}
