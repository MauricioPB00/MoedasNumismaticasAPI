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
        $userJwt = $this->security->getUser();
        if (!$userJwt) {
            return $this->json(['error' => 'Usuário não autenticado'], 401);
        }

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $userJwt->getUserIdentifier()]);
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

        // Verifica se já existe AlbumCoin com mesmo album, coin e ano
        $albumCoinRepo = $this->em->getRepository(AlbumCoin::class);
        $existing = $albumCoinRepo->findOneBy([
            'album' => $album,
            'coin' => $coin,
            'year' => $year,
            'condition' => $condition,
        ]);

        if ($existing) {
            // Já existe -> soma a quantidade
            $existing->setQuantity($existing->getQuantity() + $quantity);

            $this->em->flush();

            return $this->json(['success' => 'Quantidade atualizada com sucesso']);
        }

        // Caso não exista, cria associação nova
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


    /**
     * @Route("/album/me", name="album_get_me", methods={"GET"})
     */
    public function getAlbumUser(): JsonResponse
    {
        $userJwt = $this->security->getUser();
        if (!$userJwt) {
            return $this->json(['error' => 'Usuário não autenticado'], 401);
        }

        // Busca o usuário real no banco
        $user = $this->em->getRepository(User::class)
            ->findOneBy(['email' => $userJwt->getUserIdentifier()]);
        if (!$user) {
            return $this->json(['error' => 'Usuário não encontrado'], 404);
        }

        // Busca o álbum do usuário
        $album = $this->em->getRepository(Album::class)->findOneBy(['user' => $user]);
        if (!$album) {
            return $this->json([]);
        }

        // Busca as moedas do álbum
        $albumCoins = $this->em->getRepository(AlbumCoin::class)->findBy(['album' => $album]);

        $result = [];
        foreach ($albumCoins as $ac) {
            $result[] = [
                'coinId' => $ac->getCoin()->getId(),
                'coinTitle' => preg_replace('/\s*\(.*?\)\s*/', '', $ac->getCoin()->getTitle()),
                'minYear' => $ac->getCoin()->getMinYear(),
                'maxYear' => $ac->getCoin()->getMaxYear(),
                'obverse' => $ac->getCoin()->getObverse(),
                'reverse' => $ac->getCoin()->getReverse(),
                'year' => $ac->getYear(),
                'quantity' => $ac->getQuantity(),
                'condition' => $ac->getCondition(),
                'category' => $ac->getCoin()->getCategory(),
            ];
        }

        return $this->json($result);
    }


    /**
     * @Route("/album/me/{id}", name="album_get_id", methods={"GET"})
     */
    public function getCoinUser(int $id): JsonResponse
    {
        $userJwt = $this->security->getUser();
        if (!$userJwt) {
            return $this->json(['error' => 'Usuário não autenticado'], 401);
        }

        // Busca o usuário real no banco
        $user = $this->em->getRepository(User::class)
            ->findOneBy(['email' => $userJwt->getUserIdentifier()]);
        if (!$user) {
            return $this->json(['error' => 'Usuário não encontrado'], 404);
        }

        // Busca o álbum do usuário
        $album = $this->em->getRepository(Album::class)->findOneBy(['user' => $user]);
        if (!$album) {
            return $this->json([]);
        }

        // Busca apenas as moedas do álbum que correspondem ao coinId da rota
        $albumCoins = $this->em->getRepository(AlbumCoin::class)->findBy([
            'album' => $album,
            'coin'  => $id
        ]);

        $result = [];
        foreach ($albumCoins as $ac) {
            $result[] = [
                'coinId'    => $ac->getCoin()->getId(),
                'year'      => $ac->getYear(),
                'quantity'  => $ac->getQuantity(),
                'condition' => $ac->getCondition(),
                'category'  => $ac->getCoin()->getCategory(),
            ];
        }

        return $this->json($result);
    }

    /**
     * @Route("/album/remove", name="album_remove", methods={"POST"})
     */
    public function removeCoin(Request $request, EntityManagerInterface $em, Security $security): JsonResponse
    {
        $userJwt = $security->getUser();
        if (!$userJwt) {
            return $this->json(['error' => 'Usuário não autenticado'], 401);
        }

        $data = json_decode($request->getContent(), true);

        $coinId = $data['coinId'] ?? null;
        $year = $data['year'] ?? null;
        $condition = $data['condition'] ?? null;
        $quantityToRemove = (int) ($data['quantity'] ?? 0);

        if (!$coinId || !$year || !$condition || $quantityToRemove <= 0) {
            return $this->json(['error' => 'Dados inválidos'], 400);
        }

        // Busca usuário real no banco
        $user = $em->getRepository(User::class)->findOneBy(['email' => $userJwt->getUserIdentifier()]);
        if (!$user) {
            return $this->json(['error' => 'Usuário não encontrado'], 404);
        }

        // Busca álbum do usuário
        $album = $em->getRepository(Album::class)->findOneBy(['user' => $user]);
        if (!$album) {
            return $this->json(['error' => 'Álbum não encontrado'], 404);
        }

        // Busca o AlbumCoin correspondente
        $albumCoin = $em->getRepository(AlbumCoin::class)->findOneBy([
            'album' => $album,
            'coin' => $coinId,
            'year' => $year,
            'condition' => $condition,
        ]);

        if (!$albumCoin) {
            return $this->json(['error' => 'Moeda não encontrada no álbum'], 404);
        }

        if ($albumCoin->getQuantity() < $quantityToRemove) {
            return $this->json(['error' => 'Quantidade maior do que disponível'], 400);
        }

        // Reduz a quantidade ou remove
        $albumCoin->setQuantity($albumCoin->getQuantity() - $quantityToRemove);

        if ($albumCoin->getQuantity() <= 0) {
            $em->remove($albumCoin);
        } else {
            $em->persist($albumCoin);
        }

        $em->flush();

        return $this->json([
            'success' => true,
            'removed' => $quantityToRemove,
            'remaining' => $albumCoin->getQuantity() ?? 0
        ]);
    }
}
