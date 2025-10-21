<?php

namespace App\Command;

use App\Entity\Coins;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// php bin/console app:import-coins-detailed

// 02

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
        $jsonFile = __DIR__ . '/../../public/json/moedas_detalhadas.json';

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
            $coin->setUrl($item['url'] ?? null);
            $coin->setType($item['type'] ?? null);
            $coin->setIssuer($item['issuer'] ?? null);
            $coin->setMinYear($item['min_year'] ?? null);
            $coin->setMaxYear($item['max_year'] ?? null);
            $coin->setRuler($item['ruler'] ?? null);
            $coin->setValueText($item['value']['text'] ?? null);
            $coin->setValueNumeric($item['value']['numeric_value'] ?? null);
            $coin->setCurrencyName($item['value']['currency']['name'] ?? null);
            $coin->setIsDemonetized($item['demonetization']['is_demonetized'] ?? null);
            $coin->setSize($item['size'] ?? null);
            $coin->setThickness($item['thickness'] ?? null);
            $coin->setShape($item['shape'] ?? null);
            $coin->setWeight($item['weight'] ?? null);
            $coin->setOrientation($item['orientation'] ?? null);
            $coin->setComposition($item['composition'] ?? null);
            $coin->setTechnique($item['technique'] ?? null);
            $coin->setObverse($item['obverse'] ?? null);
            $coin->setReverse($item['reverse'] ?? null);
            $coin->setEdge($item['edge'] ?? null);
            $coin->setComments($item['comments'] ?? null);
            $coin->setRelatedTypes($item['related_types'] ?? null);
            $coin->setTags($item['tags'] ?? null);
            $coin->setReferenceCode ($item['references'] ?? null);
            $coin->setMints($item['mints'] ?? null);
            $coin->setCoinGroup($item['group'] ?? null);
            $coin->setCurrency($item['currency'] ?? null);
            $coin->setDemonetizationDate(isset($item['demonetization']['demonetization_date']) ? new \DateTime($item['demonetization']['demonetization_date']) : null);
            $coin->setObverseImg(isset($item['obverse']) ? $item['id'] . '_obverse.jpg' : null);
            $coin->setReverseImg(isset($item['reverse']) ? $item['id'] . '_reverse.jpg' : null);
            $coin->setEdgeImg(isset($item['edge']) ? $item['id'] . '_edge.jpg' : null);

            $this->em->persist($coin);
        }

        $this->em->flush();
        $output->writeln('<info>Importação detalhada concluída com sucesso!</info>');

        return Command::SUCCESS;
    }
}
