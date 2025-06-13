<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(Request $request, AuthenticationUtils $authenticationUtils, ?string $lastUsername): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        $categories = [
        (object)[ 'id' => 1, 'name' => 'Électronique' ],
        (object)[ 'id' => 2, 'name' => 'Livres' ],
        (object)[ 'id' => 3, 'name' => 'Vêtements' ],
        ];

        $products = [
        (object)[ 'name' => 'Smartphone Samsung', 'category' => $categories[0] ],
        (object)[ 'name' => 'Casque Bluetooth', 'category' => $categories[0] ],
        (object)[ 'name' => 'Le Seigneur des Anneaux', 'category' => $categories[1] ],
        (object)[ 'name' => 'Guide Symfony', 'category' => $categories[1] ],
        (object)[ 'name' => 'T-shirt noir', 'category' => $categories[2] ],
        (object)[ 'name' => 'Veste en jean', 'category' => $categories[2] ],
        ];    

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
