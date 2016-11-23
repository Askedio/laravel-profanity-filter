<?php

namespace Askedio\Laravel5ProfanityFilter;

class ProfanityFilter
{
    protected $replaceWith = '';

    protected $badWords = [];

    protected $censorChecks = [];

    protected $replaceFullWords = true;

    protected $multiReplacer = false;

    protected $strReplace = [];

    protected $config = [];


    public function __construct()
    {
        $this->config = config('profanity');
        
        $this->replaceFullWords = $this->config['replaceFullWords'];

        $this->strReplace = $this->config['strReplace'];

        $this->replaceWith = $this->config['replaceWith'];

        $this->multiReplacer = strlen($this->replaceWith) === 1;

        $this->badWords = array_merge(
            $this->config['defaults'],
            trans('Laravel5ProfanityFilter::profanity')
        );

        $this->generateCensorChecks();
    }

    public function filter($string)
    {
        return $this->censorString($string);
    }

    private function censorString($string)
    {
        return preg_replace_callback($this->censorChecks, function ($matches) {
            return $this->replaceWithReplace($matches[0]);
        }, $string);
  	}

    private function replaceWithReplace($string)
    {
        return $this->multiReplacer
          ? str_repeat($this->replaceWith, strlen($string))
          : $this->randCensor($this->replaceWith, strlen($string));
    }

    private function generateCensorChecks()
    {
        foreach ($this->badWords as $word) {
            $this->censorChecks[] =  $this->replaceWords($word);
        }
  	}

    private function replaceWords($string)
    {
        if ($this->replaceFullWords) {
            return '/\b'.$this->replaceCensor($string).'\b/i';
        }

        return '/'.$this->replaceCensor($string).'/i';
    }

    private function replaceCensor($string)
    {
        return str_ireplace(array_keys($this->strReplace), array_values($this->strReplace), $string);
    }

    public function randCensor($chars, $len)
    {
        return str_shuffle(str_repeat($chars, intval($len/strlen($chars))).substr($chars, 0, ($len%strlen($chars))));
    }
}
