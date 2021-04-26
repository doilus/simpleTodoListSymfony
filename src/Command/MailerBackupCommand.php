<?php


namespace App\Command;


use App\Email\SendEmail;
use App\Repository\TaskRepository;
use App\Services\Generator\WriteToCSVFile;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MailerBackupCommand extends Command
{
    // CLI command
    protected static $defaultName = 'app:mailer:backup';
    private SendEmail $sendEmail;
    private WriteToCSVFile $writeToCSVFile;


    public function __construct(
        SendEmail $sendEmail,
        WriteToCSVFile $writeToCSVFile)
    {
        $this->sendEmail = $sendEmail;
        $this->writeToCSVFile = $writeToCSVFile;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('Send email with data')
            ->setHelp('This command allows you to send email backup data');


        parent::configure(); // TODO: Change the autogenerated stub
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->writeToCSVFile->writeToCSV();

        $this->sendEmail->sendEmail(
            'alienBackup@example.com',
            'domi@domi.eu',
            'Your data backup ',
            'email/welcome.html.twig',
            "sample.csv"
        );

        $output->writeln([
            'Email sent',
            '==========' .
            '',
        ]);

        return Command::SUCCESS;
    }


}