<?php

use App\Models\Attendee;
use App\Models\DistrictMeeting;
use App\Models\Note;

test('note can be created', function () {
    $note = Note::factory()->create();

    expect($note)->toBeInstanceOf(Note::class)
        ->and($note->district_meeting_id)->toBeNull()
        ->and($note->file_id)->toBeNull();
});

test('note can belong to a district meeting', function () {
    $meeting = DistrictMeeting::factory()->create();
    $note = Note::factory()->forMeeting($meeting)->create();

    expect($note->districtMeeting->id)->toBe($meeting->id);
});

test('note has many attendees', function () {
    $note = Note::factory()->create();
    Attendee::factory()->count(3)->create(['note_id' => $note->id]);

    expect($note->attendees)->toHaveCount(3);
});

test('deleting a note cascades to attendees', function () {
    $note = Note::factory()->create();
    Attendee::factory()->count(2)->create(['note_id' => $note->id]);

    $note->delete();

    expect(Attendee::count())->toBe(0);
});
