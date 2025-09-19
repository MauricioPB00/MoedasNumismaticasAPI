<?php

namespace App\Controller;

use App\Entity\Coins;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class CoinsController extends AbstractController
{
    private $em;
    private JWTTokenManagerInterface $jwtManager;

    public function __construct(EntityManagerInterface $em, JWTTokenManagerInterface $jwtManager)
    {
        $this->em = $em;
        $this->jwtManager = $jwtManager;
    }

    /**
     * @Route("/coins/{id}", name="coin_show", methods={"GET"})
     */
    public function show($id, EntityManagerInterface $em): JsonResponse
    {
        $coin = $em->getRepository(Coins::class)->find($id);

    if (!$coin) {
        return new JsonResponse(['error' => 'Moeda não encontrada'], 404);
    }

     $data = [
        'id' => $coin->getId(),
        'title' => $coin->getTitle(),
        'category' => $coin->getCategory(),
        'url' => $coin->getUrl(),
        'type' => $coin->getType(),
        'issuer' => $coin->getIssuer(),
        'minYear' => $coin->getMinYear(),
        'maxYear' => $coin->getMaxYear(),
        'ruler' => $coin->getRuler(),
        'valueText' => $coin->getValueText(),
        'valueNumeric' => $coin->getValueNumeric(),
        'currencyName' => $coin->getCurrencyName(),
        'currency' => $coin->getCurrency(),
        'isDemonetized' => $coin->getIsDemonetized(),
        'demonetizationDate' => $coin->getDemonetizationDate()
            ? $coin->getDemonetizationDate()->format('Y-m-d')
            : null,
        'size' => $coin->getSize(),
        'thickness' => $coin->getThickness(),
        'shape' => $coin->getShape(),
        'weight' => $coin->getWeight(),
        'orientation' => $coin->getOrientation(),
        'composition' => $coin->getComposition(),
        'technique' => $coin->getTechnique(),
        'obverse' => $coin->getObverse(),
        'reverse' => $coin->getReverse(),
        'edge' => $coin->getEdge(),
        'comments' => $coin->getComments(),
        'relatedTypes' => $coin->getRelatedTypes(),
        'tags' => $coin->getTags(),
        'referenceCode' => $coin->getReferenceCode(),
        'mints' => $coin->getMints(),
        'coinGroup' => $coin->getCoinGroup(),
        'obverseImg' => $coin->getObverseImg(),
        'reverseImg' => $coin->getReverseImg(),
        'edgeImg' => $coin->getEdgeImg(),
    ];

    return new JsonResponse($data);
    }
}
