<?php

namespace App\Http\Controllers;

use App\Enums\EventStatus;
use App\Http\Requests\SubmitEventRequest;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ScheduleController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $events = Event::query()
            ->where('status', EventStatus::Approved)
            ->orderBy('date')
            ->get();

        $pendingEvents = $user->isAdmin()
            ? Event::query()
                ->where('status', EventStatus::Pending)
                ->with('submittedBy')
                ->orderBy('date')
                ->get()
            : collect();

        return Inertia::render('Schedule', [
            'events' => $events,
            'pendingEvents' => $pendingEvents,
            'isAdmin' => $user->isAdmin(),
        ]);
    }

    public function store(SubmitEventRequest $request): RedirectResponse
    {
        Event::query()->create([
            ...$request->validated(),
            'status' => EventStatus::Pending,
            'submitted_by' => $request->user()->id,
        ]);

        return to_route('schedule.index')->with('status', 'Event submitted for review.');
    }

    public function approve(Event $event): RedirectResponse
    {
        $event->update(['status' => EventStatus::Approved]);

        return to_route('schedule.index');
    }

    public function reject(Event $event): RedirectResponse
    {
        $event->update(['status' => EventStatus::Rejected]);

        return to_route('schedule.index');
    }
}
