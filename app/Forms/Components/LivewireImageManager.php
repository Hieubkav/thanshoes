<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Component;
use Filament\Support\Concerns\HasExtraAlpineAttributes;

class LivewireImageManager extends Component
{
    use HasExtraAlpineAttributes;

    protected string $view = 'forms.components.livewire-image-manager';

    public static function make(string $name): static
    {
        return app(static::class, ['name' => $name]);
    }

    public function getProductId()
    {
        return $this->getRecord()?->id;
    }

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->dehydrated(false);
    }
}