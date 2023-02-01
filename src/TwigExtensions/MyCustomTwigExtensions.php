<?php

namespace App\TwigExtensions;

use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

class MyCustomTwigExtensions extends AbstractExtension
{

  public function getFilters()
  {
      return [
        new TwigFilter('defaultImage', [$this, 'defaultImage'])
      ];
  }
 
  public function defaultImage(string $path): string
  {
    if (strlen(trim($path)) == 0) {
        return 'avatar.jpg';
    }
    return $path;
  }
}
