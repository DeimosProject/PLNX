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
    return app()->helper();
}

/**
 * @param string $path
 *
 * @return \Deimos\Config\Config
 */
function config($path)
{
    return app()->config()->get($path);
}
