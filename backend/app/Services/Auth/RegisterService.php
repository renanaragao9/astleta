<?php

namespace App\Services\Auth;

use App\Jobs\SendEmailVerificationJob;
use App\Models\EmailVerificationCode;
use App\Models\Profile;
use App\Models\User;
use App\Utils\DateHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterService
{
    public function run(array $data): array
    {
        try {
            return DB::transaction(callback: function () use ($data): array {
                $data['date'] = DateHelper::normalizeDate($data['date']);
                $data['uuid'] = now()->format('YmdHis').rand(100, 999);
                $data['username'] = $this->generateUsername($data['name']);
                $data['password'] = Hash::make($data['password']);
                $data['type'] = 'normal';

                unset($data['password_confirmation']);

                $athleteProfile = Profile::where('name', 'athlete')->first();

                if ($athleteProfile) {
                    $data['profile_id'] = $athleteProfile->id;
                }

                $user = User::create($data);

                $verificationCode = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

                EmailVerificationCode::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'code' => $verificationCode,
                        'expires_at' => now()->addMinutes(15),
                    ]
                );

                dispatch(new SendEmailVerificationJob($user, $verificationCode));

                return [
                    'status' => 'success',
                    'message' => 'Usuário registrado com sucesso! Verifique seu e-mail para ativar sua conta.',
                    'data' => [
                        'user' => $user->fresh(['profile']),
                        'requires_verification' => true,
                    ],
                ];
            });
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erro ao registrar usuário: '.$e->getMessage(),
                'data' => [],
            ];
        }
    }

    private function generateUsername(string $name): string
    {
        $baseUsername = strtolower(str_replace(' ', '', $name));
        $username = $baseUsername;
        $counter = 1;

        while (User::query()->where('username', $username)->exists()) {
            $username = "{$baseUsername}{$counter}";
            $counter++;
        }

        return $username;
    }
}
