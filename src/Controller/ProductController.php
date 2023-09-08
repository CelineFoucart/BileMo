<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api')]
class ProductController extends AbstractController
{
    #[Route('/products', name: 'app_product_index', methods:['GET'])]
    public function indexAction(ProductRepository $productRepository, Request $request): JsonResponse
    {
        $limit = $request->query->getInt('limit', 20);
        $offset = $request->query->getInt('offset', 0);
        $products = $productRepository->FindAllPaginated($limit, $offset);

        $data = [
            "meta" => [
                'count' => count($products),
                'limit' => $limit,
                'offset' => $offset,
                'total' => $productRepository->count([])
            ],
            "data" => $products
        ];

        return $this->json($data, Response::HTTP_OK);
    }

    #[Route('/products/{id}', name: 'app_product_show', methods:['GET'])]
    public function showAction(Product $product): JsonResponse
    {
        return $this->json($product, Response::HTTP_OK);
    }
}
