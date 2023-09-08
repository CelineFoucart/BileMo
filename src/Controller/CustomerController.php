<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\CustomerRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/customers')]
class CustomerController extends AbstractController
{
    public function __construct(private CustomerRepository $customerRepository)
    {
        
    }

    #[Route('', name: 'app_customer_index', methods:['GET'])]
    public function index(#[CurrentUser] ?Client $client, Request $request): JsonResponse
    {
        if ($client === null) {
            throw $this->createAccessDeniedException('Access Denied');
        }

        $limit = $request->query->getInt('limit', 20);
        $offset = $request->query->getInt('offset', 0);
        $customers = $this->customerRepository->findPaginatedByClient($client, $limit, $offset);

        $data = [
            "meta" => [
                'count' => count($customers),
                'limit' => $limit,
                'offset' => $offset,
                'total' => $this->customerRepository->countByClient($client)
            ],
            "data" => $customers
        ];

        return $this->json($data, Response::HTTP_OK, [], ['groups' => 'index']);
    }
}
