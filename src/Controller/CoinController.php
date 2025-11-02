<?php

namespace App\Controller;

use App\Entity\Coin;
use App\Entity\Banknote;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class CoinController extends AbstractController
{
    private $em;
    private JWTTokenManagerInterface $jwtManager;

    public function __construct(EntityManagerInterface $em, JWTTokenManagerInterface $jwtManager)
    {
        $this->em = $em;
        $this->jwtManager = $jwtManager;
    }


    /**
     * @Route("/coin/list", name="coin_list", methods={"GET"})
     */
    public function coinList(EntityManagerInterface $em): JsonResponse
    {
        $coins = $em->getRepository(Coin::class)->findAll();
        $banknotes = $em->getRepository(Banknote::class)->findAll();

        $coinData = array_map(function (Coin $coin) {
            return [
                'id' => $coin->getId(),
                'title' => preg_replace('/\s*\(.*?\)\s*/', '', $coin->getTitle()),
                'category' => $coin->getCategory(),
                'issuer' => $coin->getIssuer(),
                'min_year' => $coin->getMinYear(),
                'max_year' => $coin->getMaxYear(),
                'obverse' => $coin->getObverse(),
                'reverse' => $coin->getReverse(),
            ];
        }, $coins);

        $banknoteData = array_map(function (Banknote $note) {
            return [
                'id' => $note->getId(),
                'title' => preg_replace('/\s*\(.*?\)\s*/', '', $note->getTitle()),
                'category' => $note->getCategory(),
                'issuer' => $note->getIssuer(),
                'min_year' => $note->getMinYear(),
                'max_year' => $note->getMaxYear(),
                'obverse' => $note->getObverse(),
                'reverse' => $note->getReverse(),
            ];
        }, $banknotes);

        $allData = array_merge($coinData, $banknoteData);

        return new JsonResponse($allData);
    }

    /**
     * @Route("/coin/list/collection", name="coin_list_collection", methods={"GET"})
     */
    public function coinListCollection(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $issuer = $request->query->get('issuer', 'Brasil');

        $coinRepo = $em->getRepository(Coin::class);
        $banknoteRepo = $em->getRepository(Banknote::class);

        $coins = $coinRepo->createQueryBuilder('c')
            ->where('c.issuer = :issuer')
            ->setParameter('issuer', $issuer)
            ->getQuery()
            ->getResult();

        $banknotes = $banknoteRepo->createQueryBuilder('b')
            ->where('b.issuer = :issuer')
            ->setParameter('issuer', $issuer)
            ->getQuery()
            ->getResult();

        $coinData = array_map(function (Coin $coin) {
            return [
                'id' => $coin->getId(),
                'title' => preg_replace('/\s*\(.*?\)\s*/', '', $coin->getTitle()),
                'category' => $coin->getCategory(),
                'issuer' => $coin->getIssuer(),
                'min_year' => $coin->getMinYear(),
                'max_year' => $coin->getMaxYear(),
                'obverse' => $coin->getObverse(),
                'reverse' => $coin->getReverse(),
            ];
        }, $coins);

        $banknoteData = array_map(function (Banknote $note) {
            return [
                'id' => $note->getId(),
                'title' => preg_replace('/\s*\(.*?\)\s*/', '', $note->getTitle()),
                'category' => $note->getCategory(),
                'issuer' => $note->getIssuer(),
                'min_year' => $note->getMinYear(),
                'max_year' => $note->getMaxYear(),
                'obverse' => $note->getObverse(),
                'reverse' => $note->getReverse(),
            ];
        }, $banknotes);

        return new JsonResponse(array_merge($coinData, $banknoteData));
    }

    /**
     * @Route("/coin/list/collection/pdf", name="coin_list_collection_pdf", methods={"GET"})
     */
    public function coinListCollectionPdf(EntityManagerInterface $em): JsonResponse
    {
        $coins = $em->getRepository(Coin::class)->findAll();
        $banknotes = $em->getRepository(Banknote::class)->findAll();

        $coinData = array_map(function (Coin $coin) {
            return [
                'id' => $coin->getId(),
                'title' => $coin->getTitle(),
                'category' => $coin->getCategory(),
                'issuer' => $coin->getIssuer(),
                'min_year' => $coin->getMinYear(),
                'max_year' => $coin->getMaxYear(),
                'obverse' => $coin->getObverse(),
                'reverse' => $coin->getReverse(),
            ];
        }, $coins);

        $banknoteData = array_map(function (Banknote $note) {
            return [
                'id' => $note->getId(),
                'title' => $note->getTitle(),
                'category' => $note->getCategory(),
                'issuer' => $note->getIssuer(),
                'min_year' => $note->getMinYear(),
                'max_year' => $note->getMaxYear(),
                'obverse' => $note->getObverse(),
                'reverse' => $note->getReverse(),
            ];
        }, $banknotes);

        $allData = array_merge($coinData, $banknoteData);

        return new JsonResponse($allData);
    }

    /**
     * @Route("/moedas", name="moedas_create", methods={"POST"})
     * @param Request $request
     */
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $coin = new Coin();
        $coin->setTitle($data['title'] ?? 'Sem título');
        $coin->setCategory($data['category'] ?? 'Desconhecida');
        $coin->setIssuer($data['issuer'] ?? 'Brasil');
        $coin->setMinYear($data['min_year'] ?? null);
        $coin->setMaxYear($data['max_year'] ?? null);

        $coin->setObverse($data['obverse'] ?? 'SemFoto.png');
        $coin->setReverse($data['reverse'] ?? 'SemFoto.png');

        $em->persist($coin);
        $em->flush();

        return new JsonResponse(['status' => 'Moeda salva com sucesso!']);
    }
}
