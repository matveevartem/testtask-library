<?php

use \PHPUnit\Framework\TestCase;
use ArtemMatveev\TestTask\Tasks\Counting\Counter;

class CounterTest extends TestCase
{
    const TMP_FOLDER = 'counter_test';
    const TARGET_FILE = 'count';

    protected string $entryDir;

    public function __construct($name = null, $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->entryDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . self::TMP_FOLDER;
    }

    protected function createDirTree()
    {
        if (!file_exists($this->entryDir)) {
            mkdir($this->entryDir, 0777, true);
        }

        for ($i = 0; $i < 4; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $dir = $this->entryDir . DIRECTORY_SEPARATOR . $i . DIRECTORY_SEPARATOR . $j;
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }
                if ($j % 2) {
                    $file = $dir . DIRECTORY_SEPARATOR . 'count';
                } else {
                    $file = $dir . DIRECTORY_SEPARATOR . $i . '-' . $j;
                }
                file_put_contents($file, $i . ' - ' . $j);
            }
            $dir = $this->entryDir . DIRECTORY_SEPARATOR . $i;
            if ($i % 2) {
                $file = $dir . DIRECTORY_SEPARATOR . 'count';
            } else {
                $file = $dir . DIRECTORY_SEPARATOR . $i . '_' . $i;
            }

            file_put_contents($file, $i . ' - ' . $i);
        }
    }


    /**
     * Deletes a folder with all its contents
     * @param $dir
     * @return bool
     */
    protected function removeDirTree($dir)
    {
        $files = array_diff(scandir($dir), ['.','..']);
        foreach ($files as $file) {
            (is_dir($dir.'/'.$file)) ? $this->removeDirTree($dir.'/'.$file) : unlink($dir.'/'.$file);
        }
        return rmdir($dir);
    }

    public function testCount()
    {
        $this->createDirTree();
        $counter = new Counter($this->entryDir, 'count');
        $this->assertEquals($counter->count(), 18.00);
        $this->removeDirTree($this->entryDir);
    }
}
