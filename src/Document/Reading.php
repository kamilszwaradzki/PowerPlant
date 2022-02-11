<?php
// src/Document/Reading.php
namespace App\Document;

use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document(collection="power_plant")
 */
class Reading
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(type="int")
     * @Assert\NotBlank()
     */
    protected $generator_id;

    /**
     * @MongoDB\Field(type="float")
     * @Assert\NotBlank()
     */
    protected $time;

    /**
     * @MongoDB\Field(type="date")
     * @Assert\NotBlank()
     */
    protected $date;

    /**
     * @MongoDB\Field(type="float")
     * @Assert\NotBlank()
     */
    protected $power;

    public function getId()
    {
        return $this->id;
    }

    public function getGeneratorId()
    {
        return $this->generator_id;
    }

    public function setGeneratorId($generator_id)
    {
        $this->generator_id = $generator_id;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function setTime($time)
    {
        $this->time = $time;
    }

    public function getDate()
    {
        return $this->date->format("Y-m-d");
    }

    public function setDate($date)
    {
        $this->date = $date;
    }
    
    public function getPower()
    {
        return $this->power;
    }

    public function setPower($power)
    {
        $this->power = $power;
    }   
}