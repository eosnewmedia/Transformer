<?php


namespace Enm\Transformer;

use Enm\Transformer\Interfaces\EnumInterface;
use Enm\Transformer\Traits\EnumTrait;

/**
 * Class TransformerEvents
 *
 * @package Enm\Transformer
 * @author  Philipp Marien <marien@eosnewmedia.de>
 */
class TransformerEvents implements EnumInterface
{

  use EnumTrait;

  /**
   * Throws \Enm\Transformer\Event\ExceptionEvent
   */
  const ON_EXCEPTION = 'enm.transformer.event.on.exception';

  /**
   * Throws \Enm\Transformer\Event\ConfigurationEvent
   */
  const BEFORE_CHILD_CONFIGURATION = 'enm.transformer.event.before.child_configuration';


  /**
   * Throws \Enm\Transformer\Event\ConfigurationEvent
   */
  const AFTER_CHILD_CONFIGURATION = 'enm.transformer.event.after.child_configuration';

  /**
   * Throws \Enm\Transformer\Event\ConfigurationEvent
   */
  const AFTER_CONFIGURATION = 'enm.transformer.event.after.configuration';

  const AFTER_GLOBAL_VALUES = 'enm.transformer.event.after.global.values';

  /**
   * Throws \Enm\Transformer\Event\TransformerEvent
   */
  const BEFORE_RUN = 'enm.transformer.event.before.run';

  /**
   * Throws \Enm\Transformer\Event\TransformerEvent
   */
  const AFTER_RUN = 'enm.transformer.event.after.run';

  /**
   * Throws \Enm\Transformer\Event\ValidatorEvent
   */
  const BEFORE_VALIDATION = 'enm.transformer.event.before.validation';

  /**
   * Throws \Enm\Transformer\Event\ValidatorEvent
   */
  const AFTER_VALIDATION = 'enm.transformer.event.after.validation';

  /**
   * Throws \Enm\Transformer\Event\ValidatorEvent
   */
  const VALIDATE_STRING = 'enm.transformer.event.validate.string';

  /**
   * Throws \Enm\Transformer\Event\ValidatorEvent
   */
  const VALIDATE_INTEGER = 'enm.transformer.event.validate.integer';

  /**
   * Throws \Enm\Transformer\Event\ValidatorEvent
   */
  const VALIDATE_FLOAT = 'enm.transformer.event.validate.float';

  /**
   * Throws \Enm\Transformer\Event\ValidatorEvent
   */
  const VALIDATE_ARRAY = 'enm.transformer.event.validate.array';

  /**
   * Throws \Enm\Transformer\Event\ValidatorEvent
   */
  const VALIDATE_BOOL = 'enm.transformer.event.validate.bool';

  /**
   * Throws \Enm\Transformer\Event\ValidatorEvent
   */
  const VALIDATE_COLLECTION = 'enm.transformer.event.validate.collection';

  /**
   * Throws \Enm\Transformer\Event\ValidatorEvent
   */
  const VALIDATE_DATE = 'enm.transformer.event.validate.date';

  /**
   * Throws \Enm\Transformer\Event\ValidatorEvent
   */
  const VALIDATE_INDIVIDUAL = 'enm.transformer.event.validate.individual';

  /**
   * Throws \Enm\Transformer\Event\ValidatorEvent
   */
  const VALIDATE_OBJECT = 'enm.transformer.event.validate.object';


  /**
   * Throws \Enm\Transformer\Event\TransformerEvent
   */
  const PREPARE_INDIVIDUAL = 'enm.transformer.event.prepare.individual';

  /**
   * Throws \Enm\Transformer\Event\TransformerEvent
   */
  const PREPARE_ARRAY = 'enm.transformer.event.prepare.array';


  /**
   * Throws \Enm\Transformer\Event\TransformerEvent
   */
  const PREPARE_VALUE = 'enm.transformer.event.prepare.value';

  /**
   * Throws \Enm\Transformer\Event\TransformerEvent
   */
  const PREPARE_DEFAULT = 'enm.transformer.event.prepare.default';

  /**
   * Throws \Enm\Transformer\Event\TransformerEvent
   */
  const PREPARE_COLLECTION = 'enm.transformer.event.prepare.collection';

  /**
   * Throws \Enm\Transformer\Event\TransformerEvent
   */
  const PREPARE_OBJECT = 'enm.transformer.event.prepare.object';

  /**
   * Throws \Enm\Transformer\Event\TransformerEvent
   */
  const PREPARE_DATE = 'enm.transformer.event.prepare.date';

  /**
   * Throws \Enm\Transformer\Event\TransformerEvent
   */
  const REVERSE_TRANSFORM = 'enm.transformer.event.reverse.transform';

  /**
   * Throws \Enm\Transformer\Event\TransformerEvent
   */
  const REVERSE_COLLECTION = 'enm.transformer.event.reverse.collection';

  /**
   * Throws \Enm\Transformer\Event\TransformerEvent
   */
  const REVERSE_OBJECT = 'enm.transformer.event.reverse.object';

  /**
   * Throws \Enm\Transformer\Event\ClassBuilderEvent
   */
  const OBJECT_CREATE_INSTANCE = 'enm.transformer.event.object.create_instance';

  /**
   * Throws \Enm\Transformer\Event\ClassBuilderEvent
   */
  const OBJECT_RETURN_INSTANCE = 'enm.transformer.event.object.return_instance';

  /**
   * Throws \Enm\Transformer\Event\ConverterEvent
   */
  const CONVERT_TO_ARRAY = 'enm.transformer.event.convert.to_array';

  /**
   * Throws \Enm\Transformer\Event\ConverterEvent
   */
  const CONVERT_TO_OBJECT = 'enm.transformer.event.convert.to_object';

  /**
   * Throws \Enm\Transformer\Event\ConverterEvent
   */
  const CONVERT_TO_JSON = 'enm.transformer.event.convert.to_json';

  /**
   * Throws \Enm\Transformer\Event\ConverterEvent
   */
  const CONVERT_TO_PUBLIC = 'enm.transformer.event.convert.to_public';

  /**
   * Throws \Enm\Transformer\Event\ConverterEvent
   */
  const CONVERT_TO_STRING = 'enm.transformer.event.convert.to_string';
}
