<?php

namespace App\Console\Commands;

use App\Mail\InvitationMail;
use App\Models\Company;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendInvitationEmails extends Command
{
    protected $signature = 'mail:send-invitations';
    protected $description = 'Send invitation emails via cron job';

    public function handle()
    {
        Log::info('SendInvitationEmails started');

        $users = User::where('invitation_status', '0')->get();

        // Log::info('Users found', ['count' => $users->count()]);

        foreach ($users as $newUser) {
            
            $companyId   = $newUser->company_id;
            $companyName = Company::find($companyId)?->name ?? 'N/A';
            $adminName   = $newUser->name ?? 'Admin';
            $randomPassword = "password";

            // we can also use -> send(), queue() for later, after() etc.  +++++

            Mail::to($newUser->email)->send(
                new InvitationMail(
                    $newUser,
                    $randomPassword,
                    $companyName,
                    $newUser->name
                )
            );


            // need to run cueue command for later +++
            // php artisan queue:work --tries=3
            $delayTime = now()->addMinutes(2);
            // Mail::to($newUser->email)->later($delayTime, new InvitationMail($newUser,$randomPassword, $companyName,$newUser->name));

            // ✅ ONLY UPDATE AFTER MAIL SENT
            $newUser->update([
                'invited_at' => now(),
                'invitation_status' => '1', // sent
            ]);
        }

        $this->info('Invitation emails sent successfully.');
    }
}
