<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Notifications\toFactCode;
use Twilio\Rest\Client;

class LoginRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'         => [ 'required', 'string', 'email'],
            'password'      => [ 'required', 'string' ],
        ];
    }


    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt( $this->only('email' , 'password' ), $this->boolean('remember') ) ) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // generate code when user login
        $user = User::where( 'email' , $this->input('email') )->first();
        $user->generateCode();

        // Send code to Email
        // $user->notify( new toFactCode() );
        $message        = 'Confirm Code ' . $user->code;
        $acount_sid     = getenv('TWILIO_SID');
        $acount_token   = getenv('TWILIO_TOKEN');
        $number         = getenv('TWILIO_FROM');
        $clinet         = new Client( $acount_sid , $acount_token );
        $clinet->messages->create('+201092607753', [
            'from'  => $number,
            'body'  => $message ]);

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')).'|'.$this->ip());
    }
}
