<?php

namespace App\Http\Controllers\Sentinel;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Sentinel;
use Mail;
use Activation;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Reminder;

class SentinelController extends Controller
{
	protected $redirectTo = '/';

	protected function register(Request $request) {
		$this->validate($request, [
			'name' => 'required|max:255',
			'email' => 'required|email|max:255|unique:users',
			'password' => 'required|between:6,255|confirmed',
		]);

		$credentials = [
			'first_name' => $request['name'],
			'email' => $request['email'],
			'password' => $request['password'],
		];
		$user = Sentinel::register($credentials);
		$activation = Activation::create($user);
		$this->sendActivationCode($user, $activation->code);
		return redirect('login')->with('info', trans('sentinel.after_register'));
	}

	private function sendActivationCode($user, $code) {
		Mail::send('sentinel.emails.activation', [
			'user' => $user,
			'code' => $code,
		], function($m) use ($user) {
			$m->from(config('app.activation_from'), config('app.appname'));
			$m->to($user->email, $user->name)->subject(trans('sentinel.activate_title'));
		});
	}

	protected function activate(Request $request) {
		$user = Sentinel::findByCredentials(['email' => base64_decode($request->email)]);
		if (is_null($user)) {
			return redirect('login')->with(['myerror' => trans('sentinel.invalid_activation_params')]);
		}
		if (Activation::completed($user)) {
			return redirect('login');
		}
		if (!Activation::complete($user, $request->code)) {
			return redirect('login')->with(['myerror' => trans('sentinel.invalid_activation_params')]);
		}
		return redirect('login')->with(['info' => trans('sentinel.activation_done')]);
	}

	protected function login(Request $request) {
		$this->validate($request, [
			'email' => 'required|email|max:255',
			'password' => 'required|between:6,255',
			'remember' => 'boolean',
		]);
		try {
			$this->userInterface = Sentinel::authenticate([
				'email' => $request['email'],
				'password' => $request['password']
			], $request['remember']);
		} catch (NotActivatedException $notactivated) {
			return view('auth.login', [
				'myerror' => trans('sentinel.not_activation'),
				'resend_code' => $request['email'],
			]);
		} catch (ThrottlingException $throttling) {
			return view('auth.login', [
				'myerror' => trans('sentinel.login_throttling')."[あと".$throttling->getDelay()."秒]"
			]);
		}
		if (!$this->userInterface) {
			return view('auth.login', [
				'myerror' => trans('sentinel.login_failed')
			]);
		}
		return redirect($this->redirectTo);
	}

	protected function resendActivationCode(Request $request) {
		Activation::removeExpired();
		$user = Sentinel::findByCredentials(['email' => base64_decode($request->email)]);
		if (is_null($user)) {
			return redirect('login')->with(['myerror' => trans('sentinel.invalid_activation_params')]);
		}
		if (Activation::completed($user)) {
			return redirect('login')->with(['info' => trans('sentinel.activation_done')]);
		}
		$exists = Activation::exists($user);
		if (!$exists) {
			$activation = Activation::create($user);
		}
		else {
			$activation = $exists;
		}
		$this->sendActivationCode($user, $activation->code);
		return redirect('login')->with('info', trans('sentinel.after_register'));
	}

	protected function sendResetPassword(Request $request) {
		Reminder::removeExpired();
		$this->validate($request, [
			'email' => 'required|email|max:255',
			'password' => 'required|between:6,255|confirmed',
		]);
		$user = Sentinel::findByCredentials(['email'=>$request->email]);
		if (is_null($user)) {
			return redirect('login')->with(['info'=>trans('sentinel.password_reset_sent').__LINE__]);
		}
		$code = "";
		$exists = Reminder::exists($user);
		if ($exists) {
			$code = $exists->code;
		}
		else {
			$reminder = Reminder::create($user);
			$code = $reminder->code;
		}
		Mail::send('sentinel.emails.reminder', [
			'user' => $user,
			'code' => $code,
			'password' => $request->password,
		], function($m) use ($user) {
			$m->from(config('app.activation_from'), config('app.appname'));
			$m->to($user->email, $user->name)->subject(trans('sentinel.reminder_title'));
		});
		return redirect('login')->with(['info'=>trans('sentinel.password_reset_sent')]);
	}

	protected function resetPassword(Request $request) {
		$email = substr(base64_decode($request->email), 0, 255);
		$code = substr($request->code, 0, 64);
		$passwd = substr(base64_decode($request->password), 0,255);
		$user = Sentinel::findByCredentials(['email' => $email]);
		if (is_null($user)) {
			return redirect('login')->with('info', trans('sentinel.password_reset_done'));
		}
		if (Reminder::complete($user, $code, $passwd)) {
			return redirect('login')->with('info', trans('sentinel.password_reset_done'));
		}
		return redirect('login')->with('info', trans('sentinel.password_reset_failed'));
	}

	protected function logout(Request $request) {
		Sentinel::logout();
		return redirect($this->redirectTo);
	}
}
