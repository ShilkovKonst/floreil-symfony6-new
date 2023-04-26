<?php

namespace App\Controller;

use DateTime;
use App\Entity\Cart;
use App\Entity\User;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\OrderDetail;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\OrderDetailRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart', priority:2)]
    public function index(CartRepository $carts): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $userCartProducts = $carts->findByUser($user);
        $stripePublicKey = $this->getParameter('stripe_public_key');

        if ($userCartProducts != null) {
            return $this->render('cart/index.html.twig', [
                'user' => $user,
                'userCartProducts' => $userCartProducts,
                'stripe_public_key' => $stripePublicKey,
            ]);
        }
        return $this->render('cart/index.html.twig', [
            'user' => $user,
            'userCartProducts' => $userCartProducts,
        ]);
    }

    #[Route('/cart/{id}', name: 'app_cart_chage_qnty')]
    public function changeQnty($id, Request $request, CartRepository $carts, ProductRepository $products, EntityManagerInterface $entityManager)
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var Product $product */
        $product = $products->findOneById($id);
        // dd($product);
        $cartProdToChangeQnty = $carts->findOneByUserAndProduct($user, $product);
        $qntyToChange = $request->request->get('cart_qnty');
        $cartProdToChangeQnty->setQnty($qntyToChange);

        $entityManager->persist($cartProdToChangeQnty);
        $entityManager->flush();

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/delete/{id}', name: 'app_cart_delete_one')]
    public function deleteOne($id, CartRepository $carts, ProductRepository $products)
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var Product $product */
        $product = $products->findOneById($id);
        $cartProdToDelete = $carts->findOneByUserAndProduct($user, $product);

        $carts->remove($cartProdToDelete, true);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/validate-commande/{price}', name: 'app_cart_validate', priority: 2)]
    public function validateCart($price, CartRepository $carts, ProductRepository $products, OrderDetailRepository $orderDetails, EntityManagerInterface $manager)
    {
        /** @var User $user */
        $user = $this->getUser();
        $userCartProducts = $carts->findByUser($user);
        $date = new DateTime();

        /** @var Cart $prod */
        foreach ($userCartProducts as $prod) {
            $ids[] = $prod->getProducts()->getId();
        }
        $chosenProducts = $products->findById($ids);

        $order = new Order;
        $order->setuser($user);
        $order->setPrice($price);
        $order->setReference($user->getId() . '_' . $date->format('Ymd') . '_' . count($userCartProducts));
        /* Adding products from cart to users order */
        for ($i = 0; $i < count($userCartProducts); $i++) {
            $orderDetail = new OrderDetail;
            $orderDetail->setOrders($order);
            $orderDetail->setProducts($chosenProducts[$i]);
            $orderDetail->setQnty($userCartProducts[$i]->getQnty());
            $orderDetails->save($orderDetail, true);
        }
        for ($i = 0; $i < count($userCartProducts); $i++) {
            $chosenProducts[$i]->setinStockQnty($chosenProducts[$i]->getinStockQnty() - $userCartProducts[$i]->getQnty());

            $manager->persist($chosenProducts[$i]);
            $carts->remove($userCartProducts[$i], true);
        }
        $manager->flush();

        $this->addFlash('success', 'Votre commande est passée');
        return $this->redirectToRoute('app_cart', [
            'user' => $user,
            'userCartProducts' => $userCartProducts,
        ]);
    }
}
