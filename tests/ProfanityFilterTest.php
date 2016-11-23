<?php

namespace Askedio\Laravel5ProfanityFilter\Tests;

use Askedio\Laravel5ProfanityFilter\ProfanityFilter;

class ProfanityFilterTest extends TestCase
{
    public function testPartialTextProfanityFilter()
    {
        \Config::set('profanity.replaceFullWords', false);
        $this->assertEquals('hi you ****ing **** **** ****!', app('profanityFilter')->filter('hi you fucking cunt fuck shit!'));
    }

    public function testFullTextProfanityFilter()
    {
        \Config::set('profanity.replaceFullWords', true);
        $this->assertEquals('hi you fucking **** **** ****!', app('profanityFilter')->filter('hi you fucking cunt fuck shit!'));
    }

    public function testMultiCharReplaceWith()
    {
        \Config::set('profanity.replaceFullWords', true);
        \Config::set('profanity.replaceWith', '**');
        $this->assertEquals('hi you fucking **** **** ****!', app('profanityFilter')->filter('hi you fucking cunt fuck shit!'));
    }

    public function testEmptyFilter()
    {
        $this->assertEquals('', app('profanityFilter')->filter(' '));
    }
}
