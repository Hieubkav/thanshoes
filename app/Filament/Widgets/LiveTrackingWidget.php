<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class LiveTrackingWidget extends Widget
{
    protected static string $view = 'filament.widgets.live-tracking-widget';
    
    protected static ?int $sort = -1; // Hiển thị đầu tiên
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $pollingInterval = '3s';
}
