<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route("/api")]
class CustomerController extends AbstractController
{
    public function __construct(
        private CustomerRepository $customerRepository,
        private EntityManagerInterface $manager,
        private UserRepository $userRepository
    ) {}

    #[Route('/customers', name: 'customers_get', methods: ["GET"])]
    public function get(Request $request): JsonResponse
    {
        return $this->json($this->customerRepository->findBy(['user' => $this->getUserByToken($request)]), 200, [], ['groups' => 'customer:read']);
    }

    #[Route('/customers/{id}', name: 'customer_get', methods: ["GET"])]
    public function show($id, Request $request): JsonResponse
    {
        $customer = $this->customerRepository->find($id);
        if (!$customer) { return $this->json(["No client found"], 400);}

        //Check if right user
        $user = $this->getUserByToken($request);
        if($user != $customer->getUser()) { return $this->json(["Wrong authorization"], 403); }

        return $this->json($customer, 200, [], ['groups' => 'customer:read']);
    }

    #[Route('/customers', name: 'customers_post', methods: ["POST"])]
    public function post(Request $request, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        $data = $request->getContent();

        try {
            /** @var Customer $customer */
            $customer = $serializer->deserialize($data, Customer::class, 'json');

            // Errors entity
            $errors = $validator->validate($customer);
            if (count($errors)) { return $this->json($errors, 400); }

            $user = $this->getUserByToken($request);
            $customer->setUser($user);

            $this->manager->persist($customer);
            $this->manager->flush();

            return $this->json($customer, 201, [], ['groups' => 'customer:read']);

        } catch (NotEncodableValueException $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }

    }

    #[Route('/customers/{id}', name: 'customer_put', methods: ["PUT"])]
    public function put($id, Request $request, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {

        //Check if customer
        $customer = $this->customerRepository->find($id);
        if (!$customer) { return $this->json(["No client found"], 400);}

        //Check if right user
        $user = $this->getUserByToken($request);
        if($user != $customer->getUser()) { return $this->json(["Wrong authorization"], 403); }

        $data = $request->getContent();

        try {
            /** @var Customer $customerData */
            $customerData = $serializer->deserialize($data, Customer::class, 'json');

            // Errors entity
            $errors = $validator->validate($customerData);
            if (count($errors)) { return $this->json($errors, 400); }

            $customer->setFirstname($customerData->getFirstname() ?? $customer->getFirstname())
                     ->setLastname($customerData->getLastname() ?? $customer->getLastname())
                     ->setEmail($customerData->getEmail() ?? $customer->getEmail())
                     ->setPhone($customerData->getPhone() ?? $customer->getPhone())
            ;

            $this->manager->flush();

            return $this->json($customer, 201, [], ['groups' => 'customer:read']);

        } catch (NotEncodableValueException $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    #[Route('/customers/{id}', name: 'customer_delete', methods: ["DELETE"])]
    public function delete($id)
    {

        $customer = $this->customerRepository->find($id);
        if (!$customer) { return $this->json(["No client found"], 400);}

        try {
            $this->manager->remove($customer);
            $this->manager->flush();
            return $this->json(["success"], 201, [], ['groups' => 'customer:read']);
        } catch (NotEncodableValueException $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    function getUserByToken(Request $request){

        //GET TOKEN
        $authorizationHeader = $request->headers->get('Authorization');
        $token = substr($authorizationHeader, 7);

        //FORMAT JWT TOKEN TO OBJ
        $tks = explode('.', $token);
        if (count($tks) != 3) {
            return null;
        }
        list($headb64, $bodyb64, $cryptob64) = $tks;
        $input=$bodyb64;
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        $input = (base64_decode(strtr($input, '-_', '+/')));
        $obj = json_decode($input, false, 512, JSON_BIGINT_AS_STRING);

        //GET USER
        $user = $this->userRepository->findOneBy(['email' => $obj->email]);

        if ($user) { return $user; }
        return $this->json("JWT Auth failed", 400);

    }

}