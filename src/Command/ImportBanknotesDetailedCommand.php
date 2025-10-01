<?php

namespace App\Command;

use App\Entity\Banknotes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// php bin/console app:import-banknotes-detailed

class ImportBanknotesDetailedCommand extends Command
{
    protected static $defaultName = 'app:import-banknotes-detailed';
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this->setDescription('Importa cédulas detalhadas do arquivo JSON para o banco de dados');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $jsonFile = __DIR__ . '/../../public/json/cedulas/cedulas_detalhadas.json';

        if (!file_exists($jsonFile)) {
            $output->writeln('<error>Arquivo cedulas.json não encontrado!</error>');
            return Command::FAILURE;
        }

        $data = json_decode(file_get_contents($jsonFile), true);
        if (!is_array($data)) {
            $output->writeln('<error>Erro ao ler o arquivo JSON!</error>');
            return Command::FAILURE;
        }

        $output->writeln('<info>Iniciando importação de cédulas...</info>');
        $count = 0;

        foreach ($data as $item) {
            $existingNote = $this->em->getRepository(Banknotes::class)->find($item['id']);
            if ($existingNote) {
                continue; // já existe
            }

            $note = new Banknotes();
            $note->setId($item['id']);
            $note->setTitle($item['title'] ?? 'Sem título');
            $note->setCategory($item['category'] ?? 'Desconhecida');
            $note->setUrl($item['url'] ?? null);
            $note->setIssuer($item['issuer'] ?? []);
            $note->setIssuingEntity($item['issuing_entity'] ?? []);
            $note->setMinYear($item['min_year'] ?? null);
            $note->setMaxYear($item['max_year'] ?? null);
            $note->setType($item['type'] ?? null);
            $note->setRuler($item['ruler'] ?? []);
            $note->setValueText($item['value']['text'] ?? null);
            $note->setValueNumeric($item['value']['numeric_value'] ?? null);
            $note->setCurrency($item['value']['currency'] ?? []);
            $note->setIsDemonetized($item['demonetization']['is_demonetized'] ?? false);

            if (!empty($item['demonetization']['demonetization_date'])) {
                try {
                    $note->setDemonetizationDate(new \DateTime($item['demonetization']['demonetization_date']));
                } catch (\Exception $e) {
                    $note->setDemonetizationDate(null);
                }
            }

            $note->setSize($item['size'] ?? null);
            $note->setSize2($item['size2'] ?? null);
            $note->setShape($item['shape'] ?? null);
            $note->setComposition($item['composition'] ?? []);
            $note->setObverse($item['obverse'] ?? []);
            $note->setReverse($item['reverse'] ?? []);
            $note->setSeries($item['series'] ?? null);
            $note->setComments($item['comments'] ?? null);
            $note->setRelatedTypes($item['related_types'] ?? []);
            $note->setTags($item['tags'] ?? []);
            $note->setReferenceCode($item['references'] ?? []);
            $note->setPrinters($item['printers'] ?? []);

            $note->setObverseImg(!empty($item['obverse']['picture']) ? $item['id'] . '_obverse.jpg' : null);
            $note->setReverseImg(!empty($item['reverse']['picture']) ? $item['id'] . '_reverse.jpg' : null);

            $this->em->persist($note);
            $count++;

            if ($count % 50 === 0) {
                $this->em->flush();
                $this->em->clear();
                $output->writeln("→ {$count} cédulas importadas...");
            }
        }

        $this->em->flush();
        $output->writeln("<info>✅ Importação de cédulas concluída! Total: {$count}</info>");

        return Command::SUCCESS;
    }
}
