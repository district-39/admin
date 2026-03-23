<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInvitationRequest;
use App\Mail\InvitationMail;
use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class InvitationController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/invitations/Index', [
            'invitations' => Invitation::with('inviter')->latest()->get(),
        ]);
    }

    public function store(StoreInvitationRequest $request): RedirectResponse
    {
        $invitation = Invitation::create([
            'email' => $request->validated('email'),
            'invited_by' => $request->user()->id,
        ]);

        Mail::to($invitation->email)->send(new InvitationMail($invitation));

        return redirect()->route('admin.invitations.index');
    }

    public function destroy(Invitation $invitation): RedirectResponse
    {
        if ($invitation->isAccepted()) {
            return redirect()->route('admin.invitations.index')->withErrors([
                'invitation' => 'Cannot delete an accepted invitation.',
            ]);
        }

        $invitation->delete();

        return redirect()->route('admin.invitations.index');
    }
}
