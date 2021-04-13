<?php


namespace App\Command;


use App\Repository\TaskRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerBackupCommand extends Command
{
    // CLI command
    protected static $defaultName = 'app:mailer:backup';
    /**
     * @var TaskRepository
     */
    private TaskRepository $taskRepository;
    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    public function __construct(TaskRepository $taskRepository, MailerInterface $mailer)
    {
        $this->taskRepository = $taskRepository;
        $this->mailer = $mailer;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('Send email with data')
            ->setHelp('This command allows you to send email backup data')
        ;


        parent::configure(); // TODO: Change the autogenerated stub
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
           'Email sent',
           '=========='.
           '',
0        ]);

        $email = (new Email())
            ->from('alienBackup@example.com')
            ->to('domi@domi.eu')
            ->subject('Your data backup ')
            ->html('<p>See Twig integration for better HTML integration!</p>')
            ;
        $this->mailer->send($email);

        return Command::SUCCESS;
        //parent::execute($input, $output); // TODO: Change the autogenerated stub
    }


}