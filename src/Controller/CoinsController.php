<?php

namespace App\Controller;

use App\Entity\Coins;
use App\Entity\BanknoteInfo;
use App\Entity\Banknotes;
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
        $type = 'coin';

        if (!$coin) {
            $coin = $em->getRepository(Banknotes::class)->find($id);
            $type = 'banknote';
        }

        if (!$coin) {
            return new JsonResponse(['error' => 'Item não encontrado'], 404);
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
            'currency' => $coin->getCurrency(),
            'isDemonetized' => $coin->getIsDemonetized(),
            'demonetizationDate' => $coin->getDemonetizationDate()
                ? $coin->getDemonetizationDate()->format('Y-m-d')
                : null,
            'size' => $coin->getSize(),
            'shape' => $coin->getShape(),
            'composition' => $coin->getComposition(),
            'obverse' => $coin->getObverse(),
            'reverse' => $coin->getReverse(),
            'comments' => $coin->getComments(),
            'relatedTypes' => $coin->getRelatedTypes(),
            'tags' => $coin->getTags(),
            'obverseImg' => method_exists($coin, 'getObverseImg') ? $coin->getObverseImg() : null,
            'reverseImg' => method_exists($coin, 'getReverseImg') ? $coin->getReverseImg() : null,
            'entityType' => $type,
        ];

        if ($type === 'banknote') {
            $banknoteInfos = $em->getRepository(BanknoteInfo::class)
                ->findBy(['type_id' => $coin->getId()]);

            usort($banknoteInfos, function ($a, $b) {
                return $a->getYear() <=> $b->getYear();
            });

            $banknoteInfoArray = array_map(function ($info) {
                return [
                    'prices' => $info->getPrices(),
                    'year_info' => $info->getYear(),
                    'min_year' => $info->getMinYear(),
                    'max_year' => $info->getMaxYear(),
                    'mintage' => $info->getMintage(),
                    'issue_id' => $info->getIssueId(),
                    'type_id' => $info->getTypeId(),
                ];
            }, $banknoteInfos);

            $data = array_merge($data, [
                'issuingEntity' => $coin->getIssuingEntity(),
                'size2' => $coin->getSize2(),
                'series' => $coin->getSeries(),
                'referenceCode' => $coin->getReferenceCode(),
                'printers' => $coin->getPrinters(),
                'banknoteInfo' => $banknoteInfoArray,
            ]);
        } else {
            $data = array_merge($data, [
                'thickness' => method_exists($coin, 'getThickness') ? $coin->getThickness() : null,
                'weight' => method_exists($coin, 'getWeight') ? $coin->getWeight() : null,
                'orientation' => method_exists($coin, 'getOrientation') ? $coin->getOrientation() : null,
                'technique' => method_exists($coin, 'getTechnique') ? $coin->getTechnique() : null,
                'edge' => method_exists($coin, 'getEdge') ? $coin->getEdge() : null,
                'edgeImg' => method_exists($coin, 'getEdgeImg') ? $coin->getEdgeImg() : null,
                'mints' => method_exists($coin, 'getMints') ? $coin->getMints() : null,
                'coinGroup' => method_exists($coin, 'getCoinGroup') ? $coin->getCoinGroup() : null,
            ]);
        }

        return new JsonResponse($data);
    }
}
