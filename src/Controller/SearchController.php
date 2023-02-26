<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search', priority: 2)]
    public function searchByQuery(Request $request): Response
    {
        $queryType = $request->query->get('queryType');
        $queryValue = $request->query->get('queryValue');

        return $this->redirectToRoute('app_product_search_results', [
            'queryValue' => $queryValue,
            'queryType' => $queryType,
        ]);
    }
}
