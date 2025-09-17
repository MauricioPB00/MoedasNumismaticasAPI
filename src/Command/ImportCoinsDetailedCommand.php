<?php

namespace App\Command;

use App\Entity\Coins;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// php bin/console app:import-coins-detalhes

class ImportCoinsDetailedCommand extends Command
{
    protected static $defaultName = 'app:import-coins-detailed';
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setDescription('Importa moedas detalhadas do arquivo JSON para o banco de dados');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $jsonFile = __DIR__ . '/../../public/json/moedas_detalhes.json';

        if (!file_exists($jsonFile)) {
            $output->writeln('<error>Arquivo moedas_detalhes.json não encontrado!</error>');
            return Command::FAILURE;
        }

        $data = json_decode(file_get_contents($jsonFile), true);

        foreach ($data as $item) {
            $existingCoin = $this->em->getRepository(Coins::class)->find($item['id']);
            if ($existingCoin) {
                continue; // já existe
            }

            $coin = new Coins();
            $coin->setId($item['id']);
            $coin->setTitle($item['title'] ?? 'Sem título');
            $coin->setCategory($item['category'] ?? 'Desconhecida');
            $coin->setIssuer('Brasil');
            $coin->setMinYear($item['min_year'] ?? null);
            $coin->setMaxYear($item['max_year'] ?? null);
            $coin->setValueFullName($item['value']['full_name'] ?? null);
            $coin->setRulerName($item['ruler'][0]['name'] ?? null);
            $coin->setTechnique($item['technique']['text'] ?? null);
            $coin->setObverseDescription($item['obverse']['description'] ?? null);
            $coin->setReverseDescription($item['reverse']['description'] ?? null);
            $coin->setMints(isset($item['mints']) ? array_column($item['mints'], 'name') : null);
            $coin->setWeight($item['weight'] ?? null);
            $coin->setSize($item['size'] ?? null);
            $coin->setThickness($item['thickness'] ?? null);
            $coin->setShape($item['shape'] ?? null);
            $coin->setCompositionText($item['composition']['text'] ?? null);
            $coin->setEdge($item['edge']['description'] ?? null);
            $coin->setDemonetizationDate(isset($item['demonetization']['demonetization_date']) ? new \DateTime($item['demonetization']['demonetization_date']) : null);
            $coin->setObverse($item['id'] . '_obverse.jpg');
            $coin->setReverse($item['id'] . '_reverse.jpg');

            $this->em->persist($coin);
        }

        $this->em->flush();
        $output->writeln('<info>Importação detalhada concluída com sucesso!</info>');

        return Command::SUCCESS;
    }
}
