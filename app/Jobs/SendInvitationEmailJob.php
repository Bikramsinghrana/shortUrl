<?php

namespace App\Jobs;

use App\Mail\InvitationMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendInvitationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $randomPassword,
        public string $companyName,
        public string $invitedByName
    ) {
    }

    public function handle(): void
    {
        Mail::to($this->user->email)->send(
            new InvitationMail(
                $this->user,
                $this->randomPassword,
                $this->companyName,
                $this->invitedByName
            )
        );
    }
}
