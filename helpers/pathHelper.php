<?php

/**
 * @return string
 */
function root()
{
    return app()->root();
}

/**
 * @param string $dir
 *
 * @return string
 */
function path($dir)
{
    return root() . $dir;
}
