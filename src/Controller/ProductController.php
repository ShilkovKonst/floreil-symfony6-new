<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Category;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ProductController extends AbstractController
{
    #[Route('/{catSlug}', name: 'app_product', priority: 0)]
    public function index($catSlug, CategoryRepository $categories, ProductRepository $products): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var Category $category */
        $category = $categories->findOneBySlug($catSlug);
        $catProducts = $products->findByCategory($category->getId());


        return $this->render('product/index.html.twig', [
            'user' => $user,
            'category' => $category,
            'products' => $catProducts,
        ]);
    }    

    #[Route('/{catSlug}/{prodSlug}', name: 'app_product_show_one', priority: 0)]
    public function showOneProduct($catSlug, $prodSlug, ProductRepository $products, CategoryRepository $categories): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var Category $category */
        $category = $categories->findOneBySlug($catSlug);
        /** @var Product $product */
        $product = $products->findOneBy(['slug' => $prodSlug, 'category' => $category]);

        return $this->render(
            'product/show_one.html.twig',
            [
                'user' => $user,
                'product' => $product,
            ]
        );
    }    

    #[Route('/search-result/{queryType}/{queryValue}', name: 'app_product_search_results', priority: 2)]
    public function showSearchResult($queryType, $queryValue, ProductRepository $products)
    {
        /** @var User $user */
        $user = $this->getUser();
        $searchProducts = $products->findByPartial($queryType, $queryValue);
        $category=null;

        $this->addFlash('success', 'Le resultat de recherche est : ');
        return $this->render('product/index.html.twig', [
            'user' => $user,
            'category' => $category,
            'products' => $searchProducts
        ]);
    }
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/{catSlug}/{prodSlug}/add-to-cart', name: 'app_product_addToCart')]
    public function addToCart($catSlug, $prodSlug, CategoryRepository $categories, ProductRepository $products, CartRepository $carts, Request $request): Response
    {
        $referer = $request->headers->get('referer');

        /** @var User $user */
        $user = $this->getUser();
        /** @var Category $category */
        $category = $categories->findOneBySlug($catSlug);
        $product = $products->findOneBy(['slug' => $prodSlug, 'category' => $category]);

        $qntyToCart = $request->request->get('cart_qnty');

        if ($user->isVerified()) {
            if ($carts->findOneByUserAndProduct($user, $product)) {
                $cart = $carts->findOneByUserAndProduct($user, $product);
                $cart->setQnty($qntyToCart);

                // $entityManager->persist($cart);
                $carts->save($cart, true);
            } else {
                $cart = new Cart;
                $cart->setProducts($product);
                $cart->setUsers($user);
                $cart->setQnty($qntyToCart);

                // $entityManager->persist($cart);
                $carts->save($cart, true);
            }
            
            $this->addFlash('success', "Ce produit a été ajouté dans votre panier.");
            return $this->redirect($referer);
        } else {
            $this->addFlash('danger', "Votre compte doit être activé.");
            return $this->redirect($referer);
        }

        return $this->redirect($referer);
    }
}