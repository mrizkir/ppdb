<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class PasswordResetController extends Controller
{
    const EXPIRE_MINUTES = 60;

    const COOLDOWN_SECONDS = 60;

    /**
     * Kirim tautan reset password ke email akun yang terdaftar.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgot(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
        ], [
            'email.required' => 'Email mohon untuk diisi',
            'email.email' => 'Format email terdapat kesalahan',
        ]);

        $message = 'Jika email terdaftar, tautan reset password telah dikirim. Periksa kotak masuk dan folder spam.';

        $user = User::where('email', $request->input('email'))
            ->where('active', 1)
            ->first();

        if (! $user) {
            return response()->json([
                'status' => 1,
                'message' => $message,
            ], 200);
        }

        $recent = PasswordReset::where('user_id', $user->id)
            ->whereNull('used_at')
            ->where('created_at', '>=', Carbon::now()->subSeconds(self::COOLDOWN_SECONDS))
            ->exists();

        if ($recent) {
            return response()->json([
                'status' => 1,
                'message' => $message,
            ], 200);
        }

        $frontend = rtrim((string) env('FRONTEND_URL', ''), '/');
        if ($frontend === '') {
            \Log::error('FRONTEND_URL belum diatur. Email reset password tidak dikirim.');

            return response()->json([
                'status' => 0,
                'message' => 'Pengiriman email reset belum dapat dilakukan. Hubungi administrator.',
            ], 500);
        }

        PasswordReset::where('user_id', $user->id)->whereNull('used_at')->delete();

        $plain = bin2hex(random_bytes(32));
        PasswordReset::create([
            'id' => Uuid::uuid4()->toString(),
            'user_id' => $user->id,
            'token' => hash('sha256', $plain),
            'expires_at' => Carbon::now()->addMinutes(self::EXPIRE_MINUTES),
        ]);

        try {
            app()->mailer->to($user->email)->send(new ResetPasswordMail($user->name, $frontend.'/reset-password/'.$plain));
        } catch (\Throwable $e) {
            PasswordReset::where('user_id', $user->id)->whereNull('used_at')->delete();
            \Log::error('Gagal mengirim email reset password ke '.$user->email.': '.$e->getMessage());

            return response()->json([
                'status' => 0,
                'message' => 'Gagal mengirim email reset. Silakan coba lagi beberapa saat.',
            ], 500);
        }

        return response()->json([
            'status' => 1,
            'message' => $message,
        ], 200);
    }

    /**
     * Periksa apakah tautan reset masih berlaku.
     *
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function check($token)
    {
        if (! $this->findValidToken($token)) {
            return response()->json([
                'status' => 0,
                'message' => 'Tautan reset tidak berlaku atau sudah kedaluwarsa.',
            ], 422);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Tautan reset berlaku.',
        ], 200);
    }

    /**
     * Simpan password baru dari tautan reset.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        $this->validate($request, [
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'token.required' => 'Tautan reset tidak lengkap',
            'password.required' => 'Password baru mohon untuk diisi',
            'password.min' => 'Password baru minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak sama',
        ]);

        $reset = $this->findValidToken($request->input('token'));
        if (! $reset) {
            return response()->json([
                'status' => 0,
                'message' => 'Tautan reset tidak berlaku atau sudah kedaluwarsa.',
            ], 422);
        }

        $user = User::where('id', $reset->user_id)
            ->where('active', 1)
            ->first();

        if (! $user) {
            return response()->json([
                'status' => 0,
                'message' => 'Akun tidak ditemukan atau tidak aktif.',
            ], 422);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        $reset->used_at = Carbon::now();
        $reset->save();

        PasswordReset::where('user_id', $user->id)
            ->whereNull('used_at')
            ->delete();

        \App\Models\System\ActivityLog::log($request, [
            'object' => $user,
            'object_id' => $user->id,
            'user_id' => $user->id,
            'message' => 'Reset password akun ('.$user->username.') berhasil',
        ], 200);

        return response()->json([
            'status' => 1,
            'message' => 'Password berhasil diubah. Silakan login dengan password baru.',
        ], 200);
    }

    /**
     * @param string $plain
     * @return PasswordReset|null
     */
    private function findValidToken($plain)
    {
        if (! is_string($plain) || ! preg_match('/^[a-f0-9]{64}$/', $plain)) {
            return null;
        }

        return PasswordReset::where('token', hash('sha256', $plain))
            ->whereNull('used_at')
            ->where('expires_at', '>', Carbon::now())
            ->first();
    }
}
