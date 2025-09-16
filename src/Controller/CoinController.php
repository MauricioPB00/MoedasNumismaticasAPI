<?php 

namespace App\Controller;

use App\Entity\Coin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CoinController extends AbstractController
{
    #[Route('/api/moedas', name: 'moedas_list', methods: ['GET'])]
    public function list(EntityManagerInterface $em): JsonResponse
    {
        $coins = $em->getRepository(Coin::class)->findAll();

        $data = array_map(function(Coin $coin) {
            return [
                'id' => $coin->getId(),
                'title' => $coin->getTitle(),
                'category' => $coin->getCategory(),
                'issuer' => $coin->getIssuer(),
                'min_year' => $coin->getMinYear(),
                'max_year' => $coin->getMaxYear(),
                'obverse' => $coin->getObverse() ? '/uploads/coins/'.$coin->getObverse() : null,
                'reverse' => $coin->getReverse() ? '/uploads/coins/'.$coin->getReverse() : null,
            ];
        }, $coins);

        return $this->json($data);
    }

    #[Route('/api/moedas', name: 'moedas_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $coin = new Coin();
        $coin->setTitle($data['title']);
        $coin->setCategory($data['category']);
        $coin->setIssuer($data['issuer']);
        $coin->setMinYear($data['min_year'] ?? null);
        $coin->setMaxYear($data['max_year'] ?? null);

        // nomes das imagens (ex: 109110_obverse.jpg)
        $coin->setObverse($data['obverse']);
        $coin->setReverse($data['reverse']);

        $em->persist($coin);
        $em->flush();

        return $this->json(['status' => 'Moeda salva com sucesso!']);
    }
}
