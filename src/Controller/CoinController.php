<?php 

namespace App\Controller;

use App\Entity\Coin;
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

        $data = array_map(function(Coin $coin) {
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

        return new JsonResponse($data);
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
