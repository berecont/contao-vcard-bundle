<?php
namespace Berecont\ContaoVcardBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Routing\RoutingPluginInterface;
use Berecont\ContaoVcardBundle\ContaoVcardBundle;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class Plugin implements BundlePluginInterface, RoutingPluginInterface {
    public function getBundles(ParserInterface $parser): array {
        return [BundleConfig::create(ContaoVcardBundle::class)->setLoadAfter([ContaoCoreBundle::class])];
    }

    public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel) {
        return $resolver
            ->resolve(__DIR__.'/../../config/routes.yaml')
            ->load(__DIR__.'/../../config/routes.yaml')
        ;
    }
}
