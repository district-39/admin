<?php

use App\Models\Attendee;
use App\Models\DistrictMeeting;
use App\Models\Note;
use App\Models\Role;
use App\Models\User;

function createUserWithRole(string $slug, string $name = ''): User
{
    $user = User::factory()->create();
    $role = Role::factory()->create(['slug' => $slug, 'name' => $name ?: $slug]);
    $user->roles()->attach($role);

    return $user;
}

// --- Authorization ---

test('unauthenticated user cannot access notes', function () {
    $this->get('/notes')->assertRedirect('/login');
    $this->get('/notes/create')->assertRedirect('/login');
    $this->post('/notes')->assertRedirect('/login');
});

test('user without notes role gets 403', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/notes')
        ->assertForbidden();
});

test('secretary can access notes index', function () {
    $user = createUserWithRole('dsecretary', 'District Secretary');

    $this->actingAs($user)
        ->get('/notes')
        ->assertSuccessful();
});

test('notetaker can access notes index', function () {
    $user = createUserWithRole('notetaker', 'Notetaker');

    $this->actingAs($user)
        ->get('/notes')
        ->assertSuccessful();
});

// --- Index ---

test('notes index returns notes with attendees', function () {
    $user = createUserWithRole('dsecretary', 'District Secretary');
    $note = Note::factory()->create();
    Attendee::factory()->count(2)->create(['note_id' => $note->id]);

    $this->actingAs($user)
        ->get('/notes')
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('secretary/districtNotes/Index')
            ->has('notes', 1)
            ->has('notes.0.attendees', 2)
        );
});

// --- Create ---

test('notes create page renders', function () {
    $user = createUserWithRole('dsecretary', 'District Secretary');

    $this->actingAs($user)
        ->get('/notes/create')
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('secretary/districtNotes/Create')
            ->has('districtMeetings')
        );
});

// --- Store ---

test('user can create a note without a meeting', function () {
    $user = createUserWithRole('dsecretary', 'District Secretary');

    $this->actingAs($user)
        ->post('/notes', [
            'district_meeting_id' => null,
            'file_id' => null,
        ])
        ->assertRedirect();

    expect(Note::count())->toBe(1);
});

test('user can create a note with a district meeting', function () {
    $user = createUserWithRole('dsecretary', 'District Secretary');
    $meeting = DistrictMeeting::factory()->create();

    $this->actingAs($user)
        ->post('/notes', [
            'district_meeting_id' => $meeting->id,
        ])
        ->assertRedirect();

    expect(Note::first()->district_meeting_id)->toBe($meeting->id);
});

test('user can create a note with attendees', function () {
    $user = createUserWithRole('notetaker', 'Notetaker');

    $this->actingAs($user)
        ->post('/notes', [
            'attendees' => [
                [
                    'email' => 'test@example.com',
                    'is_present' => true,
                    'is_gsr' => true,
                    'title' => 'GSR',
                ],
                [
                    'email' => 'other@example.com',
                    'is_present' => false,
                    'is_gsr' => false,
                    'title' => null,
                ],
            ],
        ])
        ->assertRedirect();

    expect(Note::count())->toBe(1)
        ->and(Attendee::count())->toBe(2);
});

test('store validates district_meeting_id exists', function () {
    $user = createUserWithRole('dsecretary', 'District Secretary');

    $this->actingAs($user)
        ->post('/notes', [
            'district_meeting_id' => 9999,
        ])
        ->assertSessionHasErrors('district_meeting_id');
});

test('store validates attendee email format', function () {
    $user = createUserWithRole('dsecretary', 'District Secretary');

    $this->actingAs($user)
        ->post('/notes', [
            'attendees' => [
                [
                    'email' => 'not-an-email',
                    'is_present' => true,
                    'is_gsr' => true,
                ],
            ],
        ])
        ->assertSessionHasErrors('attendees.0.email');
});

// --- Edit ---

test('notes edit page renders with note data', function () {
    $user = createUserWithRole('dsecretary', 'District Secretary');
    $note = Note::factory()->create();
    Attendee::factory()->create(['note_id' => $note->id]);

    $this->actingAs($user)
        ->get("/notes/{$note->id}/edit")
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('secretary/districtNotes/Edit')
            ->has('note')
            ->has('note.attendees', 1)
            ->has('districtMeetings')
        );
});

// --- Update ---

test('user can update a note', function () {
    $user = createUserWithRole('dsecretary', 'District Secretary');
    $note = Note::factory()->create();
    $meeting = DistrictMeeting::factory()->create();

    $this->actingAs($user)
        ->put("/notes/{$note->id}", [
            'district_meeting_id' => $meeting->id,
        ])
        ->assertRedirect();

    expect($note->fresh()->district_meeting_id)->toBe($meeting->id);
});

test('update replaces attendees', function () {
    $user = createUserWithRole('dsecretary', 'District Secretary');
    $note = Note::factory()->create();
    Attendee::factory()->count(3)->create(['note_id' => $note->id]);

    $this->actingAs($user)
        ->put("/notes/{$note->id}", [
            'attendees' => [
                [
                    'email' => 'new@example.com',
                    'is_present' => true,
                    'is_gsr' => true,
                ],
            ],
        ])
        ->assertRedirect();

    expect($note->attendees()->count())->toBe(1)
        ->and($note->attendees()->first()->email)->toBe('new@example.com');
});

// --- Destroy ---

test('user can delete a note', function () {
    $user = createUserWithRole('dsecretary', 'District Secretary');
    $note = Note::factory()->create();

    $this->actingAs($user)
        ->delete("/notes/{$note->id}")
        ->assertRedirect('/notes');

    expect(Note::count())->toBe(0);
});
