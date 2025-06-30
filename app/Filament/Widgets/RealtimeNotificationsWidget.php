<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class RealtimeNotificationsWidget extends Widget
{
    protected static string $view = 'filament.widgets.realtime-notifications-widget';
    
    protected static ?int $sort = 0; // Hiển thị sau LiveTrackingWidget
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $pollingInterval = '7s';
}
