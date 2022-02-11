<?php
// src/Service/SearchReadings.php
namespace App\Service;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Reading;

class SearchReadings
{
    public function getReadingsBy(DocumentManager $dm, $generator_id, $from, $to, $i)
    {
        /*
        - id generatora
        - data od
        - data do
        */
        $builder = $dm->createAggregationBuilder(Reading::class);
        $builder
        ->match()
            ->field('date')->type('date')
            ->gte($from)
            ->lt($to)
            ->field('generator_id')
            ->equals($generator_id)
            ->limit(20 * $i)
            ->skip( 20 * ($i - 1))
            ;
        $readings = $builder->getAggregation();
        return $readings;
    }
}