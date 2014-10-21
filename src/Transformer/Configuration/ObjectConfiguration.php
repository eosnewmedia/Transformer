<?php

namespace Enm\Transformer\Configuration;

use Enm\Transformer\Enums\StringValidationEnum;
use Enm\Transformer\Enums\TypeEnum;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class ObjectConfiguration
 *
 * @package Enm\TransformerBundle\DependencyInjection
 * @author  Philipp Marien <marien@eosnewmedia.de>
 */
class ObjectConfiguration extends BaseConfiguration implements ConfigurationInterface
{

  /**
   * Generates the configuration tree builder.
   *
   * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
   */
  public function getConfigTreeBuilder()
  {
    $treeBuilder = new TreeBuilder();
    $rootNode    = $treeBuilder->root('config');

    $rootNode
      ->useAttributeAsKey('name')
        ->prototype('array')
          ->children()
            ->enumNode('type')->values(array_merge(TypeEnum::toArray(), array_map('strtoupper', TypeEnum::toArray())))->defaultValue(null)->end()
            ->scalarNode('renameTo')->defaultValue(null)->end()
            ->arrayNode('children')->prototype('variable')->end()->end()
            ->arrayNode('options')
            ->addDefaultsIfNotSet()
              ->children()
                ->booleanNode('assoc')->defaultValue(true)->end()
                ->arrayNode('expected')->prototype('scalar')->end()->end()
                ->enumNode('stringValidation')->values(array_merge(StringValidationEnum::toArray(), array_map('strtoupper', StringValidationEnum::toArray())))->defaultValue(null)->end()
                ->booleanNode('strongValidation')->defaultValue(false)->end()
                ->floatNode('min')->defaultValue(null)->end()
                ->floatNode('max')->defaultValue(null)->end()
                ->integerNode('round')->defaultValue(null)->end()
                ->booleanNode('floor')->defaultValue(false)->end()
                ->booleanNode('ceil')->defaultValue(false)->end()
                ->scalarNode('returnClass')->defaultValue(null)->end()
                ->variableNode('defaultValue')->defaultValue(null)->end()
                ->scalarNode('regex')->defaultValue(null)->end()
                ->arrayNode('date')
                  ->addDefaultsIfNotSet()
                  ->children()
                    ->variableNode('expectedFormat')->defaultValue('Y-m-d')->end()
                    ->booleanNode('convertToObject')->defaultValue(false)->end()
                    ->scalarNode('convertToFormat')->defaultValue(null)->end()
                  ->end()
                ->end()
                ->booleanNode('required')->defaultValue(false)->end()
                ->arrayNode('requiredIfNotAvailable')
                ->addDefaultsIfNotSet()
                  ->children()
                    ->arrayNode('and')
                      ->prototype('scalar')->end()
                      ->defaultValue(array())
                    ->end()
                    ->arrayNode('or')
                      ->prototype('scalar')->end()
                      ->defaultValue(array())
                    ->end()
                  ->end()
                ->end()
                ->arrayNode('requiredIfAvailable')
                  ->addDefaultsIfNotSet()
                  ->children()
                    ->arrayNode('and')
                      ->prototype('scalar')->end()
                      ->defaultValue(array())
                    ->end()
                    ->arrayNode('or')
                      ->prototype('scalar')->end()
                      ->defaultValue(array())
                    ->end()
                  ->end()
                ->end()
                ->arrayNode('forbiddenIfNotAvailable')
                  ->prototype('scalar')->end()
                  ->defaultValue(array())
                ->end()
                ->arrayNode('forbiddenIfAvailable')
                  ->prototype('scalar')->end()
                  ->defaultValue(array())
                ->end()
                ->arrayNode('length')
                  ->addDefaultsIfNotSet()
                  ->children()
                    ->floatNode('min')->defaultValue(null)->end()
                    ->floatNode('max')->defaultValue(null)->end()
                  ->end()
                ->end()
                ->arrayNode('individual')->prototype('variable')->end()->end()
                ->append($this->addEventConfiguration())
              ->end()
            ->end()
          ->end()
        ->end()
      ->end();

    return $treeBuilder;
  }
}
