<?php

namespace App\Controller;

use Stripe\StripeClient;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class CheckoutController extends AbstractController
{
    #[Route('/checkout/{amount}', name: 'app_checkout', priority: 3)]
    public function checkout($amount, Request $request, CartRepository $carts, ProductRepository $products, ValidatorInterface $validator): Response
    {
        $user = $this->getUser();
        $userCartProducts = $carts->findByUser($user);
        $stripePublicKey = $this->getParameter('stripe_public_key');

        $invalidQnty = false;
        foreach ($userCartProducts as $prod) {
            $ids[] = $prod->getProducts()->getId();
        }
        $chosenProducts = $products->findById($ids);

        $currency = 'eur';

        for ($i = 0; $i < count($userCartProducts); $i++) {
            $errors = $validator->validate($chosenProducts[$i]);
            if ($chosenProducts[$i]->getInstockQnty() < $userCartProducts[$i]->getQnty()) {
                $invalidQnty = true;
            }
        }

        if (count($errors) > 0 || $invalidQnty) {
            // dd($errors, $invalidQnty);
            $this->addFlash('danger', 'La quantité des produits est insuffisante. Le payment est annulé. Verifiez la quantité des produits.');
            $this->redirectToRoute('app_cart', [
                'stripe_public_key' => $stripePublicKey,
                'amount' => $amount * 100,
            ]);
        } else {

            if ($request->isMethod('POST')) {
                $token = $request->get('stripeToken');

                $stripe = new StripeClient($this->getParameter('stripe_api_key'));
                $stripe->charges->create([
                    'amount' => $amount * 100,
                    'currency' => $currency,
                    'source' => $token,
                    'description' => 'My First Test Charge (created for API docs at https://www.stripe.com/docs/api)',
                ]);

                $this->addFlash('success', 'Your payment goes well for now');
                return $this->redirectToRoute('app_cart_validate', [
                    'price' => $amount
                ]);
            }
        }

        return $this->render('cart/index.html.twig', [
            'user' => $user,
            'chosenProducts' => $chosenProducts,
            'userCartProducts' => $userCartProducts,
            'stripe_public_key' => $stripePublicKey,
        ]);
    }
}
