<?php

namespace Enm\Transformer\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class TransformerConfiguration
 *
 * @package Enm\TransformerBundle\DependencyInjection
 * @author  Philipp Marien <marien@eosnewmedia.de>
 */
class TransformerConfiguration extends BaseConfiguration implements ConfigurationInterface
{

  /**
   * Generates the configuration tree builder.
   *
   * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
   */
  public function getConfigTreeBuilder()
  {
    $treeBuilder = new TreeBuilder();
    $rootNode    = $treeBuilder->root('enm_transformer');

    $rootNode
        ->useAttributeAsKey('name')
        ->prototype('array')
          ->children()
            ->append($this->addEventConfiguration())
          ->end()
        ->end()
      ->end();

    return $treeBuilder;
  }
}
