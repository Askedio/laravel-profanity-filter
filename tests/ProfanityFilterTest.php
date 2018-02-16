<?php

namespace Askedio\Laravel5ProfanityFilter\Tests;

use Askedio\Laravel5ProfanityFilter\ProfanityFilter;

class ProfanityFilterTest extends TestCase
{
    private $string = 'hi you fucking cunt fuck shit!';

    public function testPartialTextProfanityFilter()
    {
        \Config::set('profanity.replaceFullWords', false);
        $this->assertEquals('hi you ****ing **** **** ****!', app('profanityFilter')->filter($this->string));
    }

    public function testFullTextProfanityFilter()
    {
        \Config::set('profanity.replaceFullWords', true);
        $this->assertEquals('hi you fucking **** **** ****!', app('profanityFilter')->filter($this->string));
    }

    public function testMultiCharReplaceWith()
    {
        \Config::set('profanity.replaceFullWords', true);
        \Config::set('profanity.replaceWith', '**');
        $this->assertEquals('hi you fucking **** **** ****!', app('profanityFilter')->filter($this->string));
    }

    public function testReplaceWith()
    {
        $this->assertEquals('hi you fucking #### #### ####!', app('profanityFilter')->replaceWith('#')->filter($this->string));
    }

    public function testReplaceFullWords()
    {
        $this->assertEquals('hi you ****ing **** **** ****!', app('profanityFilter')->replaceFullWords(false)->filter($this->string));
    }

    public function testMultipleFiltersWithReset()
    {
        $this->assertEquals('hi you fucking #### #### ####!', app('profanityFilter')->replaceWith('#')->filter($this->string));
        $this->assertEquals('hi you ****ing **** **** ****!', app('profanityFilter')->reset()->replaceFullWords(false)->filter($this->string));
    }

    public function testEsLanguage()
    {
        app()->setLocale('es');

        $this->assertEquals('hi you ****ing cunt **** ******!', app('profanityFilter')->replaceFullWords(false)->filter('hi you fucking cunt fuck mierda!'));
    }

    public function testEmptyFilter()
    {
        $this->assertEquals(' ', app('profanityFilter')->filter(' '));
    }

    public function testFilterDetails()
    {
        $this->assertEquals([
          'orig'     => 'hi you fucking cunt fuck shit!',
          'clean'    => 'hi you ****ing **** **** ****!',
          'hasMatch' => true,
          'matched'  => [
            0 => 'fuck',
            1 => 'shit',
            2 => 'cunt',
            3 => 'fuck',
          ],
        ], app('profanityFilter')->replaceFullWords(false)->filter($this->string, true));
    }
}
