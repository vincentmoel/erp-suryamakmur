<?php

namespace App\Traits;

trait EnumLabel
{
  public static function getLabelByValue(int $value): ?string
  {
    return self::tryFrom($value)?->label();
  }
}
