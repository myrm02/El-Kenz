<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\SecurityBundle\Security;
use GenerateNotification;

final class UserController extends AbstractController
{
    // public function __construct(private GenerateNotification $generateNotification)
    // {
        
    // }

    #[Route('/user', name: 'app_user')]
    public function index(Request $request, AuthenticationUtils $authenticationUtils, ?string $lastUsername, User $user, EntityManagerInterface $entityManager): Response
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
            'last_username' => $lastUsername,
            'user' => $user
        ]);
    }

    #[Route('/product/{id}', name: 'app_product_detail')]
    public function detailProduct(int $id, EntityManagerInterface $entityManager): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);

        // Redirige vers la page du produit avec un paramètre 'added=true'
        return $this->render('user/detail.html.twig', [
            'product' => $product,
            'added' => false,
        ]);
    }

    #[Route('/product/{id}/buy', name: 'app_buy_product')]
    public function buyProduct(int $id, EntityManagerInterface $entityManager, Security $security): Response
    {
        /** @var User $user */
        $loggedUser = $security->getUser();

        if (!$loggedUser) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour acheter un produit.');
        }

        $users = $entityManager->getRepository(User::class)->findAll();
        $user = null;

        for ($x = 0; $x < count($users); $x++) {

            foreach ($users as $user) {
                if ($user->getEmail() === $loggedUser->getUserIdentifier()) {

                    $user = $entityManager->getRepository(User::class)->find($user->getId());

                }
            }

        }

        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvé');
        }

        $admins = $entityManager->getRepository(User::class)->findBy(['roles' => 'ROLE_ADMIN']);
        $buy_notification = null;

        if($user->isActive() === false) {
            // $buy_notification = $this->generateNotification->generate($user, 'You are inactive, you cannot buy products for the moment.');
            return $this->redirectToRoute('app_product_detail', ['id' => $id, 'product' => $product, 'added' => false]);

        }

        if($user->getPoints() < $product->getPrice()) {
            // $buy_notification = $this->generateNotification->generate($user, 'Unefficient points for user %s: %s points for a product that costs %s euros . The issue happened at %s' .$user->getUserIdentifier() .$user->getPoints().$product->getPrice().(new \DateTime())->format('Y-m-d H:i:s'));
            return $this->redirectToRoute('app_product_detail', ['id' => $id, 'product' => $product, 'added' => false]);
        } else {
            // Logique d'achat du produit (par exemple, mettre à jour la base de données)
            $user->setPoints($user->getPoints() - $product->getPrice());
            $entityManager->persist($user);
            $entityManager->flush();

        //     for ($x = 0; $x <= count($admins); $x++) {

        //     foreach ($admins as $admin) {
        //         $this->generateNotification->generate($admin, 'Profile updated: %s (firstname) and %s (lastname) at %s' .$user->getFirstname() .$user->getLastname().(new \DateTime())->format('Y-m-d H:i:s'));
        //     }
        // }
        }

        return $this->redirectToRoute('app_product_detail', [
            'id' => $id,
            'product'  => $product,
            'added' => true,
            'unefficient_notification' => $buy_notification
        ]);
    }

    #[Route('/product/{id}/cancel', name: 'app_cancel_product')]
    public function cancelProduct(int $id, EntityManagerInterface $entityManager, Security $security): Response
    {
        /** @var User $user */
        $loggedUser = $security->getUser();

        if (!$loggedUser) {
            throw $this->createAccessDeniedException("Vous devez être connecté pour annuler l'achat d'un produit.");
        }

        $user = $entityManager->getRepository(User::class)->find($loggedUser->getUserIdentifier());

        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvé');
        }

        $user->setPoints($user->getPoints() + $product->getPrice());
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_product_detail', [
            'id' => $id,
            'product'  => $product,
            'added' => false,
        ]);
    }
}
