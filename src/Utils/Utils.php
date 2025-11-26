<?php

declare(strict_types=1);

namespace Jobberwocky\Utils;

use ReflectionObject;

class Utils {
  public static function objToArray($objeto) {
    $array = [];
    $reflector = new ReflectionObject($objeto);
    foreach ($reflector->getProperties() as $prop) {
      $prop->setAccessible(true);
      $array[$prop->getName()] = $prop->getValue($objeto);
    }   
    return $array;
  }
}