<?php

namespace App\Controller;

use App\Entity\Sales;
use App\Entity\Clothes;
use App\Entity\SalesValue;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Suppliers;

class ClothesController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/api/clothes", name="clothes_create", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name'], $data['size'], $data['resale'], $data['bought'])) {
            return new JsonResponse([
                'error' => true,
                'message' => 'Dados inválidos',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $clothes = new Clothes();
        $clothes->setname($data['name']);
        $clothes->setsize($data['size']);
        $clothes->setresale($data['resale']);
        $clothes->setbought($data['bought']);
        $clothes->setsuppliers($data['supplier']);

        try {
            $this->em->persist($clothes);
            $this->em->flush();
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => true,
                'message' => 'Erro ao salvar no banco de dados',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse([
            'error' => false,
            'message' => 'Item de roupa criado com sucesso',
        ], JsonResponse::HTTP_CREATED);
    }


    /**
     * @Route("/api/clothes/last", name="clothing_get_last", methods={"GET"})
     */
    public function getLastClothing(): JsonResponse
    {
        $lastClothing = $this->em->getRepository(Clothes::class)->findOneBy([], ['id' => 'DESC']);

        if (!$lastClothing) {
            $nextId = 1;
            return new JsonResponse([
                'nextId' => $nextId,
            ], JsonResponse::HTTP_OK);
        }

        $nextId = $lastClothing->getId() + 1;

        return new JsonResponse([
            'nextId' => $nextId,
        ], JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/api/clothes/suppliers", name="suppliers_get", methods={"GET"})
     */
    public function getSuppliers(): JsonResponse
    {
        $suppliers = $this->em->getRepository(Suppliers::class)->findAll();

        if (!$suppliers) {
            return new JsonResponse(['message' => 'No suppliers item found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = [];

        foreach ($suppliers as $supplier) {
            $data[] = [
                'id' => $supplier->getId(),
                'name' => $supplier->getName(),
                'city' => $supplier->getCity(),
                'phone' => $supplier->getPhone(),
            ];
        }
        return $this->json($data);
    }

    /**
     * @Route("/api/clothes/register/suppliers", name="suppliers_post", methods={"POST"})
     */
    public function postSuppliers(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name'], $data['city'], $data['phone'])) {
            return new JsonResponse([
                'error' => true,
                'message' => 'Dados inválidos',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
        $existingSupplier = $this->em->getRepository(Suppliers::class)
            ->findByNameAndCity($data['name'], $data['city']);
        if ($existingSupplier) {
            return new JsonResponse([
                'error' => true,
                'message' => 'Fornecedor já cadastrado com este nome e cidade',
            ], JsonResponse::HTTP_CONFLICT);
        }

        $suppliers = new Suppliers();
        $suppliers->setname($data['name']);
        $suppliers->setcity($data['city']);
        $suppliers->setphone($data['phone']);

        try {
            $this->em->persist($suppliers);
            $this->em->flush();
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => true,
                'message' => 'Erro ao salvar no banco de dados',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse([
            'error' => false,
            'message' => 'Fornecedor criado com sucesso',
        ], JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/api/clothes/search/{id}", name="clothing_get_search", methods={"GET"})
     */
    public function getSearchClothing(int $id): JsonResponse
    {
        $clothing = $this->getDoctrine()->getRepository(Clothes::class)->find($id);

        if (!$clothing) {
            return new JsonResponse([
                'error' => true,
                'message' => 'Produto não encontrado.',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $clothing->getId(),
            'name' => $clothing->getName(),
            'size' => $clothing->getSize(),
            'resale' => $clothing->getResale(),
            'bought' => $clothing->getBought(),
        ];

        return new JsonResponse([
            'error' => false,
            'message' => 'Produto encontrado',
            'data' => $data,
        ]);
    }

    /**
     * @Route("/api/clothes/register/sale", name="sale_post", methods={"POST"})
     */
    public function postSale(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $lastSales = $entityManager->getRepository(Sales::class)->findOneBy([], ['id' => 'DESC']);
        $nextId = $lastSales ? $lastSales->getId() + 1 : 1;
        
        foreach ($data['clothing'] as $clothingItem) {
            
            $existingSalesValue = $entityManager->getRepository(SalesValue::class)
                ->findOneBy(['idClothes' => $clothingItem['id']]);

            if ($existingSalesValue) {
                return new JsonResponse([
                    'error' => true,
                    'message' => "O item com ID {$clothingItem['id']} já está registrado em outra venda.",
                ], JsonResponse::HTTP_CONFLICT);
            }
        
        if ($data['type'] === 'card') {
            $sales = new Sales();
            $sales->setid($nextId);
            $sales->setcard($data['card']);
            $sales->setflag($data['flag']);
            $sales->setdiscount($data['discount']);
            $sales->settotalWithDiscount($data['totalWithDiscount']);
            $sales->setcombinedTotalText($data['combinedTotalText']);
            $sales->setDaysale(new \DateTime('now'));
            $sales->settype($data['type']);
        } else if ($data['type'] === 'money') {
            $sales = new Sales();
            $sales->setid($nextId);
            $sales->setcard(0);
            $sales->setflag(0);
            $sales->setdiscount($data['discount']);
            $sales->settotalWithDiscount($data['totalWithDiscount']);
            $sales->setcombinedTotalText(0);
            $sales->setDaysale(new \DateTime('now'));
            $sales->settype($data['type']);
        }
     }
        // dd($sales);
        try {
            $entityManager->persist($sales);

            foreach ($data['clothing'] as $clothingItem) {
                $clothesItem = $entityManager->getRepository(Clothes::class)->find($clothingItem['id']);
                if (!$clothesItem) {
                    return new JsonResponse([
                        'error' => true,
                        'message' => "Item com ID {$clothingItem['id']} não encontrado na tabela Clothes",
                    ], JsonResponse::HTTP_BAD_REQUEST);
                }

                $entityManager->remove($clothesItem);

                $salesValue = new SalesValue();
                $salesValue->setid($nextId);
                $salesValue->setresale($clothingItem['resale']);
                $salesValue->setbought($clothingItem['bought']);
                $salesValue->setidClothes($clothingItem['id']);
                $salesValue->setDaysale(new \DateTime('now'));
                $salesValue->setidSales($sales->getId());
                $entityManager->persist($salesValue);
            }

            $entityManager->flush();

            return new JsonResponse([
                'error' => false,
                'message' => 'Vendido com sucesso',
            ], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => true,
                'message' => 'Erro ao salvar no banco de dados',
                'details' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

