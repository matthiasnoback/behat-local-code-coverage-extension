<?php
declare(strict_types=1);

namespace BehatLocalCodeCoverage;

use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class LocalCodeCoverageExtension implements Extension
{
    public function process(ContainerBuilder $container)
    {
    }

    public function getConfigKey()
    {
        return 'local_code_coverage';
    }

    public function initialize(ExtensionManager $extensionManager)
    {
    }

    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->scalarNode('phpunit_xml_path')
                    ->defaultValue('%paths.base%/phpunit.xml.dist')
                    ->info('The path of the PHPUnit XML file containing the coverage filter configuration.')
                ->end()
                ->scalarNode('target_directory')
                    ->isRequired()
                    ->info('The directory where the generated coverage files should be stored.')
                ->end()
            ->end();
    }

    public function load(ContainerBuilder $container, array $config)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('local_code_coverage.phpunit_xml_path', $config['phpunit_xml_path']);
        $container->setParameter('local_code_coverage.target_directory', $config['target_directory']);
    }
}
