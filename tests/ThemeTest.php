<?php

namespace App\Tests\Entity;

use App\Entity\Theme;
use PHPUnit\Framework\TestCase;

class ThemeTest extends TestCase
{
    public function testAddEntityTheme(): void
    {
        $theme = new Theme();
        $theme->setCode('environement');
        $theme->setIsSection(true);
        $theme->setParentId(null);
        $theme->setExternalId('2980');
        $this->assertSame('environement', $theme->getCode());
        $this->assertSame(true, $theme->getIsSection());
        $this->assertSame(null, $theme->getParentId());
        $this->assertSame('2980', $theme->getExternalId());
    }
}
