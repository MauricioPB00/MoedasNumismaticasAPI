<?php

namespace App\Command;

use App\Entity\Coin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


// php bin/console app:import-coins

// Salva no banco o moedas.json
// Salva no banco o caminho das fotos

class ImportCoinsCommand extends Command
{
    protected static $defaultName = 'app:import-coins';
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setDescription('Importa moedas do arquivo JSON para o banco de dados');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $jsonFile = __DIR__ . '/../../public/json/moedas.json';

        if (!file_exists($jsonFile)) {
            $output->writeln('<error>Arquivo moedas.json não encontrado!</error>');
            return Command::FAILURE;
        }

        $data = json_decode(file_get_contents($jsonFile), true);

        foreach ($data as $item) {

            $existingCoin = $this->em->getRepository(Coin::class)->find($item['id']);
            if ($existingCoin) {
                continue;
            }

            $coin = new Coin();
            $coin->setId($item['id']);
            $coin->setTitle($item['title'] ?? 'Sem título');
            $coin->setCategory($item['category'] ?? 'Desconhecida');
            $coin->setIssuer('Brasil');
            $coin->setMinYear($item['min_year'] ?? null);
            $coin->setMaxYear($item['max_year'] ?? null);
            $coin->setObverse(isset($item['obverse_thumbnail']) ? $item['id'] . '_obverse.png' : 'SemFoto.png');
            $coin->setReverse(isset($item['reverse_thumbnail']) ? $item['id'] . '_reverse.png' : 'SemFoto.png');

            $this->em->persist($coin);
        }

        $this->em->flush();

        $output->writeln('<info>Importação concluída com sucesso!</info>');

        return Command::SUCCESS;
    }
}
