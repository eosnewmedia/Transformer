<?php


namespace Enm\Transformer\Tests\Complex;

use Enm\Transformer\Tests\BaseTransformerTestClass;

class BaseComplex extends BaseTransformerTestClass
{

  protected function getConfig()
  {
    $config = array(
      'user'    => [
        'type' => 'string'
      ],
      'address' => [
        'type'     => 'object',
        'children' => [
          'street'  => [
            'type' => 'string'
          ],
          'plz'     => [
            'type' => 'string'
          ],
          'place'   => [
            'type' => 'string'
          ],
          'country' => [
            'type' => 'string'
          ]
        ],
        'options'  => [
          'returnClass' => 'stdClass'
        ]
      ]
    );

    return $config;
  }



  protected function getParams()
  {
    $params = array(
      'user'    => 'testUser',
      'address' => [
        'street'  => 'Test Straße 3a',
        'plz'     => '21031',
        'place'   => 'Test Place',
        'country' => 'Germany'
      ]
    );

    return $params;
  }
}
 