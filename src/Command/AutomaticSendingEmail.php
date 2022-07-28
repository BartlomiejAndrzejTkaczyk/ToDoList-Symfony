<?php

namespace App\Command;

use App\Entity\DTOEntity\TaskForEmail;
use App\Query\DbalTaskQuery;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

#[AsCommand(name: 'app:sendEmail')]
class AutomaticSendingEmail extends Command
{

    protected static $defaultName = 'app:sendEmail';


    public function __construct(
        private readonly DbalTaskQuery $taskQuery
    )
    {
        parent::__construct(self::$defaultName);
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var TaskForEmail $item */
        foreach ($this->taskQuery->getCommingTaskWithUser() as $item) {
            $mailText = '';
            foreach ($item->getTasksName() as $task) {
                $mailText .=  $task;
            }
            $mailText .= "\n";
            $this->sendMail($item->getEmail(), $mailText);
        }
        return Command::SUCCESS;
    }



    private function sendMail(string $mail, string $task)
    {
        $transport = Transport::fromDsn($_ENV['MAILER_DSN']);

        $email = (new Email())
            ->from('support@example.com')
            ->to($mail)
            ->subject('Task in next 3 days')
            ->text($task);

        $mailer = new Mailer($transport);

        $mailer->send($email);
    }

    protected function configure(): void
    {
        $this
            // the command help shown when running the command with the "--help" option
            ->setHelp('This command allows you to create a user...');
    }
}