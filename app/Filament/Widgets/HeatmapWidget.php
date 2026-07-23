<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Suffragan;

class HeatmapWidget extends Widget
{
    protected static string $view = 'filament.widgets.heatmap-widget';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return auth()->user()->hasRole(['super-admin', 'super_admin', 'coordinador']);
    }

    public function getLocations(): array
    {
        // Get all suffragans with coordinates
        $query = Suffragan::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', '')
            ->where('longitude', '!=', '');

        $totalCount = $query->count();
        \Illuminate\Support\Facades\Log::info("HeatmapWidget: Total suffragans with coords: " . $totalCount);

        $suffragans = $query->get(['id', 'latitude', 'longitude', 'voter_type', 'name', 'lastname', 'photo', 'documentationnumber', 'email', 'phone'])
            ->map(function ($suffragan) {
                // Map vote intent to colors
                $color = match ($suffragan->voter_type) {
                    'Duro' => '#22c55e', // Green
                    'Blando' => '#eab308', // Yellow
                    'Opinión' => '#3b82f6', // Blue
                    default => '#6b7280', // Gray
                };

                $photoUrl = $suffragan->photo ? "/storage/{$suffragan->photo}" : "/img/default-avatar.png";
                $phoneFormatted = preg_replace('/[^0-9]/', '', $suffragan->phone ?? '');
                
                $fallbackImage = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' /></svg>";

                $html = '<div style="text-align:center;">';
                $html .= '<div style="width: 80px; height: 80px; border-radius: 50%; border: 2px solid #ccc; margin: 0 auto 10px auto; overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: #f3f4f6;">';
                $html .= '<img src="' . htmlspecialchars($photoUrl) . '" alt="' . htmlspecialchars($suffragan->name ?? '') . '" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src=\'' . htmlspecialchars($fallbackImage) . '\'">';
                $html .= '</div>';
                $html .= '<strong>' . htmlspecialchars(($suffragan->name ?? '') . ' ' . ($suffragan->lastname ?? '')) . '</strong>';
                $html .= '</div>';
                
                $html .= '<div style="font-size: 13px; margin-top: 10px;">';
                $html .= '<strong>Cédula: </strong>' . htmlspecialchars($suffragan->documentationnumber ?? 'N/A') . '<br>';
                $html .= '<strong>Correo: </strong><a href="mailto:' . htmlspecialchars($suffragan->email ?? '') . '">' . htmlspecialchars($suffragan->email ?? 'N/A') . '</a><br>';
                
                $phoneDisplay = $suffragan->phone ?? 'N/A';
                if ($phoneDisplay !== 'N/A' && $phoneFormatted) {
                    $html .= '<strong>WhatsApp: </strong><a href="https://wa.me/57' . htmlspecialchars($phoneFormatted) . '" target="_blank">' . htmlspecialchars($phoneDisplay) . '</a><br>';
                } else {
                    $html .= '<strong>WhatsApp: </strong>' . htmlspecialchars($phoneDisplay) . '<br>';
                }
                
                $html .= '</div>';
                $html .= '<hr style="margin: 8px 0;">';
                $html .= '<strong>Categoría: </strong>Voto: ' . htmlspecialchars($suffragan->voter_type ?? 'N/A');

                return [
                    'lat' => (float)$suffragan->latitude,
                    'lng' => (float)$suffragan->longitude,
                    'color' => $color,
                    'popup' => $html
                ];
            })->toArray();

        return $suffragans;
    }
}
