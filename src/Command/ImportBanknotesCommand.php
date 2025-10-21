<?php

namespace App\Command;

use App\Entity\Banknote;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


// php bin/console app:import-banknote

// 01

// MUDAR O PAIS 
// TA FIXO NA LINHA 59

// Salva no banco o moedas.json
// Salva no banco o caminho das fotos

class ImportBanknotesCommand extends Command
{
    protected static $defaultName = 'app:import-banknote';
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
        $jsonFile = __DIR__ . '/../../public/json/cedulas.json';

        if (!file_exists($jsonFile)) {
            $output->writeln('<error>Arquivo moedas.json não encontrado!</error>');
            return Command::FAILURE;
        }

        $data = json_decode(file_get_contents($jsonFile), true);

        foreach ($data as $item) {

            $existingCoin = $this->em->getRepository(Banknote::class)->find($item['id']);
            if ($existingCoin) {
                continue;
            }

            $coin = new Banknote();
            $coin->setId($item['id']);
            $coin->setTitle($item['title'] ?? 'Sem título');
            $coin->setCategory($item['category'] ?? 'Desconhecida');
            $coin->setIssuer('Uruguai');
            $coin->setMinYear($item['min_year'] ?? null);
            $coin->setMaxYear($item['max_year'] ?? null);
            $coin->setObverse(isset($item['obverse_thumbnail']) ? $item['id'] . '_obverse.jpg' : 'SemFoto.png');
            $coin->setReverse(isset($item['reverse_thumbnail']) ? $item['id'] . '_reverse.jpg' : 'SemFoto.png');

            $this->em->persist($coin);
        }

        $this->em->flush();

        $output->writeln('<info>Importação concluída com sucesso!</info>');

        return Command::SUCCESS;
    }
}
