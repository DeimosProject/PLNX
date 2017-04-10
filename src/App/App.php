<?php

namespace Deimos\App;

use Deimos\Builder\Builder;
use Deimos\Config\Config;
use Deimos\Helper\Helper;

class App extends Builder
{

    /**
     * @var string
     */
    protected $root;

    /**
     * App constructor.
     *
     * @param string $root
     */
    public function __construct($root)
    {
        $this->root = rtrim($root, '\\/') . '/';
    }

    /**
     * @return string
     */
    public function root()
    {
        return $this->root;
    }

    /**
     * @return Helper
     */
    public function helper()
    {
        return $this->once(function ()
        {
            return new Helper($this);
        }, __METHOD__);
    }

    /**
     * @return Config
     */
    public function config()
    {
        return $this->once(function ()
        {
            $config = path('config');

            return new Config($this->helper(), $config);
        }, __METHOD__);
    }

}