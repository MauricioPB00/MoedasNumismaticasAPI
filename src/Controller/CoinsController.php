<?php

namespace App\Controller;

use App\Entity\Coins;
use App\Entity\BanknoteInfo;
use App\Entity\CoinInfo;
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
            $coinInfos = $em->getRepository(CoinInfo::class)
                ->findBy(['type_id' => $coin->getId()]);

            usort($coinInfos, function ($a, $b) {
                return $a->getYear() <=> $b->getYear();
            });

            $coinInfosArray = array_map(function ($info) {
                return [
                    'prices' => $info->getPrices(),
                    'year_info' => $info->getYear(),
                    'min_year' => $info->getMinYear(),
                    'max_year' => $info->getMaxYear(),
                    'mintage' => $info->getMintage(),
                    'issue_id' => $info->getIssueId(),
                    'type_id' => $info->getTypeId(),
                ];
            }, $coinInfos);

            $data = array_merge($data, [
                'thickness' => method_exists($coin, 'getThickness') ? $coin->getThickness() : null,
                'weight' => method_exists($coin, 'getWeight') ? $coin->getWeight() : null,
                'orientation' => method_exists($coin, 'getOrientation') ? $coin->getOrientation() : null,
                'technique' => method_exists($coin, 'getTechnique') ? $coin->getTechnique() : null,
                'edge' => method_exists($coin, 'getEdge') ? $coin->getEdge() : null,
                'edgeImg' => method_exists($coin, 'getEdgeImg') ? $coin->getEdgeImg() : null,
                'mints' => method_exists($coin, 'getMints') ? $coin->getMints() : null,
                'coinGroup' => method_exists($coin, 'getCoinGroup') ? $coin->getCoinGroup() : null,
                'coinInfo' => $coinInfosArray,
            ]);
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/coins/{id}/saveinfo", name="coin_save_info", methods={"POST"})
     */
    public function saveInfo(
        int $id,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'JSON inválido'], 400);
        }

        // "coin" ou "banknote"
        $type = $data['entityType'] ?? null;

        if (!in_array($type, ['coin', 'banknote'])) {
            return new JsonResponse(['error' => 'Tipo inválido'], 400);
        }

        // ENTIDADE CORRETA
        $repo = $type === 'coin' ? CoinInfo::class : BanknoteInfo::class;

        // EDITAR
        if (!empty($data['id'])) {
            $info = $em->getRepository($repo)->find($data['id']);

            if (!$info) {
                return new JsonResponse(['error' => 'Registro não encontrado'], 404);
            }
        } else {
            // CRIAR
            $info = new $repo();
            $info->setTypeId($id);
        }

        // CAMPOS PADRÃO
        $info->setYear($data['year_info'] ?? null);
        $info->setMinYear(isset($data['min_year']) && $data['min_year'] !== '' ? intval($data['min_year']) : 0);
        $info->setMaxYear(isset($data['max_year']) && $data['max_year'] !== '' ? intval($data['max_year']) : 0);
        $info->setMintage(isset($data['mintage']) && $data['mintage'] !== '' ? intval($data['mintage']) : 0);
        $info->setIssueId(isset($data['issue_id']) && $data['issue_id'] !== '' ? intval($data['issue_id']) : 0);

        // PREÇOS (json)
        if (isset($data['prices'])) {
            $info->setPrices($data['prices']);
        }

        // SALVAR
        $em->persist($info);
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'id' => $info->getId(),
            'message' => !empty($data['id'])
                ? 'Informações atualizadas com sucesso!'
                : 'Informações criadas com sucesso!'
        ]);
    }
}
