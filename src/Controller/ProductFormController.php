<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Form\ProductForm;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ProductFormController extends AbstractController
{
    #[Route('/create/product', name: 'app_product_form')]
    public function index(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        /** @var User $user */
        $loggedUser = $security->getUser();

        $users = $entityManager->getRepository(User::class)->findAll();

        $expectedRole = 'ROLE_ADMIN';

        $product = new Product();
        $form = $this->createForm(ProductForm::class, $product);
        $form->handleRequest($request);

        for ($x = 0; $x <= count($users); $x++) {

            foreach ($users as $user) {
                if ($user->getEmail() === $loggedUser->getUserIdentifier()) {

                    if (in_array($expectedRole, $user->getRoles(), true)) {

                        if ($form->isSubmitted() && $form->isValid()) {

            // User infos

            $product->setName($form->get('name')->getData())
                ->setDescription($form->get('description')->getData())
                ->setPrice($form->get('price')->getData())
                ->setCategory($form->get('category')->getData());

            $user->addProduct($product);

            $entityManager->persist($product);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin');
        }

                    } else {

                        $this->createAccessDeniedException('Access denied: You do not have permission to create a product.');
                    }

                }
            }

        }

        return $this->render('admin/create_product.html.twig', [
            'controller_name' => 'ProductFormController',
            'productForm' => $form
        ]);
    }

    #[Route('/edit/product/{id}', name: 'app_product_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, Security $security, ?int $id): Response
    {
        /** @var User $user */
        $loggedUser = $security->getUser();

        $users = $entityManager->getRepository(User::class)->findAll();

        $expectedRole = 'ROLE_ADMIN';

        $product = $entityManager->getRepository(Product::class)->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        $form = $this->createForm(ProductForm::class, $product);
        $form->handleRequest($request);

        for ($x = 0; $x <= count($users); $x++) {

            foreach ($users as $user) {
                if ($user->getEmail() === $loggedUser->getUserIdentifier()) {

                    if (in_array($expectedRole, $user->getRoles(), true)) {

                        if ($form->isSubmitted() && $form->isValid()) {

            // User infos

            $product->setName($form->get('name')->getData())
                ->setDescription($form->get('description')->getData())
                ->setPrice($form->get('price')->getData())
                ->setCategory($form->get('category')->getData());

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin');
        }

                    } else {

                        $this->createAccessDeniedException('Access denied: You do not have permission to edit a product.');
                    }

                }
            }

        }

        return $this->render('admin/create_product.html.twig', [
            'controller_name' => 'ProductFormController',
            'productForm' => $form
        ]);
    }

    #[Route('/delete/product/{id}', name: 'app_product_delete')]
    public function delete(AuthenticationUtils $authenticationUtils, EntityManagerInterface $entityManager, Security $security, ?int $id): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        /** @var User $user */
        $loggedUser = $security->getUser();

        $users = $entityManager->getRepository(User::class)->findAll();

        $product = $entityManager->getRepository(Product::class)->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        $expectedRole = 'ROLE_ADMIN';

        for ($x = 0; $x <= count($users); $x++) {

            foreach ($users as $user) {
                if ($user->getEmail() === $loggedUser->getUserIdentifier()) {

                    if (in_array($expectedRole, $user->getRoles(), true)) {

                        if (!$loggedUser) {
            throw $this->createAccessDeniedException('You must be logged in to delete a product.');
        }

                        $entityManager->remove($product);
                        $entityManager->flush();

                    } else {

                        $this->createAccessDeniedException('Access denied: You do not have permission to access this page.');
                    }

                }
            }

        }

        return $this->redirectToRoute('app_admin', [
            'controller_name' => 'AdminController',
            'products' => $entityManager->getRepository(Product::class)->findAll(),
            'categories' => [], // Replace with actual categories if needed
            'error' => $error,
            'last_username' => $loggedUser->getUserIdentifier(),
            'selectedCategoryId' => 0, // All categories
            'selectedCategoryName' => 'Toutes les catégories'
        ]);
    }
}