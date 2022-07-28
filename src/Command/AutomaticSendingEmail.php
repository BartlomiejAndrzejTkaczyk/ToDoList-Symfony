<?php

namespace App\Command;

use App\Entity\DTOEntity\TasksForEmail;
use App\Query\DbalTaskQuery;
use PHPUnit\Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

#[AsCommand(name: 'app:sendEmail')]
class AutomaticSendingEmail extends Command
{

    protected static $defaultName = 'app:sendEmail';


    public function __construct(
        private readonly DbalTaskQuery   $taskQuery,
        private readonly MailerInterface $mailer
    )
    {
        parent::__construct(self::$defaultName);
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var TasksForEmail $item */
        foreach ($this->taskQuery->getComingTaskWithUser() as $item) {
            $mailText = '';
            $nr = 1;
            foreach ($item->getTasksName() as $task) {
                $mailText .= $nr . ') ' . $task . "\n";
                $nr++;
            }
            $this->sendMail($item->getEmail(), $mailText);
        }
        return Command::SUCCESS;
    }


    private function sendMail(string $mail, string $task)
    {
        $email = (new Email())
            ->from('support@example.com')
            ->to($mail)
            ->subject('Task in next 3 days')
            ->text($task);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            dd($e);
        }
    }

    protected function configure(): void
    {
        $this
            // the command help shown when running the command with the "--help" option
            ->setHelp('This command allows you to create a user...');
    }
}