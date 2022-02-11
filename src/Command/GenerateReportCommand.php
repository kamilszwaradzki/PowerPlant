<?php
// src/Command/GenerateReportCommand.php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Mailer\MailerInterface;
use App\Document\Reading;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class GenerateReportCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:generate-report';
    protected static $defaultDescription = 'Creates a new report.';

    private $dm;
    private $mailer;

    public function __construct(DocumentManager $dm, MailerInterface $mailer) {
        $this->dm = $dm;
        $this->mailer = $mailer;
        parent::__construct();
    }

    protected function configure(): void
    {
        // ...
    }

    /**
     * This function return average reading's power per hour.
     *
     * @param array<App\Document\Reading> $readingsPerOneHour
     * @return float
     */
    private function calcAvgPowerPerHour($readingsPerOneHour) {
        $avg = 0;
        foreach ($readingsPerOneHour as $reading) {
            $avg += $reading->getPower();
        }
        $avg /= count($readingsPerOneHour);
        return $avg;
    }

    private function generateTestReport(DocumentManager $dm, MailerInterface $mailer)
    {
        /*
        * Proszę przygotować dodatkowo aby raz na dobę szedł raport
        * z informacją o średniej mocy wytworzonej przez każdy generator w ciągu godziny.
        * Czyli będzie 20 generatorów * 24 wyniki (bo tyle godzin w ciągu doby).
        * Uwaga: w raporcie wyniki podajemy w MW.
        */
        $date = '2021-10-02';
        $avgPowers = [];
        for ($i = 1; $i < 21; $i++) {
            $hour = 0;
            $readingsPerGenerator = $dm->getRepository(Reading::class)->findBy(['generator_id' => $i], ['time' => 'asc']);
            $countReadingsPerGenerator = count($readingsPerGenerator);
            for ( $j = 9; $j < $countReadingsPerGenerator; $j += 10) {
                $readingsPerHour = [];
                for ($k = $j; $k > $j - 10; $k--) {
                    array_push($readingsPerHour, $readingsPerGenerator[$k]);
                }
                $avgPowers[$i][$hour++] = $this->calcAvgPowerPerHour($readingsPerHour) / 1000; // convert KW to MW.
            }
        }
        $email = (new TemplatedEmail())
        ->from('fabien@example.com')
        ->to(new Address('ryan@example.com'))
        ->subject('Report for' . $date)
        ->htmlTemplate('emails/report.html.twig')
        ->context([
            'report' => $avgPowers,
            'date' => $date
        ])
    ;
        $mailer->send($email);
        print('Report generated successfully.');
    }

    private function generateReport(DocumentManager $dm, MailerInterface $mailer)
    {
        $date = date('d.m.Y',strtotime("-1 days"));
        $avgPowers = [];
        for ($i = 1; $i < 21; $i++) {
            $hour = 0;
            $readingsPerGenerator = $dm->getRepository(Reading::class)->findBy(['generator_id' => $i, 'date' => $date], ['time' => 'asc']);
            $countReadingsPerGenerator = count($readingsPerGenerator);
            for ( $j = 119; $j < $countReadingsPerGenerator; $j += 120) {
                $readingsPerHour = [];
                for ($k = $j; $k > $j - 120; $k--) {
                    array_push($readingsPerHour, $readingsPerGenerator[$k]);
                }
                $avgPowers[$i][$hour++] = $this->calcAvgPowerPerHour($readingsPerHour) / 1000; // convert KW to MW.
            }
        }
        $email = (new TemplatedEmail())
        ->from('fabien@example.com')
        ->to(new Address('ryan@example.com'))
        ->subject('Report for' . $date)
        ->htmlTemplate('emails/report.html.twig')
        ->context([
            'report' => $avgPowers,
            'date' => $date
        ]);
        $mailer->send($email);
        print('Report generated successfully for ' . $date . '.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dm = $this->dm;
        $mailer = $this->mailer;
        $this->generateReport($dm, $mailer);
        // this method must return an integer number with the "exit status code"
        // of the command. You can also use these constants to make code more readable

        // return this if there was no problem running the command
        // (it's equivalent to returning int(0))
        return Command::SUCCESS;

        // or return this if some error happened during the execution
        // (it's equivalent to returning int(1))
        // return Command::FAILURE;

        // or return this to indicate incorrect command usage; e.g. invalid options
        // or missing arguments (it's equivalent to returning int(2))
        // return Command::INVALID
    }
}