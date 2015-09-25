<?php

namespace AppBundle\Tests\Unit\Utils;

use AppBundle\Entity\Project;
use AppBundle\Tests\Unit\TestCase;
use AppBundle\Utils\ProjectCodeConverter;

class ProjectCodeConverterTest extends TestCase
{
    public function testToAcronym()
    {
        $projectCodeConverter = new ProjectCodeConverter();

        $project = new Project();

        $this->assertEquals('OOP', $projectCodeConverter->getCode($project->setLabel('Object-Oriented Programming')));
        $this->assertEquals('UCC', $projectCodeConverter->getCode($project->setLabel('UpperCamelCase')));
        $this->assertEquals('CC', $projectCodeConverter->getCode($project->setLabel('lowerCamelCase')));
        $this->assertEquals('BC', $projectCodeConverter->getCode($project->setLabel('Bonnie and Clyde')));
        $string = 'Объектно-Ориентированное Программирование';
        $this->assertEquals('ООП', $projectCodeConverter->getCode($project->setLabel($string)));
        $this->assertEquals('OOP', $projectCodeConverter->getCode($project->setLabel('object-oriented programming')));
        $this->assertEquals('U', $projectCodeConverter->getCode($project->setLabel('uppercamelcase')));
        $this->assertEquals('L', $projectCodeConverter->getCode($project->setLabel('lowercamelcase')));
        $this->assertEquals('BAC', $projectCodeConverter->getCode($project->setLabel('bonnie and clyde')));
        $message = 'объектно-ориентированное программирование';
        $this->assertEquals('ООП', $projectCodeConverter->getCode($project->setLabel($message)));
        $this->assertEquals('', $projectCodeConverter->getCode($project->setLabel('@3 @# @#4')));
        $this->assertEquals('', $projectCodeConverter->getCode($project->setLabel('@3@#@#4')));
    }
}
