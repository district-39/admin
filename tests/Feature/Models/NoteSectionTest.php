<?php

use App\Models\DistrictMeeting;
use App\Models\NoteSection;

test('note section can be created', function () {
    $section = NoteSection::factory()->create();

    expect($section)->toBeInstanceOf(NoteSection::class)
        ->and($section->title)->toBeString()
        ->and($section->order)->toBeInt();
});

test('note section belongs to a district meeting', function () {
    $meeting = DistrictMeeting::factory()->create();
    $section = NoteSection::factory()->create(['district_meeting_id' => $meeting->id]);

    expect($section->districtMeeting->id)->toBe($meeting->id);
});

test('note section casts json field to array', function () {
    $data = ['key' => 'value', 'items' => [1, 2, 3]];
    $section = NoteSection::factory()->create(['json' => $data]);

    expect($section->json)->toBe($data);
});

test('note section casts order to integer', function () {
    $section = NoteSection::factory()->create(['order' => '5']);

    expect($section->order)->toBe(5);
});

test('note section nullable fields default to null', function () {
    $section = NoteSection::factory()->create();

    expect($section->committee)->toBeNull()
        ->and($section->json)->toBeNull()
        ->and($section->text)->toBeNull()
        ->and($section->markdown)->toBeNull();
});

test('deleting a district meeting cascades to note sections', function () {
    $meeting = DistrictMeeting::factory()->create();
    NoteSection::factory()->count(3)->create(['district_meeting_id' => $meeting->id]);

    $meeting->delete();

    expect(NoteSection::count())->toBe(0);
});
