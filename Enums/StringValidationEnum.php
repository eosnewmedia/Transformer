<?php


namespace Enm\Transformer\Enums;

use Enm\Transformer\Traits\EnumTrait;

class StringValidationEnum
{

  use EnumTrait;

  const EMAIL = 'email';

  const URL = 'url';

  const IP = 'ip';
}
