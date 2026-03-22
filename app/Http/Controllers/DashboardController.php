<?php

namespace App\Http\Controllers;

use App\Enums\MeetingType;
use App\Models\Meeting;
use App\Models\Update;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $today = Carbon::today();

        $servicePositions = collect([
            MeetingType::District,
            MeetingType::Intergroup,
            MeetingType::Area,
        ])->map(function (MeetingType $type) use ($today) {
            $lastMeeting = Meeting::query()
                ->where('type', $type)
                ->where('_date', '<', $today)
                ->orderByDesc('_date')
                ->first();

            $nextMeeting = Meeting::query()
                ->where('type', $type)
                ->where('_date', '>=', $today)
                ->orderBy('_date')
                ->first();

            $userRole = auth()->user()->roles
                ->first(fn ($role) => str_contains(strtolower($role->name), strtolower($type->value)));

            return [
                'title' => $type->value === 'district' ? 'District Meeting' : ($type->value === 'intergroup' ? 'Intergroup Meeting' : 'Area Meeting'),
                'description' => 'Regular '.strtolower($type->value).' service meeting.',
                'role' => $userRole?->name,
                'nextMeeting' => $nextMeeting ? ['date' => $nextMeeting->_date->format('M j, Y')] : null,
                'lastMeeting' => $lastMeeting ? [
                    'date' => $lastMeeting->_date->format('M j, Y'),
                    'file_url' => null,
                ] : null,
            ];
        });

        return Inertia::render('Dashboard', [
            'updates' => Update::query()->latest('date')->limit(10)->get(),
            'servicePositions' => $servicePositions,
        ]);
    }
}
