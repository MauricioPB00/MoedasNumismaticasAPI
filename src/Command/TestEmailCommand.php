<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class TestEmailCommand extends Command
{
    protected static $defaultName = 'app:test-email';
    protected static $defaultDescription = 'Envia um e-mail de teste para validar configuração do Mailer';

    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        parent::__construct();
        $this->mailer = $mailer;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (new Email())
            ->from('SEU_EMAIL@gmail.com')
            ->to('mauricioperinbecker@gmail.com')
            ->subject('Assunto do Teste')
            ->text('Corpo do e-mail - teste do Symfony Mailer');

        $this->mailer->send($email);

        $output->writeln('✅ E-mail enviado com sucesso!');
        return Command::SUCCESS;
    }
}
