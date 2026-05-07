<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;

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

class CreateUserCommand extends Command
{
    protected $signature = 'users:create';

    protected $description = 'Interactively create a verified user (admin or customer) with Laravel Prompts';

    public function handle(): int
    {
        title('Create user — '.config('app.name'));

        intro('Add a verified account with a password. Admins get the full dashboard; customers can shop while signed in.');

        $responses = form()
            ->select(
                label: 'What role should this user have?',
                options: [
                    'customer' => 'Customer — storefront account (no /admin access)',
                    'admin' => 'Admin — catalog, orders, team & settings',
                ],
                default: 'customer',
                hint: 'Customer: browse & checkout only. Admin: full dashboard. Change later under Admin users if needed.',
                name: 'role',
            )
            ->text(
                label: 'Full name',
                placeholder: 'Alex K. Owusu',
                required: 'A name is required.',
                validate: ['name' => 'required|string|max:255'],
                hint: 'Shown in the admin, on confirmations, and wherever the app displays their name.',
                name: 'name',
                transform: fn (string $value) => trim($value),
            )
            ->text(
                label: 'Email address',
                placeholder: 'alex@example.com',
                required: 'Email is required.',
                validate: ['email' => 'required|email|max:255|unique:users,email'],
                hint: 'Must be unique. They will use this to sign in.',
                name: 'email',
                transform: fn (string $value) => trim(mb_strtolower($value)),
            )
            ->password(
                label: 'Password',
                placeholder: 'Minimum 8 characters',
                required: 'A password is required.',
                validate: ['password' => 'required|string|min:8'],
                hint: 'Share this securely with the user after creation.',
                name: 'password',
            )
            ->add(function (array $responses): string {
                return password(
                    label: 'Confirm password',
                    placeholder: 'Repeat the same password',
                    required: 'Please confirm the password.',
                    validate: fn (string $value) => $value !== ($responses['password'] ?? '')
                        ? 'The passwords do not match.'
                        : null,
                );
            }, name: 'password_confirmation')
            ->submit();

        $isAdmin = $responses['role'] === 'admin';

        if (! confirm(
            label: 'Create this user now?',
            default: true,
            yes: 'Create user',
            no: 'Cancel',
            hint: sprintf('%s · %s', $isAdmin ? 'Admin' : 'Customer', $responses['email'])
        )) {
            info('No user was created.');

            return self::SUCCESS;
        }

        try {
            $user = spin(
                message: 'Creating account…',
                callback: fn (): User => User::query()->create([
                    'name' => $responses['name'],
                    'email' => $responses['email'],
                    'password' => $responses['password'],
                    'is_admin' => $isAdmin,
                    'email_verified_at' => now(),
                ]),
            );
        } catch (QueryException $e) {
            error('Could not save the user. '.$e->getMessage());

            return self::FAILURE;
        }

        table(
            headers: ['Field', 'Value'],
            rows: [
                ['Name', $user->name],
                ['Email', $user->email],
                ['Role', $user->is_admin ? 'Admin' : 'Customer'],
                ['Email verified', 'Yes (set by this command)'],
                ['Password (copy now — shown once here)', $responses['password']],
            ],
        );

        info('Copy the password from the row above before you close this terminal. It is not logged or stored in plain text.');
        outro('User is ready to sign in.');
        if ($user->is_admin) {
            warning('Share credentials over a secure channel — avoid plain email when you can.');
        }

        return self::SUCCESS;
    }
}
