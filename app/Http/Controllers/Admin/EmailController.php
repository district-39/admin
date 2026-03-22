<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EmailStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEmailRequest;
use App\Http\Requests\Admin\UpdateEmailRequest;
use App\Models\Email;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class EmailController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/Emails/Index', [
            'emails' => Email::query()->latest()->paginate(20),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/Emails/Create');
    }

    public function store(StoreEmailRequest $request): RedirectResponse
    {
        Email::query()->create($request->validated());

        return to_route('admin.emails.index')->with('status', 'Email created.');
    }

    public function edit(Email $email): Response
    {
        return Inertia::render('admin/Emails/Edit', [
            'email' => $email,
        ]);
    }

    public function update(UpdateEmailRequest $request, Email $email): RedirectResponse
    {
        $email->update($request->validated());

        return to_route('admin.emails.index')->with('status', 'Email updated.');
    }

    public function destroy(Email $email): RedirectResponse
    {
        $email->delete();

        return to_route('admin.emails.index')->with('status', 'Email deleted.');
    }

    public function send(Email $email): RedirectResponse
    {
        $email->update([
            'status' => EmailStatus::Sent,
            'date_sent' => now(),
        ]);

        return to_route('admin.emails.index')->with('status', 'Email marked as sent.');
    }
}
