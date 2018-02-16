<?php

namespace Askedio\Laravel5ProfanityFilter;

class ProfanityFilter
{
    protected $replaceWith = '';

    protected $badWords = [];

    protected $filterChecks = [];

    protected $replaceFullWords = true;

    protected $multiCharReplace = false;

    protected $strReplace = [];

    protected $replaceWithLength;

    protected $config = [];

    protected $filteredStrings = [];

    protected $wasFiltered = false;

    public function __construct($config, $badWordsArray)
    {
        $this->config = $config;

        $this->strReplace = $this->config['strReplace'];

        $this->reset();

        $this->badWords = array_merge(
            $this->config['defaults'],
            $badWordsArray
        );

        $this->generateFilterChecks();
    }

    public function reset()
    {
        $this->replaceFullWords($this->config['replaceFullWords']);

        $this->replaceWith($this->config['replaceWith']);

        return $this;
    }

    public function replaceWith($string)
    {
        $this->replaceWith = $string;

        $this->replaceWithLength = mb_strlen($this->replaceWith);

        $this->multiCharReplace = $this->replaceWithLength === 1;

        return $this;
    }

    public function replaceFullWords($boolean)
    {
        $this->replaceFullWords = $boolean;

        $this->generateFilterChecks();

        return $this;
    }

    private function resetFiltered()
    {
        $this->filteredStrings = [];

        $this->wasFiltered = false;
    }

    public function filter($string, $details = null)
    {
        $this->resetFiltered();

        if (!is_string($string) || !trim($string)) {
            return $string;
        }

        $filtered = $this->filterString($string);

        if ($details) {
            return [
                'orig'     => $string,
                'clean'    => $filtered,
                'hasMatch' => $this->wasFiltered,
                'matched'  => $this->filteredStrings,
            ];
        }

        return $filtered;
    }

    private function filterString($string)
    {
        return preg_replace_callback($this->filterChecks, function ($matches) {
            return $this->replaceWithFilter($matches[0]);
        }, $string);
    }

    private function setFiltered($string)
    {
        array_push($this->filteredStrings, $string);

        if (!$this->wasFiltered) {
            $this->wasFiltered = true;
        }
    }

    private function replaceWithFilter($string)
    {
        $this->setFiltered($string);

        $strlen = mb_strlen($string);

        if ($this->multiCharReplace) {
            return str_repeat($this->replaceWith, $strlen);
        }

        return $this->randomFilterChar($strlen);
    }

    private function generateFilterChecks()
    {
        foreach ($this->badWords as $string) {
            $this->filterChecks[] = $this->getFilterRegexp($string);
        }
    }

    private function getFilterRegexp($string)
    {
        $replaceFilter = $this->replaceFilter($string);

        if ($this->replaceFullWords) {
            return '/\b'.$replaceFilter.'\b/iu';
        }

        return '/'.$replaceFilter.'/iu';
    }

    private function replaceFilter($string)
    {
        return str_ireplace(array_keys($this->strReplace), array_values($this->strReplace), $string);
    }

    private function randomFilterChar($len)
    {
        return str_shuffle(str_repeat($this->replaceWith, intval($len / $this->replaceWithLength)).substr($this->replaceWith, 0, ($len % $this->replaceWithLength)));
    }
}
