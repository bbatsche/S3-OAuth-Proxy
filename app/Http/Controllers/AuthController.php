<?php namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Socialite\Contracts\Factory;

class AuthController extends Controller
{
    protected $github;

    public function __construct(Factory $social)
    {
        $this->github = $social->driver('github');
    }

    public function getLogin()
    {
        return $this->github->redirect();
    }

    public function getGithub(Request $request)
    {
        if (!$request->has('code')) {
            return redirect('login');
        }

        $ghUser = $this->github->user();

        try {
            $user = User::where('github_id', $ghUser->getId())
                ->orWhere('github_username', $ghUser->getNickname())
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            abort(403);
        }

        $user->github_id       = $ghUser->getId();
        $user->github_username = $ghUser->getNickname();
        $user->name            = $ghUser->getName();
        $user->email           = $ghUser->getEmail();
        $user->github_icon     = $ghUser->getAvatar();

        $user->save();

        $auth = app('auth');

        $auth->login($user, true);

        return redirect('index.html');
    }
}
