<?php

namespace App\Controller;

use App\Entity\AlbumCoin;
use App\Entity\Banknote;
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
            return $this->json(['error' => 'coinId obrigatório'], 400);
        }

        $album = $user->getAlbum();
        if (!$album) {
            $album = new Album();
            $album->setUser($user);
            $this->em->persist($album);
        }

        $coin = $this->em->getRepository(Coin::class)->find($coinId);
        $banknote = null;
        $type = 'coin';

        if (!$coin) {
            $banknote = $this->em->getRepository(Banknote::class)->find($coinId);
            $type = 'banknote';
        }

        if (!$coin && !$banknote) {
            return $this->json(['error' => 'Moeda ou cédula não encontrada'], 404);
        }

        $criteria = [
            'album' => $album,
            'year' => $year,
            'condition' => $condition,
        ];

        if ($coin) {
            $criteria['coin'] = $coin;
        } else {
            $criteria['banknote'] = $banknote;
        }

        $albumCoinRepo = $this->em->getRepository(AlbumCoin::class);
        $existing = $albumCoinRepo->findOneBy($criteria);

        if ($existing) {
            $existing->setQuantity($existing->getQuantity() + $quantity);
            $this->em->flush();

            return $this->json(['success' => 'Quantidade atualizada com sucesso']);
        }

        $albumCoin = new AlbumCoin();
        $albumCoin->setAlbum($album)
            ->setYear($year)
            ->setQuantity($quantity)
            ->setCondition($condition);

        if ($coin) {
            $albumCoin->setCoin($coin);
        } else {
            $albumCoin->setBanknote($banknote);
        }

        $this->em->persist($albumCoin);
        $this->em->flush();

        return $this->json([
            'success' => ucfirst($type) . ' adicionada ao álbum com sucesso',
            'type' => $type,
            'id' => $coinId
        ]);
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

        $user = $this->em->getRepository(User::class)
            ->findOneBy(['email' => $userJwt->getUserIdentifier()]);
        if (!$user) {
            return $this->json(['error' => 'Usuário não encontrado'], 404);
        }

        $album = $this->em->getRepository(Album::class)->findOneBy(['user' => $user]);
        if (!$album) {
            return $this->json([]);
        }

        $albumItems = $this->em->getRepository(AlbumCoin::class)->findBy(['album' => $album]);

        $result = [];
        foreach ($albumItems as $item) {
            $coin = $item->getCoin();
            $banknote = $item->getBanknote();

            if ($coin) {
                $result[] = [
                    'type' => 'coin',
                    'id' => $coin->getId(),
                    'title' => preg_replace('/\s*\(.*?\)\s*/', '', $coin->getTitle()),
                    'minYear' => $coin->getMinYear(),
                    'maxYear' => $coin->getMaxYear(),
                    'obverse' => $coin->getObverse(),
                    'reverse' => $coin->getReverse(),
                    'year' => $item->getYear(),
                    'quantity' => $item->getQuantity(),
                    'condition' => $item->getCondition(),
                    'category' => $coin->getCategory(),
                ];
            } elseif ($banknote) {
                $result[] = [
                    'type' => 'banknote',
                    'id' => $banknote->getId(),
                    'title' => preg_replace('/\s*\(.*?\)\s*/', '', $banknote->getTitle()),
                    'minYear' => $banknote->getMinYear(),
                    'maxYear' => $banknote->getMaxYear(),
                    'obverse' => method_exists($banknote, 'getObverse') ? $banknote->getObverse() : null,
                    'reverse' => method_exists($banknote, 'getReverse') ? $banknote->getReverse() : null,
                    'year' => $item->getYear(),
                    'quantity' => $item->getQuantity(),
                    'condition' => $item->getCondition(),
                    'category' => $banknote->getCategory(),
                ];
            }
        }

        return $this->json($result);
    }

    /**
     * @Route("/album/me/map", name="album_get_me_map", methods={"GET"})
     */
    public function getAlbumUserMap(): JsonResponse
    {
        $userJwt = $this->security->getUser();
        if (!$userJwt) {
            return $this->json(['error' => 'Usuário não autenticado'], 401);
        }

        $user = $this->em->getRepository(User::class)
            ->findOneBy(['email' => $userJwt->getUserIdentifier()]);
        if (!$user) {
            return $this->json(['error' => 'Usuário não encontrado'], 404);
        }

        $album = $this->em->getRepository(Album::class)->findOneBy(['user' => $user]);
        if (!$album) {
            return $this->json([]);
        }

        $albumItems = $this->em->getRepository(AlbumCoin::class)->findBy(['album' => $album]);

        $result = [];
        foreach ($albumItems as $item) {
            $coin = $item->getCoin();
            $banknote = $item->getBanknote();

            if ($coin) {
                $result[] = [
                    'type' => 'coin',
                    'id' => $coin->getId(),
                    'quantity' => $item->getQuantity(),
                    'category' => $coin->getCategory(),
                    'issuer' => $coin->getIssuer(),
                ];
            } elseif ($banknote) {
                $result[] = [
                    'type' => 'banknote',
                    'id' => $banknote->getId(),
                    'quantity' => $item->getQuantity(),
                    'category' => $banknote->getCategory(),
                    'issuer' => $banknote->getIssuer(),
                ];
            }
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

        $user = $this->em->getRepository(User::class)
            ->findOneBy(['email' => $userJwt->getUserIdentifier()]);
        if (!$user) {
            return $this->json(['error' => 'Usuário não encontrado'], 404);
        }

        $album = $this->em->getRepository(Album::class)->findOneBy(['user' => $user]);
        if (!$album) {
            return $this->json([]);
        }

        $albumItems = $this->em->getRepository(AlbumCoin::class)->findBy(['album' => $album]);

        $result = [];
        foreach ($albumItems as $item) {
            $coin = $item->getCoin();
            $banknote = $item->getBanknote();

            if ($coin && $coin->getId() === $id) {
                $result[] = [
                    'type' => 'coin',
                    'id' => $coin->getId(),
                    'title' => preg_replace('/\s*\(.*?\)\s*/', '', $coin->getTitle()),
                    'minYear' => $coin->getMinYear(),
                    'maxYear' => $coin->getMaxYear(),
                    'obverse' => $coin->getObverse(),
                    'reverse' => $coin->getReverse(),
                    'year' => $item->getYear(),
                    'quantity' => $item->getQuantity(),
                    'condition' => $item->getCondition(),
                    'category' => $coin->getCategory(),
                ];
            } elseif ($banknote && $banknote->getId() === $id) {
                $result[] = [
                    'type' => 'banknote',
                    'id' => $banknote->getId(),
                    'title' => preg_replace('/\s*\(.*?\)\s*/', '', $banknote->getTitle()),
                    'minYear' => $banknote->getMinYear(),
                    'maxYear' => $banknote->getMaxYear(),
                    'obverse' => method_exists($banknote, 'getObverse') ? $banknote->getObverse() : null,
                    'reverse' => method_exists($banknote, 'getReverse') ? $banknote->getReverse() : null,
                    'year' => $item->getYear(),
                    'quantity' => $item->getQuantity(),
                    'condition' => $item->getCondition(),
                    'category' => $banknote->getCategory(),
                ];
            }
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

        $itemId = $data['coinId'] ?? null;
        $year = $data['year'] ?? null;
        $condition = $data['condition'] ?? null;
        $quantityToRemove = (int) ($data['quantity'] ?? 0);

        if (!$itemId || !$year || $quantityToRemove <= 0) {
            return $this->json(['error' => 'Dados inválidos'], 400);
        }

        $user = $em->getRepository(User::class)->findOneBy(['email' => $userJwt->getUserIdentifier()]);
        if (!$user) {
            return $this->json(['error' => 'Usuário não encontrado'], 404);
        }

        $album = $em->getRepository(Album::class)->findOneBy(['user' => $user]);
        if (!$album) {
            return $this->json(['error' => 'Álbum não encontrado'], 404);
        }

        $coin = $em->getRepository(Coin::class)->find($itemId);
        $banknote = null;

        if (!$coin) {
            $banknote = $em->getRepository(Banknote::class)->find($itemId);
            if (!$banknote) {
                return $this->json(['error' => 'Item não encontrado (nem moeda nem cédula)'], 404);
            }
        }

        $qb = $em->getRepository(AlbumCoin::class)->createQueryBuilder('ac')
            ->where('ac.album = :album')
            ->andWhere('ac.year = :year')
            ->setParameter('album', $album)
            ->setParameter('year', $year);

        if ($coin) {
            $qb->andWhere('ac.coin = :coin')->setParameter('coin', $coin);
        } else {
            $qb->andWhere('ac.banknote = :banknote')->setParameter('banknote', $banknote);
        }

        if ($condition === null || $condition === '') {
            $qb->andWhere('ac.condition IS NULL');
        } else {
            $qb->andWhere('ac.condition = :condition')->setParameter('condition', $condition);
        }

        $albumItem = $qb->getQuery()->getOneOrNullResult();

        if (!$albumItem) {
            return $this->json(['error' => 'Item não encontrado no álbum'], 404);
        }

        if ($albumItem->getQuantity() < $quantityToRemove) {
            return $this->json(['error' => 'Quantidade maior do que disponível'], 400);
        }

        $novaQuantidade = $albumItem->getQuantity() - $quantityToRemove;
        if ($novaQuantidade <= 0) {
            $em->remove($albumItem);
        } else {
            $albumItem->setQuantity($novaQuantidade);
            $em->persist($albumItem);
        }

        $em->flush();

        return $this->json([
            'success' => true,
            'removed' => $quantityToRemove,
            'remaining' => $novaQuantidade > 0 ? $novaQuantidade : 0,
            'type' => $coin ? 'coin' : 'banknote'
        ]);
    }
}
