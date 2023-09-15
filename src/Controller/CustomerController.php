<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Customer;
use App\Form\CustomerType;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/customers')]
class CustomerController extends AbstractController
{
    public function __construct(
        private CustomerRepository $customerRepository,
        private EntityManagerInterface $entityManager
    ) {
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

    #[Route('/{id}', name: 'app_customer_show', methods:['GET'])]
    public function showAction(Customer $customer, #[CurrentUser] ?Client $client): JsonResponse
    {
        if ($customer->getClient()->getId() !== $client->getId()) {
            throw $this->createAccessDeniedException('Access denied');
        }

        return $this->json($customer, Response::HTTP_OK, [], ['groups' => 'index']);
    }
    
    #[Route('', name: 'app_customer_create', methods:['POST'])]
    public function createAction(
        Request $request, 
        #[CurrentUser] ?Client $client, 
        ValidatorInterface $validator, 
        SerializerInterface $serializer
    ): JsonResponse {
        /** @var Customer */
        $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json', ['groups' => 'index']);
        $customer->setClient($client);
        /** @var ConstraintViolationInterface[] */
        $violations = $validator->validate($customer);

        if (count($violations) > 0) {
            $errors = [];

            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()][] = $violation->getMessage();
            }

            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($customer);
        $this->entityManager->flush();
        
        return $this->json($customer, Response::HTTP_CREATED, [], ['groups' => 'index']);
    }

    #[Route('/{id}', name: 'app_customer_delete', methods:['DELETE'])]
    public function deleteAction(#[CurrentUser] ?Client $client, Customer $customer): JsonResponse
    {
        if ($customer->getClient()->getId() !== $client->getId()) {
            throw $this->createAccessDeniedException('Access denied');
        }

        $this->entityManager->remove($customer);
        $this->entityManager->flush();
        
        return $this->json('', Response::HTTP_NO_CONTENT);
    }
}
