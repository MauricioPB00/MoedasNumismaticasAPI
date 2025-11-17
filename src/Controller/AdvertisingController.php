<?php

namespace App\Controller;

use App\Entity\Advertising;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;
use App\Repository\AdvertisingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdvertisingController extends AbstractController
{
    private $em;
    private $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    /**
     * @Route("/advertising", name="advertising_save", methods={"POST"})
     */
    public function saveAdvertising(
        Request $request,
        EntityManagerInterface $em,
        AdvertisingRepository $advRepo
    ): JsonResponse {

        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Usuário não autenticado'], 401);
        }

        $existing = $advRepo->findOneBy(['user' => $user]);
        if ($existing) {
            return new JsonResponse(['error' => 'Você já possui um banner cadastrado'], 400);
        }

        $url = $request->request->get('url');
        /** @var UploadedFile $file */
        $file = $request->files->get('image');

        if (!$file) {
            return new JsonResponse(['error' => 'Nenhuma imagem enviada'], 400);
        }

        $allowedExtensions = ['png', 'jpg', 'jpeg'];
        $ext = strtolower($file->getClientOriginalExtension());

        if (!in_array($ext, $allowedExtensions)) {
            return new JsonResponse(['error' => 'A imagem deve ser PNG ou JPG'], 400);
        }

        if ($file->getSize() > 500 * 1024) {
            return new JsonResponse(['error' => 'A imagem deve ter no máximo 500KB'], 400);
        }

        [$width, $height] = getimagesize($file->getPathname());

        if ($width < 1000 || $width > 3000 || $height < 100 || $height > 3000) {
            return new JsonResponse([
                'error' => 'A imagem deve ter entre 1000px e 3000px de largura'
            ], 400);
        }

        $ratio = $width / $height;
        if ($ratio < 3.5 || $ratio > 4.5) {
            return new JsonResponse([
                'error' => 'A imagem deve ter proporção aproximada de 4:1'
            ], 400);
        }

        $uploadDir = 'C:/Users/Usuario/Documents/Moedas/Moedas/src/assets/img/anuncio';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $userId = $user->getId();
        $fileName = $userId . '_advertising.' . $ext;

        $fullPath = $uploadDir . '/' . $fileName;

        $targetW = 1200;
        $targetH = 300;

        if ($ext === 'png') {
            $source = imagecreatefrompng($file->getPathname());
        } else {
            $source = imagecreatefromjpeg($file->getPathname());
        }

        $dest = imagecreatetruecolor($targetW, $targetH);
        imagecopyresampled($dest, $source, 0, 0, 0, 0, $targetW, $targetH, $width, $height);

        if ($ext === 'png') {
            imagepng($dest, $fullPath);
        } else {
            imagejpeg($dest, $fullPath, 90);
        }

        imagedestroy($source);
        imagedestroy($dest);

        $adv = new Advertising();
        $adv->setUser($user);
        $adv->setUrl($url);
        $adv->setApproved(1);
        $adv->setAdvertisingImg($fileName);
        $adv->setCreatedAt(new \DateTimeImmutable());

        $em->persist($adv);
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Banner cadastrado com sucesso!',
            'preview' => $adv->getAdvertisingImg()
        ]);
    }

    /**
     * @Route("/advertising/buscar", name="advertising_get", methods={"GET"})
     */
    public function getAdvertising(): JsonResponse
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

        $adv = $this->em->getRepository(Advertising::class)
            ->findOneBy(['user' => $user]);

        if (!$adv) {
            return new JsonResponse(['empty' => true]);
        }

        return new JsonResponse([
            'url'   => $adv->getUrl(),
            'image' => $adv->getAdvertisingImg()
        ]);
    }

    /**
     * @Route("/advertising/delete", name="advertising_delete", methods={"POST"})
     */
    public function deleteAdvertising(
        AdvertisingRepository $advRepo,
        EntityManagerInterface $em
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Usuário não autenticado'], 401);
        }

        $adv = $advRepo->findOneBy(['user' => $user]);

        if (!$adv) {
            return new JsonResponse(['error' => 'Nenhum banner encontrado'], 400);
        }

        $em->remove($adv);
        $em->flush();

        return new JsonResponse([
            'message' => 'Banner removido com sucesso'
        ]);
    }


    /**
     * @Route("/advertising/pendentes", name="advertising_pending", methods={"GET"})
     */
    public function getPendingAds(
        AdvertisingRepository $advRepo,
        Security $security,
        EntityManagerInterface $em
    ): JsonResponse {

        $userJwt = $security->getUser();
        if (!$userJwt) {
            return $this->json(['error' => 'Usuário não autenticado'], 401);
        }

        $user = $em->getRepository(User::class)
            ->findOneBy(['email' => $userJwt->getUserIdentifier()]);

        if (!$user) {
            return $this->json(['error' => 'Usuário não encontrado'], 404);
        }

        $ads = $advRepo->findBy(['approved' => 1]);

        $result = [];
        foreach ($ads as $a) {
            $result[] = [
                'id'   => $a->getId(),
                'url'  => $a->getUrl(),
                'advertisingImg' => $a->getAdvertisingImg(),
                'user' => [
                    'id'    => $a->getUser()->getId(),
                    'name'  => $a->getUser()->getName(),
                    'email' => $a->getUser()->getEmail(),
                ]
            ];
        }

        return $this->json($result);
    }

    /**
     * @Route("/advertising/aprovar/{id}", name="advertising_approve", methods={"POST"})
     */
    public function approveAdvertising(
        int $id,
        AdvertisingRepository $advRepo,
        EntityManagerInterface $em,
        Security $security
    ): JsonResponse {

        $userJwt = $security->getUser();
        if (!$userJwt) {
            return $this->json(['error' => 'Usuário não autenticado'], 401);
        }

        $user = $em->getRepository(User::class)
            ->findOneBy(['email' => $userJwt->getUserIdentifier()]);

        if (!$user) {
            return $this->json(['error' => 'Usuário não encontrado'], 404);
        }

        $adv = $advRepo->find($id);
        if (!$adv) {
            return $this->json(['error' => 'Banner não encontrado'], 404);
        }

        $adv->setApproved(2);
        $em->flush();

        return $this->json([
            'message' => 'Banner aprovado com sucesso!'
        ]);
    }
}
