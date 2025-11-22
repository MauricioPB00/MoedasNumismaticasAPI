<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;

class RecognizeController extends AbstractController
{
    /**
     * @Route("/recognize/coin", name="recognize_coin", methods={"POST"})
     */
    public function recognizeCoin(
        Request $request,
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

        $data = json_decode($request->getContent(), true);
        if (!$data || !isset($data['image'])) {
            return $this->json(['error' => 'Imagem não enviada'], 400);
        }

        $imageBase64 = $data['image'];

        $pythonResponse = $this->callPythonAI($imageBase64);

        if ($pythonResponse === null) {
            return $this->json(['error' => 'Falha ao executar Python'], 500);
        }

        return $this->json([
            'status' => 'success',
            'result' => $pythonResponse
        ]);
    }

    private function callPythonAI(string $imageBase64): ?array
    {
        $script = realpath(__DIR__ . '/../../../MoedasPY/recognize_coin.py');

        $python = realpath(__DIR__ . '/../../../MoedasPY/venv/Scripts/python.exe');

        if (!$script || !file_exists($script)) {
            return ['error' => 'Python script não encontrado', 'path' => $script];
        }

        if (!$python || !file_exists($python)) {
            return ['error' => 'Python não encontrado no venv', 'path' => $python];
        }

        $tmpFile = tempnam(sys_get_temp_dir(), 'coin_') . ".jpg";

        $base64 = preg_replace('#^data:image/\w+;base64,#i', '', $imageBase64);
        file_put_contents($tmpFile, base64_decode($base64));

        $cmd = "set PATH=\"".dirname($python).";%PATH%\" && \"$python\" \"$script\" \"$tmpFile\" 2>&1";

        $output = [];
        $returnCode = 0;

        exec($cmd, $output, $returnCode);

        unlink($tmpFile);

        if ($returnCode !== 0) {
            return [
                'error' => 'Falha ao executar Python',
                'cmd' => $cmd,
                'output' => $output,
                'returnCode' => $returnCode
            ];
        }

        return json_decode(implode("\n", $output), true);
    }
}
