<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\form;
use function Laravel\Prompts\info;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\password;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\table;
use function Laravel\Prompts\title;
use function Laravel\Prompts\warning;

class ResetUserPasswordCommand extends Command
{
    protected $signature = 'users:reset-password';

    protected $description = 'Search for a user and set a new password (Laravel Prompts)';

    public function handle(): int
    {
        title('Reset password — '.config('app.name'));

        intro('Find the account by typing part of their name or email, then set a new password they can use to sign in.');

        if (User::query()->count() === 0) {
            warning('There are no users in the database yet. Run users:create first.');

            return self::FAILURE;
        }

        $responses = form()
            ->search(
                label: 'Which user needs a new password?',
                options: fn (string $value): array => $this->matchingUsersForPrompt($value),
                placeholder: 'Type name or email…',
                hint: 'Matches appear as you type (up to 15). Leave empty to see the first accounts alphabetically.',
                name: 'user_id',
            )
            ->password(
                label: 'New password',
                placeholder: 'Minimum 8 characters',
                required: 'A password is required.',
                validate: ['password' => 'required|string|min:8'],
                hint: 'They will use this with their email on the next login.',
                name: 'password',
            )
            ->add(function (array $responses): string {
                return password(
                    label: 'Confirm new password',
                    placeholder: 'Repeat the same password',
                    required: 'Please confirm the password.',
                    validate: fn (string $value) => $value !== ($responses['password'] ?? '')
                        ? 'The passwords do not match.'
                        : null,
                );
            }, name: 'password_confirmation')
            ->submit();

        $userId = (int) $responses['user_id'];
        $user = User::query()->find($userId);

        if ($user === null) {
            error('That user no longer exists. Try again.');

            return self::FAILURE;
        }

        if (! confirm(
            label: 'Save this new password?',
            default: true,
            yes: 'Reset password',
            no: 'Cancel',
            hint: sprintf('%s · %s', $user->name, $user->email)
        )) {
            info('Password was not changed.');

            return self::SUCCESS;
        }

        spin(
            message: 'Updating password…',
            callback: function () use ($user, $responses): void {
                $user->update([
                    'password' => $responses['password'],
                ]);
            },
        );

        Log::info('User password reset via artisan', [
            'target_user_id' => $user->id,
            'reset_by_console' => true,
        ]);

        table(
            headers: ['Field', 'Value'],
            rows: [
                ['Name', $user->name],
                ['Email', $user->email],
                ['Role', $user->is_admin ? 'Admin' : 'Customer'],
                ['Password (copy now — shown once here)', $responses['password']],
            ],
        );

        info('Copy the password from the row above before you close this terminal. It is not logged or stored in plain text.');
        outro('Password updated.');
        warning('Share it over a secure channel when you can. Two-factor settings for this account are unchanged.');

        return self::SUCCESS;
    }

    /**
     * @return array<int|string, string>
     */
    private function matchingUsersForPrompt(string $value): array
    {
        $query = User::query()->orderBy('name')->limit(15);

        $trimmed = trim($value);
        if ($trimmed !== '') {
            $like = '%'.addcslashes($trimmed, '%_\\').'%';
            $query->where(function ($q) use ($like): void {
                $q->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like);
            });
        }

        return $query->get()->mapWithKeys(function (User $user): array {
            $role = $user->is_admin ? ' · admin' : '';

            return [
                $user->id => "{$user->name} <{$user->email}>{$role}",
            ];
        })->all();
    }
}
