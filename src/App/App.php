<?php

namespace Deimos\App;

use Deimos\Builder\Builder;
use Deimos\Config\Config;
use Deimos\Database\Database;
use Deimos\Helper\Helper;
use Deimos\ORM\ORM;
use Deimos\ORM\StaticORM;

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
        $this->root = \rtrim($root, '\\/') . '/';
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
            $config = \path('config');

            return new Config(\helper(), $config);
        }, __METHOD__);
    }

    /**
     * @return Database
     */
    public function database()
    {
        return $this->once(function ()
        {
            return new Database(\config('db'));
        }, __METHOD__);
    }

    /**
     * @return ORM
     */
    public function orm()
    {
        return $this->once(function ()
        {
            $relationships = \config('relationships')->asArray();

            $orm = new ORM(\helper(), \database());
            $orm->setConfig($relationships);

            StaticORM::setORM($orm);

            return $orm;

        }, __METHOD__);
    }

}