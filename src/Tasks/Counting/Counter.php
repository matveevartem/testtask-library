<?php

namespace ArtemMatveev\TestTask\Tasks\Counting;

class Counter
{
    protected string $targetPath;
    protected string $needle;
    protected bool $caseSensitive;

    protected float $count = 0;

    /**
     * @param string $targetPath path to target folder
     * @param string $needle target filename
     * @param bool $caseSensitive case sensitive search flag
     */
    public function __construct(string $targetPath, string $needle = 'count', bool $caseSensitive = false)
    {
        $this->targetPath = $targetPath;
        $this->needle = $needle;
        $this->caseSensitive = $caseSensitive;
    }

    /**
     * Parses the file, extracts numbers and calculates them
     * 
     * @param \DirectoryIterator $iterator
     */
    protected function calculateFromFile(\DirectoryIterator $iterator)
    {
        $content = file_get_contents($iterator->getPathname());
        $content = preg_replace('/[ ]+/', ' ', preg_replace('/[^0-9 \.]/', ' ', str_replace(PHP_EOL, '' , $content)));
        foreach (explode(' ', $content) as $value) {
            $this->count += floatval($value);
        }
    }

    /**
     * Goes through the subfolders and looks for the desired file name
     * 
     * @param string $path
     */
    protected function walkDirTree(string $path)
    {
        $iterator = new \DirectoryIterator($path);

        foreach($iterator as $current)
        {
            if ($current->getFilename() != '.' && $current->getFilename() != '..') {
                if ($current->isDir()) {
                    $this->walkDirTree($current->getPathname());
                } else {
                    if (
                        $this->caseSensitive
                        ?
                        !strcasecmp($current->getFilename(), $this->needle)
                        :
                        !strcmp($current->getFilename(), $this->needle)
                    ) {
                        $this->calculateFromFile($current);
                    }
                }
            }
        }
    }

    /**
     * Starts a calculation operation from BasePath
     */
    public function count(): float
    {
        $this->count = 0;

        $this->walkDirTree($this->targetPath);

        return $this->count;
    }
}

//echo (new Counter('/home/artem/dev/test/comments-example'))->count();
//echo (new Counter('/tmp/counter_test'))->count();
