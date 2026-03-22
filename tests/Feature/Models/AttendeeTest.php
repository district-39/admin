<?php

use App\Models\Attendee;
use App\Models\Note;
use App\Models\User;

test('attendee can be created with defaults', function () {
    $attendee = Attendee::factory()->create();

    expect($attendee)->toBeInstanceOf(Attendee::class)
        ->and($attendee->is_gsr)->toBeTrue()
        ->and($attendee->user_id)->toBeNull();
});

test('attendee belongs to a note', function () {
    $note = Note::factory()->create();
    $attendee = Attendee::factory()->create(['note_id' => $note->id]);

    expect($attendee->note->id)->toBe($note->id);
});

test('attendee can belong to a user', function () {
    $user = User::factory()->create();
    $attendee = Attendee::factory()->create(['user_id' => $user->id]);

    expect($attendee->user->id)->toBe($user->id);
});

test('attendee casts booleans correctly', function () {
    $attendee = Attendee::factory()->present()->create(['is_gsr' => false]);

    expect($attendee->is_present)->toBeTrue()
        ->and($attendee->is_gsr)->toBeFalse();
});

test('attendee present state works', function () {
    $attendee = Attendee::factory()->present()->create();

    expect($attendee->is_present)->toBeTrue();
});

test('attendee absent state works', function () {
    $attendee = Attendee::factory()->absent()->create();

    expect($attendee->is_present)->toBeFalse();
});
