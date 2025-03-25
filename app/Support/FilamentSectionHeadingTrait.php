<?php

namespace App\Support;

use Illuminate\Support\Str;

trait FilamentSectionHeadingTrait
{
    protected function initializeComponent()
    {
        parent::initializeComponent();

        $this->heading = is_string($this->heading) ? $this->heading : (string) $this->heading;
    }
}