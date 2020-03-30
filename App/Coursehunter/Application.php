<?php


namespace App\Coursehunter;


use App\Coursehunter\Controller as CoursehunterController;
use App\Filesystem\Controller as FilesystemController;
use App\Exceptions\OptionNotFoundException;
use League\Flysystem\Filesystem;
use App\Utility\Utility;
use App\Http\Resolver;
use GuzzleHttp\Client;

class Application
{
    protected $system;
    protected $coursehunter;
    protected $resolver;
    private $basePath;

    /**
     * Dependencies Auto Injection
     *
     * @param Client $client
     * @param Filesystem $filesystem
     * @param string $basePath
     */
    public function __construct(Client $client, Filesystem $filesystem, $basePath)
    {
        $this->basePath = $basePath;
        $this->coursehunter = new CoursehunterController($client);
        $this->system = new FilesystemController($filesystem);
        $this->resolver = new Resolver($client);
    }

    public function authenticate($email, $password)
    {
        if (is_null($email) or is_null($password))
            throw new OptionNotFoundException('Email or password options are not provided');

        Utility::write('Authenticating ...');

        if (! $this->coursehunter->authenticate($email, $password))
            throw new \LogicException('Something is wrong with your authentication credentials');
    }

    /**
     * Get the base path for the application
     *
     * @param string $path
     * @return string
     */
    public function basePath($path = '')
    {
        return $this->basePath . ($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the path to courses cache file
     *
     * @return string
     */
    public function getCachedItemsPath() {
        return $this->basePath('Cache/items.php');
    }
}
