<?php

namespace App\Controller;

use App\Entity\AlbumCoin;
use App\Entity\Album;
use App\Entity\Coin;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AlbumController extends AbstractController
{
    private $em;
    private $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    /**
     * @Route("/album/add", name="album_add", methods={"POST"})
     */
    public function albumAdd(Request $request): JsonResponse
    {
        $userJwt = $this->security->getUser(); // pode ser apenas UserInterface
        if (!$userJwt) {
            return $this->json(['error' => 'Usuário não autenticado'], 401);
        }

        // Recupera a entidade User real
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $userJwt->getUsername()]);
        if (!$user) {
            return $this->json(['error' => 'Usuário não encontrado'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $coinId = $data['coinId'] ?? null;
        $year = $data['year'] ?? null;
        $quantity = $data['quantity'] ?? 1;
        $condition = $data['condition'] ?? null;

        if (!$coinId) {
            return $this->json(['error' => 'CoinId obrigatório'], 400);
        }

        // Cria álbum se não existir
        $album = $user->getAlbum();
        if (!$album) {
            $album = new Album();
            $album->setUser($user);
            $this->em->persist($album);
        }

        // Procura a moeda
        $coin = $this->em->getRepository(Coin::class)->find($coinId);
        if (!$coin) {
            return $this->json(['error' => 'Moeda não encontrada'], 404);
        }

        // Cria associação
        $albumCoin = new AlbumCoin();
        $albumCoin->setAlbum($album)
            ->setCoin($coin)
            ->setYear($year)
            ->setQuantity($quantity)
            ->setCondition($condition);

        $this->em->persist($albumCoin);
        $this->em->flush();

        return $this->json(['success' => 'Moeda adicionada ao álbum com sucesso']);
    }
}
