<?php
declare(strict_types=1);

namespace Harpya\SDK\Core;

use Phalcon\Mvc\View\Engine\Volt;
use Phalcon\Mvc\View;
use \Harpya\SDK\Constants;

/**
 *
 */
class ViewUtils
{
    public static $lsFilters = [];

    public static function initFilters()
    {
        $formatDateTime = 'd/m/Y H:i:s';
        $formatDateTime = getenv(Constants::CONFIG_DATETIME_FORMAT);
        if (!$formatDateTime) {
            $formatDateTime = 'Y-m-d';
        }

        static::$lsFilters = [
            'date_format' => function ($resolvedArgs, $exprArgs) use ($formatDateTime) {
                return ' (strtotime(' . $resolvedArgs . ')) ?  date("' . $formatDateTime . '", strtotime(' . $resolvedArgs . ')):null';
            }
        ];
    }

    public static function addFilters($di)
    {
        $volt = $di->getShared('volt');
        if (!$volt) {
            $view = $di->getShared('view');

            if (!$view) {
                $view = new View();
            }

            $volt = new Volt($view, $di);
        }
        $compiler = $volt->getCompiler();

        foreach (static::$lsFilters as $filterName => $closure) {
            $compiler->addFilter(
                $filterName,
                $closure
            );
        }
    }
}
