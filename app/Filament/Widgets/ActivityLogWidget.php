<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Spatie\Activitylog\Models\Activity;

class ActivityLogWidget extends Widget
{
    protected static string $view = 'filament.widgets.activity-log-widget';

    protected static ?int $sort = 10;

    public function getRecords()
    {
        return Activity::latest()->limit(5)->get();
    }
}
