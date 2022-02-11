<?php
namespace App\Controller;
use App\Document\Reading;
use App\Service\SearchReadings;
use DateInterval;
use DatePeriod;
use DateTime;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReadingController extends AbstractController
{
    /**
     * @Route("/reading/add", name="add_reading")
     */
    public function createAction(DocumentManager $dm, Request $request)
    {
        $reading = new Reading();
        $reading->setDate($request->request->get('date'));
        $reading->setGeneratorId($request->request->get('generator_id'));
        $reading->setTime($request->request->get('time'));
        $reading->setPower($request->request->get('power'));

        $dm->persist($reading);
        $dm->flush();

        return new Response('Created reading id ' . $reading->getId());
    }

    /**
     * @Route("/reading/show/{id}", name="show_reading")
     */
    public function showAction(DocumentManager $dm, $id)
    {
        $reading = $dm->getRepository(Reading::class)->find($id);
        if (!$reading) {
            throw $this->createNotFoundException('No generator found for id ' . $id);
        }
        return new Response("Identyfikator:" . $reading->getId() . "\nDodano:" . $reading->getDate() . "\nCzas:" . $reading->getTime() . "\nIdentyfikator Generatora:" . $reading->getGeneratorId());
    }

    /**
     * @Route("/reading/generate_for_day", name="generate_reading_for_one_day")
     */
    public function generateReadingsForOneDay(DocumentManager $dm)
    {
        for ($i = 1; $i < 21; $i++) {
            for ($j = 0.5; $j < 120.5; $j+=0.5) { // 24 hour * 10 readings per hour
                $reading = new Reading();
                $reading->setDate('2021-10-02');
                $reading->setGeneratorId($i);
                $reading->setTime($j);
                $reading->setPower(random_int(1, 1000));
        
                $dm->persist($reading);
                $dm->flush();
            }
        }
        return new Response('Created reading id ' . $reading->getId());
    }

    /**
     * @Route("/reading/generate_for_year", name="generate_reading_for_one_year")
     */
    public function generateReadingsForOneYear(DocumentManager $dm)
    {
        $start = new DateTime('01/01/2019');
        $end = new DateTime('12/31/2019');
        $oneday = new DateInterval("P1D");

        foreach(new DatePeriod($start, $oneday, $end->add($oneday)) as $day) {
            for ($i = 1; $i < 21; $i++) {
                for ($j = 0.5; $j < 1440.5; $j+=0.5) { // 24 hour * 120 readings per hour
                    $reading = new Reading();
                    $reading->setDate($day->format("Y-m-d"));
                    $reading->setGeneratorId($i);
                    $reading->setTime($j);
                    $reading->setPower(random_int(1, 1000));
            
                    $dm->persist($reading);
                    $dm->flush();
                }
            }
        }
    }

    /**
     * @Route("/reading/search/{generator_id}/{from}/{to}/{page}", name="search_readings_by")
     */
    public function searchReadingsBy(DocumentManager $dm, SearchReadings $sr, $generator_id, $from, $to, $page)
    {
        $result = $sr->getReadingsBy($dm, $generator_id, $from, $to, $page);
        return $this->render('readings/search.html.twig', ['readings' => $result]);
    }
}