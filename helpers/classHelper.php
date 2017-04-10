<?php

/**
 * @return \Deimos\App\App
 */
function app()
{
    global $app;

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
 * @param string $className
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
