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

    protected $config = [];

    public function __construct()
    {
        $this->config = config('profanity');

        $this->replaceFullWords = $this->config['replaceFullWords'];

        $this->strReplace = $this->config['strReplace'];

        $this->replaceWith = $this->config['replaceWith'];

        $this->multiCharReplace = strlen($this->replaceWith) === 1;

        $this->badWords = array_merge(
            $this->config['defaults'],
            trans('Laravel5ProfanityFilter::profanity')
        );

        $this->generateCensorChecks();
    }

    public function filter($string)
    {
        if (!is_string($string) || !trim($string)) {
            return '';
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

    public function randomCensorChar($len)
    {
        $strlen = strlen($this->replaceWith);

        return str_shuffle(str_repeat($this->replaceWith, intval($len / $strlen)).substr($this->replaceWith, 0, ($len % $strlen)));
    }
}
