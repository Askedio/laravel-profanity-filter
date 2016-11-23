<?php

namespace Askedio\Laravel5ProfanityFilter;

class ProfanityFilter
{
    protected $replaceWith = '';

    protected $badWords = [];

    protected $censorChecks = [];

    protected $replaceFullWords = true;

    protected $multiCharReplace = false;

    protected $strReplace = [];

    protected $replaceWithLength;

    protected $config = [];

    public function __construct()
    {
        $this->config = config('profanity');

        $this->strReplace = $this->config['strReplace'];

        $this->replaceFullWords($this->config['replaceFullWords']);

        $this->replaceWith($this->config['replaceWith']);

        $this->badWords = array_merge(
            $this->config['defaults'],
            trans('Laravel5ProfanityFilter::profanity')
        );

        $this->generateCensorChecks();
    }

    public function replaceWith($string)
    {
        $this->replaceWith = $string;

        $this->replaceWithLength = strlen($this->replaceWith);

        $this->multiCharReplace = $this->replaceWithLength === 1;

        return $this;
    }

    public function replaceFullWords($boolean)
    {
        $this->replaceFullWords = $boolean;

        $this->generateCensorChecks();

        return $this;
    }

    public function filter($string)
    {
        if (!is_string($string) || !trim($string)) {
            return $string;
        }

        return $this->filterString($string);
    }

    private function filterString($string)
    {
        return preg_replace_callback($this->censorChecks, function ($matches) {
            return $this->replaceWithFilter($matches[0]);
        }, $string);
    }

    private function replaceWithFilter($string)
    {
        $strlen = strlen($string);

        if ($this->multiCharReplace) {
            return str_repeat($this->replaceWith, $strlen);
        }

        return $this->randomCensorChar($strlen);
    }

    private function generateCensorChecks()
    {
        foreach ($this->badWords as $string) {
            $this->censorChecks[] = $this->getCensorRegexp($string);
        }
    }

    private function getCensorRegexp($string)
    {
        $replaceCensor = $this->replaceCensor($string);

        if ($this->replaceFullWords) {
            return '/\b'.$replaceCensor.'\b/i';
        }

        return '/'.$replaceCensor.'/i';
    }

    private function replaceCensor($string)
    {
        return str_ireplace(array_keys($this->strReplace), array_values($this->strReplace), $string);
    }

    private function randomCensorChar($len)
    {
        return str_shuffle(str_repeat($this->replaceWith, intval($len / $this->replaceWithLength)).substr($this->replaceWith, 0, ($len % $this->replaceWithLength)));
    }
}
