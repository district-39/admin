<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Models\DistrictMeeting;
use App\Models\Note;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class NoteController extends Controller
{
    public function index(): Response
    {
        $notes = Note::query()
            ->with(['districtMeeting', 'attendees'])
            ->latest()
            ->get();

        return Inertia::render('secretary/districtNotes/Index', [
            'notes' => $notes,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('secretary/districtNotes/Create', [
            'districtMeetings' => DistrictMeeting::all(),
        ]);
    }

    public function store(StoreNoteRequest $request): RedirectResponse
    {
        $note = Note::query()->create($request->safe()->only(['district_meeting_id', 'file_id']));

        if ($request->has('attendees')) {
            $note->attendees()->createMany($request->validated('attendees'));
        }

        return to_route('notes.edit', $note)->with('status', 'Note created.');
    }

    public function edit(Note $note): Response
    {
        $note->load(['districtMeeting', 'attendees', 'file']);

        return Inertia::render('secretary/districtNotes/Edit', [
            'note' => $note,
            'districtMeetings' => DistrictMeeting::all(),
        ]);
    }

    public function update(UpdateNoteRequest $request, Note $note): RedirectResponse
    {
        $note->update($request->safe()->only(['district_meeting_id', 'file_id']));

        if ($request->has('attendees')) {
            $note->attendees()->delete();
            $note->attendees()->createMany($request->validated('attendees'));
        }

        return to_route('notes.edit', $note)->with('status', 'Note updated.');
    }

    public function destroy(Note $note): RedirectResponse
    {
        $note->delete();

        return to_route('notes.index')->with('status', 'Note deleted.');
    }
}
