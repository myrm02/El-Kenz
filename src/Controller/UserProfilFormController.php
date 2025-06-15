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
use GenerateNotification;

final class UserProfilFormController extends AbstractController
{
    // public function __construct(private GenerateNotification $generateNotification)
    // {
       
    // }

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

        $admins = $entityManager->getRepository(User::class)->findBy(['roles' => 'ROLE_ADMIN']);

        if ($form->isSubmitted() && $form->isValid()) {

            // User infos

            $user->setFirstname($form->get('firstname')->getData())
                ->setLastname($form->get('lastname')->getData());

            $entityManager->persist($user);
            $entityManager->flush();

        //     for ($x = 0; $x <= count($admins); $x++) {

        //     foreach ($admins as $admin) {
        //         $this->generateNotification->generate($admin, 'Profile updated: %s (firstname) and %s (lastname) at %s' .$user->getFirstname() .$user->getLastname().(new \DateTime())->format('Y-m-d H:i:s'));
        //     }
        // }
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
