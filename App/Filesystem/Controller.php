<?php

namespace App\Filesystem;

use League\Flysystem\Filesystem;

class Controller
{
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Get all local courses [name => [0,1,2]]
     * 
     * @return array
     */
    public function courses()
    {
        $contents = $this->filesystem->listContents(null, true);

        $array = [];

        foreach ($contents as $content) {
            if ($content['type'] != 'file') continue;

            $course = $content['dirname'];

            $episode = (int) substr($content['filename'], 0, strpos($content['filename'], '-'));

            $array[$course][] = $episode;
        }

        return $array;
    }
}