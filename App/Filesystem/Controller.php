<?php

namespace App\Filesystem;

use League\Flysystem\Adapter\Local;
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

    public function createFolderIfNotExists($folder)
    {
        if (! $this->filesystem->has($folder)) {
            $this->filesystem->createDir($folder);
        }
    }

    /**
     *  Write items into cache
     *
     *  @param string $path
     *  @param array $data
     */
    public function cacheItems($path, $data)
    {
        $adapter = new Local(dirname($path));

        $name = str_replace($adapter->getPathPrefix(), '', $path);

        if (file_exists($path))
            $data = array_merge($data, require $path);

        (new FileSystem($adapter))
            ->put($name, '<?php return '.var_export($data, true).';'.PHP_EOL);
    }
}
