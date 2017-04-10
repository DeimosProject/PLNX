<?php

/**
 * @return \Deimos\App\App
 */
function app()
{
    static $app;

    if (!$app)
    {
        $root = dirname(__DIR__);
        $app  = new \Deimos\App\App($root);
    }

    return $app;
}

/**
 * @return \Deimos\Helper\Helper
 */
function helper()
{
    return \app()->helper();
}

/**
 * @param string $path
 *
 * @return \Deimos\Slice\Slice|mixed
 */
function config($path)
{
    return \app()->config()->get($path);
}

/**
 * @param string $modelName
 *
 * @return \Deimos\ORM\Queries\Query
 */
function repository($modelName)
{
    return \app()->orm()->repository($modelName);
}

/**
 * @return \Deimos\Database\Database
 */
function database()
{
    return \app()->database();
}
