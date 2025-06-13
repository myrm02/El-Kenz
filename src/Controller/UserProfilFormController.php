<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Form\UserProfilForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;

final class UserProfilFormController extends AbstractController
{
    #[Route('/profil', name: 'app_user_profil')]
    public function index(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        /** @var User $user */
        $user = $security->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in.');
        }

        // Now $user contains the logged-in user
        $firstname = $user->getFirstname();
        $lastname = $user->getLastname();

        $form = $this->createForm(UserProfilForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // User infos

            $user->setFirstname($form->get('firstname')->getData())
                ->setLastname($form->get('lastname')->getData());

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user');
        }

        // Optionally, pass these to the template
        return $this->render('user/profil.html.twig', [
            'controller_name' => 'UserProfilFormController',
            'userProfilForm' => $form,
            'firstname' => $firstname,
            'lastname' => $lastname,
        ]);
    }
}
