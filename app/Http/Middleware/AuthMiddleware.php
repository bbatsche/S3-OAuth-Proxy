<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard as Auth;
use Psr\Log\LoggerInterface as Log;

class AuthMiddleware
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    protected $logger;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Auth $auth, Log $logger)
    {
        $this->auth   = $auth;
        $this->logger = $logger;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest()) {
            if ($request->ajax()) {
                $this->logger->error('Authentication failed: Ajax request?!', array('url' => $request->path()));

                return response('Unauthorized.', 401);
            } else {
                $request->session()->flash('intended_url', $request->path());

                return redirect('login');
            }
        }

        return $next($request);
    }
}
