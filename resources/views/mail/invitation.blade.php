<x-mail::message>
# You've Been Invited

You've been invited to join **{{ config('app.name') }}** by {{ $invitation->inviter->name }}.

Click the button below to create your account. This link will expire in 7 days.

<x-mail::button :url="$signedUrl">
Create Your Account
</x-mail::button>

If you did not expect this invitation, no action is required.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
