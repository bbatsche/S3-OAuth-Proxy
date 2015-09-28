<?php namespace App\Http\Controllers;

use App\User;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Laravel\Socialite\Contracts\Factory as Socialite;
use Psr\Log\LoggerInterface as Log;

class AuthController extends Controller
{
    protected $github;

    public function __construct(Socialite $social)
    {
        $this->github = $social->driver('github');
    }

    public function getLogin(Request $request)
    {
        $request->session()->reflash();

        return $this->github->redirect();
    }

    public function getLogout(Auth $auth)
    {
        $auth->logout();

        return redirect('http://codeup.com');
    }

    public function getGithub(Request $request, Auth $auth, Log $logger)
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
            $logInfo = array(
                'id'       => $ghUser->getId(),
                'username' => $ghUser->getNickname(),
                'email'    => $ghUser->getEmail()
            );

            $logger->warning('GitHub authentication failed: no user found!', $logInfo);

            abort(401);
        }

        $user->github_id       = $ghUser->getId();
        $user->github_username = $ghUser->getNickname();
        $user->name            = $ghUser->getName();
        $user->email           = $ghUser->getEmail();
        $user->github_icon     = $ghUser->getAvatar();

        $user->save();

        $auth->login($user, true);

        return redirect($request->session()->get('intended_url', 'index.html'));
    }
}
