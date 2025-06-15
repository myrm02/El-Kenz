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
        ]);
    }
}
