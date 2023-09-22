<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
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

    /**
     * Get a customer list.
     *
     * @param Client|null $client
     */
    #[Route('', name: 'app_customer_index', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the paginated list of customers',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Customer::class, groups: ['index']))
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
    #[OA\Tag(name: 'Customer')]
    #[Security(name: 'Bearer')]
    #[OA\Response(
        response: 401,
        description: 'If the JWT Token is not found',
        content: new OA\JsonContent(
            ref: '#/components/schemas/InvalidToken',
            example: ['code' => 401, 'message' => 'JWT Token not found']),
    )]
    public function index(#[CurrentUser] ?Client $client, Request $request): JsonResponse
    {
        if (null === $client) {
            throw $this->createAccessDeniedException('Access Denied');
        }

        $limit = $request->query->getInt('limit', 20);
        $offset = $request->query->getInt('offset', 0);
        $customers = $this->customerRepository->findPaginatedByClient($client, $limit, $offset);

        $data = [
            'meta' => [
                'count' => count($customers),
                'limit' => $limit,
                'offset' => $offset,
                'total' => $this->customerRepository->countByClient($client),
            ],
            'data' => $customers,
        ];

        return $this->json($data, Response::HTTP_OK, [], ['groups' => 'index']);
    }

    /**
     * Get a customer.
     */
    #[Route('/{id}', name: 'app_customer_show', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns a customer informations',
        content: new Model(type: Customer::class, groups: ['index'])
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'customer id',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Tag(name: 'Customer')]
    #[Security(name: 'Bearer')]
    #[OA\Response(
        response: 401,
        description: 'If the JWT Token is not found',
        content: new OA\JsonContent(
            ref: '#/components/schemas/InvalidToken',
            example: ['code' => 401, 'message' => 'JWT Token not found']),
    )]
    public function showAction(Customer $customer, #[CurrentUser] ?Client $client): JsonResponse
    {
        if ($customer->getClient()->getId() !== $client->getId()) {
            throw $this->createAccessDeniedException('Access denied');
        }

        return $this->json($customer, Response::HTTP_OK, [], ['groups' => 'index']);
    }

    /**
     * Creates a new customer.
     */
    #[Route('', name: 'app_customer_create', methods: ['POST'])]
    #[OA\RequestBody(
        required: true,
        description: 'The new user informations',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(type: 'string'),
            example: ['username' => 'john.doe', 'email' => 'john@domaine.com', 'firstname' => 'John', 'lastname' => 'Doe'])
    )]
    #[OA\Response(
        response: 201, description: 'Returns the new customer informations',
        content: new Model(type: Customer::class, groups: ['index'])
    )]
    #[OA\Response(
        response: 400,
        description: 'Returns the list of errors',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(type: 'array', items: new OA\Items(type: 'string')),
            example: ['username' => ['This value should not be blank.']]),
    )]
    #[OA\Tag(name: 'Customer')]
    #[Security(name: 'Bearer')]
    #[OA\Response(
        response: 401,
        description: 'If the JWT Token is not found',
        content: new OA\JsonContent(
            ref: '#/components/schemas/InvalidToken',
            example: ['code' => 401, 'message' => 'JWT Token not found']),
    )]
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

    /**
     * deletes a customer.
     */
    #[Route('/{id}', name: 'app_customer_delete', methods: ['DELETE'])]
    #[OA\Response(response: 204, description: 'If the customer has been deleted')]
    #[OA\Response(response: 403, description: 'If the client cannot delete this customer')]
    #[OA\Response(response: 404, description: 'If the customer has not been found')]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'customer id',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Tag(name: 'Customer')]
    #[Security(name: 'Bearer')]
    #[OA\Response(
        response: 401,
        description: 'If the JWT Token is not found',
        content: new OA\JsonContent(
            ref: '#/components/schemas/InvalidToken',
            example: ['code' => 401, 'message' => 'JWT Token not found']),
    )]
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
