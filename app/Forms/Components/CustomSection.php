<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Section as BaseSection;

class CustomSection extends BaseSection
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->heading = is_string($this->heading) ? $this->heading : (string) $this->heading;
    }
}