<?php

namespace Domain\User\Projectors;

use Domain\User\Events\ResendVerificationEvent;
use Domain\User\Events\VerifyUserEvent;
use Domain\User\Events\CreateUserEvent;
use Domain\User\Models\User;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;
use Spatie\EventProjector\Projectors\Projector;
use Spatie\EventProjector\Projectors\ProjectsEvents;

class UserProjector implements Projector
{
    use ProjectsEvents;

    public $handlesEvents = [
        CreateUserEvent::class => 'createUser',
        VerifyUserEvent::class => 'verifyUser',
        ResendVerificationEvent::class => 'resendVerification'
    ];

    public function createUser(CreateUserEvent $event): void
    {
        User::create([
            'uuid' => $event->user_uuid,
            'name' => $event->email,
            'email' => $event->email,
            'password' => $event->password_hash,
            'verification_token' => $this->generateVerificationToken($event->email),
        ]);
    }

    public function resendVerification(ResendVerificationEvent $event): void
    {
        $user = User::whereUuid($event->user_uuid)->firstOrFail();

        $user->verification_token = $this->generateVerificationToken($user->uuid);

        $user->save();
    }

    public function verifyUser(VerifyUserEvent $event): void
    {
        $user = User::whereUuid($event->user_uuid)->firstOrFail();

        $user->is_verified = true;

        $user->save();
    }

    private function generateVerificationToken(string $email): string
    {
        return sha1(Hash::make($email . (string) Uuid::uuid4()));
    }
}
