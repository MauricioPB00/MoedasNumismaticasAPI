<?php

namespace App\Command;

use App\Entity\BanknoteInfo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

# primeiro o import depois esse Update
# php bin/console app:update-banknoteInfo

class UpdateBanknoteInfoCommand extends Command
{
    protected static $defaultName = 'app:update-banknoteInfo';
    protected static $defaultDescription = 'Atualiza os dados de BanknoteInfo com base no arquivo cedulas_issues.json';

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $caminho = __DIR__ . '/../../public/json/cedulas/cedulas_issues.json';

        if (!file_exists($caminho)) {
            $output->writeln("<error>Arquivo não encontrado: $caminho</error>");
            return Command::FAILURE;
        }

        $json = file_get_contents($caminho);
        $dados = json_decode($json, true);

        if (!is_array($dados)) {
            $output->writeln("<error>Erro ao decodificar JSON</error>");
            return Command::FAILURE;
        }

        $total = count($dados);
        $output->writeln("<info>Iniciando atualização de $total registros...</info>");

        $count = 0;
        foreach ($dados as $item) {
            $typeId = $item['type_id'] ?? null;
            $issueId = $item['id'] ?? null;

            if (!$typeId || !$issueId) {
                continue;
            }

            $entity = $this->em->getRepository(BanknoteInfo::class)
                ->findOneBy([
                    'type_id' => $typeId,
                    'issue_id' => $issueId
                ]);

            if (!$entity) {
                continue;
            }

            if (isset($item['min_year'])) {
                $entity->setMinYear($item['min_year']);
            }
            if (isset($item['max_year'])) {
                $entity->setMaxYear($item['max_year']);
            }

            if (isset($item['min_year'])) {
                $entity->setYear($item['min_year']);
            }

            if (isset($item['mintage'])) {
                $entity->setMintage((int) round($item['mintage']));
            }

            if (isset($item['year'])) {
                $entity->setYear((int) round($item['year']));
            }

            # Rodar depois para atualizar os gregorian_year

            // if (isset($item['gregorian_year'])) {
            //     $entity->setYear((int) round($item['gregorian_year']));
            // }
            $this->em->persist($entity);

            if (++$count % 50 === 0) {
                $this->em->flush();
                $this->em->clear();
                $output->writeln("🔁 $count registros atualizados...");
            }
        }

        $this->em->flush();
        $output->writeln("<info>✅ Atualização concluída! Total de registros processados: $count</info>");

        return Command::SUCCESS;
    }
}
