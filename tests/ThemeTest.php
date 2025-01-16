<?php

namespace App\Tests\Entity;

use App\Entity\Theme;
use PHPUnit\Framework\TestCase;

class ThemeTest extends TestCase
{
    public function testSetAndgetParentId(): void
    {
        $theme = new Theme();
        $this->assertNull($theme->getParentId());
        $theme->setParentId(20);
        $this->assertSame(20, $theme->getParentId());
    }

    public function testGetIdTheme():void
    {
        $theme = new Theme();
        $this->assertNull($theme->getId());
        $this->assertSame(null, $theme->getId());
    }
    
}