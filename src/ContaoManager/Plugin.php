<?php

declare(strict_types=1);

namespace Dvc\ContaoPlentaJobsSelectWidgetBundle\ContaoManager;

use Dvc\ContaoPlentaJobsSelectWidgetBundle\DvcContaoPlentaJobsSelectWidgetBundle;
use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Plenta\ContaoJobsBasic\PlentaContaoJobsBasicBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(DvcContaoPlentaJobsSelectWidgetBundle::class)
                ->setLoadAfter([
                    ContaoCoreBundle::class,
                    PlentaContaoJobsBasicBundle::class,
                ])
        ];
    }
}
