<?php

namespace App\Controller;

use App\Entity\Product;
use OpenApi\Attributes as OA;
use App\Repository\ProductRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Cache\ItemInterface;

#[Route('/api')]
class ProductController extends AbstractController
{
    public const CACHE_EXPIRATION = 86400; 

    /**
     * Gets the product list.
     */
    #[Route('/products', name: 'app_product_index', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the paginated list of product',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Product::class, groups: ['index']))
        )
    )]
    #[OA\Parameter(
        name: 'limit',
        in: 'query',
        description: 'max number of records to return',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Parameter(
        name: 'offset',
        in: 'query',
        description: 'number of records to skip',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Tag(name: 'Product')]
    #[Security(name: 'Bearer')]
    #[OA\Response(
        response: 401,
        description: 'If the JWT Token is not found',
        content: new OA\JsonContent(
            ref: '#/components/schemas/InvalidToken',
            example: ['code' => 401, 'message' => 'JWT Token not found']),
    )]
    public function indexAction(ProductRepository $productRepository, Request $request, TagAwareCacheInterface $cachePool): JsonResponse
    {
        $limit = $request->query->getInt('limit', 20);
        $offset = $request->query->getInt('offset', 0);
        $idCache = 'products-' . $limit . '-' . $offset;
        $data = $cachePool->get($idCache, function(ItemInterface $item) use ($productRepository, $limit, $offset) {
            $item->tag('productsCache')->expiresAfter(ProductController::CACHE_EXPIRATION);

            $products = $productRepository->FindAllPaginated($limit, $offset);
            $data = [
                'meta' => [
                    'count' => count($products),
                    'limit' => $limit,
                    'offset' => $offset,
                    'total' => $productRepository->count([]),
                ],
                'data' => $products,
            ];

            return $data;
        });

        return $this->json($data, Response::HTTP_OK, [], ['groups' => 'index']);
    }

    /**
     * Get a product.
     */
    #[Route('/products/{id}', name: 'app_product_show', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns a product informations',
        content: new Model(type: Product::class, groups: ['index'])
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'query',
        description: 'product id',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Tag(name: 'Product')]
    #[Security(name: 'Bearer')]
    #[OA\Response(
        response: 401,
        description: 'If the JWT Token is not found',
        content: new OA\JsonContent(
            ref: '#/components/schemas/InvalidToken',
            example: ['code' => 401, 'message' => 'JWT Token not found']),
    )]
    public function showAction(Product $product): JsonResponse
    {
        return $this->json($product, Response::HTTP_OK, [], ['groups' => 'index']);
    }
}
