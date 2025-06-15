<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(
        AuthenticationUtils $authenticationUtils,
        ?string $lastUsername,
        EntityManagerInterface $entityManager
    ): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        /** @var User $user */
        $user = $this->getUser();
        $id = $user->getId();
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Access denied: You do not have permission to access this page.');
        }
        
        $categories = []; 
        
        $products = $entityManager->getRepository(User::class)->find($id)->getProducts();
        if (!$products) {
            $products = []; // Aucun produit trouvé
        }

        $users = $entityManager->getRepository(User::class)->findAll();
        if (!$users) {
            $users = []; // Aucun utilisateur trouvé
        }

        $selectedCategoryId = 0; // Toutes les catégories
        if ($selectedCategoryId > 0) {
            $products = array_filter($products, function ($product) use ($selectedCategoryId) {
                return $product->getCategory()->getId() === $selectedCategoryId;
            });
        } else {
            $selectedCategoryId = 0; // Toutes les catégories
            $products = $products; // Pas de filtre
        }
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'products' => $products,
            'categories' => $categories,
            'error' => $error,
            'last_username' => $lastUsername,
            'selectedCategoryId' => $selectedCategoryId,
            'selectedCategoryName' => $selectedCategoryId > 0 ? $categories[$selectedCategoryId - 1]->getName() : 'Toutes les catégories',
            'users' => $users
        ]);
    }

    #[Route('/desactivate/user/{id}', name: 'app_desactivate_user')]
    public function desactivate(
        AuthenticationUtils $authenticationUtils,
        ?string $lastUsername,
        EntityManagerInterface $entityManager,
        int $id
    ): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        /** @var User $user */
        $user = $this->getUser();
        $id = $user->getId();
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Access denied: You do not have permission to access this page.');
        }
        
        $categories = []; 
        
        $products = $entityManager->getRepository(User::class)->find($id)->getProducts();
        if (!$products) {
            $products = []; // Aucun produit trouvé
        }

        $users = $entityManager->getRepository(User::class)->findAll();
        if (!$users) {
            $users = []; // Aucun utilisateur trouvé
        }

        $selectedCategoryId = 0; // Toutes les catégories
        if ($selectedCategoryId > 0) {
            $products = array_filter($products, function ($product) use ($selectedCategoryId) {
                return $product->getCategory()->getId() === $selectedCategoryId;
            });
        } else {
            $selectedCategoryId = 0; // Toutes les catégories
            $products = $products; // Pas de filtre
        }

        $standardUser = $entityManager->getRepository(User::class)->find($id);
        if (!$standardUser) {
            throw $this->createNotFoundException('User not found');
        }
        $standardUser->setIsActive(false);
        $entityManager->persist($standardUser);
        $entityManager->flush();

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'products' => $products,
            'categories' => $categories,
            'error' => $error,
            'last_username' => $lastUsername,
            'selectedCategoryId' => $selectedCategoryId,
            'selectedCategoryName' => $selectedCategoryId > 0 ? $categories[$selectedCategoryId - 1]->getName() : 'Toutes les catégories',
            'users' => $users
        ]);
    }

    #[Route('/activate/user/{id}', name: 'app_activate_user')]
    public function activate(
        AuthenticationUtils $authenticationUtils,
        ?string $lastUsername,
        EntityManagerInterface $entityManager,
        int $id
    ): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        /** @var User $user */
        $user = $this->getUser();
        $id = $user->getId();
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Access denied: You do not have permission to access this page.');
        }
        
        $categories = []; 
        
        $products = $entityManager->getRepository(User::class)->find($id)->getProducts();
        if (!$products) {
            $products = []; // Aucun produit trouvé
        }

        $users = $entityManager->getRepository(User::class)->findAll();
        if (!$users) {
            $users = []; // Aucun utilisateur trouvé
        }

        $selectedCategoryId = 0; // Toutes les catégories
        if ($selectedCategoryId > 0) {
            $products = array_filter($products, function ($product) use ($selectedCategoryId) {
                return $product->getCategory()->getId() === $selectedCategoryId;
            });
        } else {
            $selectedCategoryId = 0; // Toutes les catégories
            $products = $products; // Pas de filtre
        }

        $standardUser = $entityManager->getRepository(User::class)->find($id);
        if (!$standardUser) {
            throw $this->createNotFoundException('User not found');
        }
        $standardUser->setIsActive(true);
        $entityManager->persist($standardUser);
        $entityManager->flush();

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'products' => $products,
            'categories' => $categories,
            'error' => $error,
            'last_username' => $lastUsername,
            'selectedCategoryId' => $selectedCategoryId,
            'selectedCategoryName' => $selectedCategoryId > 0 ? $categories[$selectedCategoryId - 1]->getName() : 'Toutes les catégories',
            'users' => $users
        ]);
    }

    #[Route('/status/user/{id}', name: 'app_admin_status')]
    public function giveAdminRole(
        AuthenticationUtils $authenticationUtils,
        ?string $lastUsername,
        EntityManagerInterface $entityManager,
        int $id
    ): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        /** @var User $user */
        $user = $this->getUser();
        $id = $user->getId();
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Access denied: You do not have permission to access this page.');
        }
        
        $categories = []; 
        
        $products = $entityManager->getRepository(User::class)->find($id)->getProducts();
        if (!$products) {
            $products = []; // Aucun produit trouvé
        }

        $users = $entityManager->getRepository(User::class)->findAll();
        if (!$users) {
            $users = []; // Aucun utilisateur trouvé
        }

        $selectedCategoryId = 0; // Toutes les catégories
        if ($selectedCategoryId > 0) {
            $products = array_filter($products, function ($product) use ($selectedCategoryId) {
                return $product->getCategory()->getId() === $selectedCategoryId;
            });
        } else {
            $selectedCategoryId = 0; // Toutes les catégories
            $products = $products; // Pas de filtre
        }

        $standardUser = $entityManager->getRepository(User::class)->find($id);
        if (!$standardUser) {
            throw $this->createNotFoundException('User not found');
        }
        $standardUser->setRoles($standardUser->getRoles() + ['ROLE_ADMIN']);
        $entityManager->persist($standardUser);
        $entityManager->flush();

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'products' => $products,
            'categories' => $categories,
            'error' => $error,
            'last_username' => $lastUsername,
            'selectedCategoryId' => $selectedCategoryId,
            'selectedCategoryName' => $selectedCategoryId > 0 ? $categories[$selectedCategoryId - 1]->getName() : 'Toutes les catégories',
            'users' => $users
        ]);
    }

    #[Route('/retro/user/{id}', name: 'app_retro_status')]
    public function retroAdminRole(
        AuthenticationUtils $authenticationUtils,
        ?string $lastUsername,
        EntityManagerInterface $entityManager,
        int $id
    ): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        /** @var User $user */
        $user = $this->getUser();
        $id = $user->getId();
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Access denied: You do not have permission to access this page.');
        }
        
        $categories = []; 
        
        $products = $entityManager->getRepository(User::class)->find($id)->getProducts();
        if (!$products) {
            $products = []; // Aucun produit trouvé
        }

        $users = $entityManager->getRepository(User::class)->findAll();
        if (!$users) {
            $users = []; // Aucun utilisateur trouvé
        }

        $selectedCategoryId = 0; // Toutes les catégories
        if ($selectedCategoryId > 0) {
            $products = array_filter($products, function ($product) use ($selectedCategoryId) {
                return $product->getCategory()->getId() === $selectedCategoryId;
            });
        } else {
            $selectedCategoryId = 0; // Toutes les catégories
            $products = $products; // Pas de filtre
        }

        $standardUser = $entityManager->getRepository(User::class)->find($id);
        if (!$standardUser) {
            throw $this->createNotFoundException('User not found');
        }
        $standardUser->setRoles(['ROLE_USER']);
        $entityManager->persist($standardUser);
        $entityManager->flush();

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'products' => $products,
            'categories' => $categories,
            'error' => $error,
            'last_username' => $lastUsername,
            'selectedCategoryId' => $selectedCategoryId,
            'selectedCategoryName' => $selectedCategoryId > 0 ? $categories[$selectedCategoryId - 1]->getName() : 'Toutes les catégories',
            'users' => $users
        ]);
    }

    #[Route('/add/points/{id}', name: 'app_add_points')]
    public function addPoints(
        AuthenticationUtils $authenticationUtils,
        ?string $lastUsername,
        EntityManagerInterface $entityManager,
        int $id
    ): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        /** @var User $user */
        $user = $this->getUser();
        $id = $user->getId();
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Access denied: You do not have permission to access this page.');
        }
        
        $categories = []; 
        
        $products = $entityManager->getRepository(User::class)->find($id)->getProducts();
        if (!$products) {
            $products = []; // Aucun produit trouvé
        }

        $users = $entityManager->getRepository(User::class)->findAll();
        if (!$users) {
            $users = []; // Aucun utilisateur trouvé
        }

        $selectedCategoryId = 0; // Toutes les catégories
        if ($selectedCategoryId > 0) {
            $products = array_filter($products, function ($product) use ($selectedCategoryId) {
                return $product->getCategory()->getId() === $selectedCategoryId;
            });
        } else {
            $selectedCategoryId = 0; // Toutes les catégories
            $products = $products; // Pas de filtre
        }

        $standardUser = $entityManager->getRepository(User::class)->find($id);
        if (!$standardUser) {
            throw $this->createNotFoundException('User not found');
        }
        $standardUser->setPoints($standardUser->getPoints() + 1000); // Ajoute 1000 points
        $entityManager->persist($standardUser);
        $entityManager->flush();

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'products' => $products,
            'categories' => $categories,
            'error' => $error,
            'last_username' => $lastUsername,
            'selectedCategoryId' => $selectedCategoryId,
            'selectedCategoryName' => $selectedCategoryId > 0 ? $categories[$selectedCategoryId - 1]->getName() : 'Toutes les catégories',
            'users' => $users
        ]);
    }
}
