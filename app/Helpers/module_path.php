<?php

if (!function_exists('module_path')) {
    function module_path($module, $path = '')
    {
        return app()->basePath('modules/' . $module . ($path ? DIRECTORY_SEPARATOR . $path : $path));
    }
}
