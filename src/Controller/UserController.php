<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(Request $request, AuthenticationUtils $authenticationUtils, ?string $lastUsername, EntityManagerInterface $entityManager): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        $categories = [];

        $products = $entityManager->getRepository(Product::class)->findAll();
        if (!$products) {
            $products = []; // Aucun produit trouvé
        }

        $selectedCategoryId = $request->query->getInt('category');

    if ($selectedCategoryId > 0) {
        $products = array_filter($products, function ($product) use ($selectedCategoryId) {
            return $product->category->id === $selectedCategoryId;
        });
    } else {
        $selectedCategoryId = 0; // Toutes les catégories
        $products = $products; // Pas de filtre
    }

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'products' => $products,
            'categories' => $categories,
            'selectedCategoryId' => $selectedCategoryId,
            'selectedCategoryName' => $selectedCategoryId > 0 ? $categories[$selectedCategoryId - 1]->name : 'Toutes les catégories',
            'error' => $error,
            'last_username' => $lastUsername
        ]);
    }
}
