<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
class ProductController extends AbstractController
{
    #[Route('/products', name: 'app_product_index', methods:['GET'])]
    public function indexAction(ProductRepository $productRepository): JsonResponse
    {
        return $this->json($productRepository->findAll(), Response::HTTP_OK);
    }

    #[Route('/products/{id}', name: 'app_product_show', methods:['GET'])]
    public function showAction(Product $product): JsonResponse
    {
        return $this->json($product, Response::HTTP_OK);
    }
}
