<?php

namespace AppBundle\Tests\Unit\Utils;

use AppBundle\Tests\Unit\TestCase;
use AppBundle\Utils\NameConverter;

class NameConverterTest extends TestCase
{
    public function testToAcronym()
    {
        $nameConverter = new NameConverter();


        $this->assertEquals('OOP', $nameConverter->toAcronym('Object-Oriented Programming'));
        $this->assertEquals('UCC', $nameConverter->toAcronym('UpperCamelCase'));
        $this->assertEquals('CC', $nameConverter->toAcronym('lowerCamelCase'));
        $this->assertEquals('BC', $nameConverter->toAcronym('Bonnie and Clyde'));
        $string = 'Объектно-Ориентированное Программирование';
        $this->assertEquals('ООП', $nameConverter->toAcronym($string));
        $this->assertEquals('OOP', $nameConverter->toAcronym('object-oriented programming'));
        $this->assertEquals('U', $nameConverter->toAcronym('uppercamelcase'));
        $this->assertEquals('L', $nameConverter->toAcronym('lowercamelcase'));
        $this->assertEquals('BAC', $nameConverter->toAcronym('bonnie and clyde'));
        $message = 'объектно-ориентированное программирование';
        $this->assertEquals('ООП', $nameConverter->toAcronym($message));
        $this->assertEquals('', $nameConverter->toAcronym('@3 @# @#4'));
        $this->assertEquals('', $nameConverter->toAcronym('@3@#@#4'));
    }
}
