<?php namespace App\Http\Middleware;

use Aws\S3\Exception\S3Exception;
use Closure;
use GrahamCampbell\Flysystem\FlysystemManager as Filesystem;

class S3Middleware
{
    protected $fs;

    public function __construct(Filesystem $fs)
    {
        $this->fs  = $fs;
    }

    /**
     * Return 404s for missing files and redirect to index.html for directories
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // File path requested
        $path = $request->decodedPath();

        // Remove any repeated slashes
        $path = preg_replace('#//+#', '/', $path);

        // If user requested / explicitly redirect them to index.html
        if ($path == '/') {
            return redirect('index.html', 301);
        }

        // Handle requests for missing files
        if (!$this->fs->has($path)) {
            abort(404);
        }

        // If request is for directory, redirect to index.html in that directory
        $metaData = $this->fs->getMetadata($path);

        if ($metaData['type'] == 'dir') {
            $path .= '/index.html';

            return redirect($path, 301);
        }

        // Forward to Controller for content & modified time
        return $next($request);
    }
}
