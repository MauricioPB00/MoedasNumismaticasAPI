<?php

namespace App\Command;

use App\Entity\BanknoteInfo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#php bin/console app:import-banknoteInfo


// 03

# 1 import 2 Updade..
# sao 3 acoes

class ImportBanknoteInfoCommand extends Command
{
    protected static $defaultName = 'app:import-banknoteInfo';
    protected static $defaultDescription = 'Importa dados do arquivo cedulas_prices.json para o banco (tabela BanknoteInfo).';

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $caminho = __DIR__ . '/../../public/json/cedulas_prices.json';

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
        $output->writeln("<info>Iniciando importação de $total registros...</info>");

        $count = 0;
        foreach ($dados as $item) {
            $entity = new BanknoteInfo();

            $entity->setIssueId($item['issue_id'] ?? null);
            $entity->setTypeId($item['type_id'] ?? null);

            if (isset($item['prices'])) {
                $entity->setPrices($item['prices'] ?? []);
            }

            if (isset($item['year'])) {
                $entity->setYear($item['year']);
            }
            if (isset($item['min_year'])) {
                $entity->setMinYear($item['min_year']);
            }
            if (isset($item['max_year'])) {
                $entity->setMaxYear($item['max_year']);
            }
            if (isset($item['mintage'])) {
                $entity->setMintage($item['mintage']);
            }

            $this->em->persist($entity);

            if (++$count % 50 === 0) {
                $this->em->flush();
                $this->em->clear();
                $output->writeln("✅ $count registros salvos...");
            }
        }

        $this->em->flush();
        $output->writeln("<info>✅ Importação concluída! Total de registros salvos: $count</info>");

        return Command::SUCCESS;
    }
}
