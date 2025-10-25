<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'captcha_answer' => [
                'required',
                function ($attribute, $value, $fail) {
                    $expectedAnswer = Session::pull('registration_captcha_answer');

                    if ($expectedAnswer === null || (int) $value !== (int) $expectedAnswer) {
                        $fail(__('The captcha response was incorrect.'));
                    }
                },
            ],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        $question = $this->generateCaptchaQuestion();

        Session::put('registration_captcha_answer', $question['answer']);

        return view('auth.register', [
            'captchaQuestion' => $question['prompt'],
        ]);
    }

    /**
     * Generate a simple arithmetic captcha question.
     *
     * @return array{prompt: string, answer: int}
     */
    protected function generateCaptchaQuestion()
    {
        $first = random_int(1, 9);
        $second = random_int(1, 9);

        return [
            'prompt' => __('What is :first + :second?', ['first' => $first, 'second' => $second]),
            'answer' => $first + $second,
        ];
    }
}
