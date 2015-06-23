<?php namespace App\Http\Middleware;

use Aws\S3\Exception\S3Exception;
use Closure;
use Illuminate\Http\Response;
use Laravel\Lumen\Application;
use League\Flysystem\Filesystem;

class S3Middleware
{
    protected $fs;

    public function __construct(Filesystem $fs)
    {
        $this->fs  = $fs;
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
        // File path requested
        $path = $request->decodedPath();

        // Remove any extra slashes
        $path = preg_replace('#//+#', '/', $path);

        // If user requested / explicitly redirect them to index.html
        if ($path == '/') {
            return redirect('index.html', 301);
        }

        // Handle requests for missing files
        try {
            if (!$this->fs->has($path)) {
                abort(404);
            }
        } catch (S3Exception $e) {
            // AwsS3Adapter::has() should NOT throw this exception, but it does.
            // See https://github.com/thephpleague/flysystem-aws-s3-v3/issues/29
            if ($e->getResponse()->getStatusCode() == 403) {
                abort(404);
            }

            throw $e;
        }

        // If request is for directory, redirect to index.html in that directory
        $metaData = $this->fs->getMetadata($path);

        if ($metaData['type'] == 'dir') {
            $path .= '/index.html';

            return redirect($path, 301);
        }

        // Calculate ETag for caching
        $ETag = md5($path . $this->fs->getTimestamp($path));
        $rawETags = $request->getEtags();

        // Return 304 (Not Modified) if requested ETag matches for current file
        if (isset($rawETags[0])) {
            // Handle "weak" ETags
            $reqestETag = preg_replace('/^(?:W\/)?"([[:xdigit:]]{32})"/', '$1', $rawETags[0]);

            if ($ETag == $reqestETag) {
                return response(null)->setNotModified();
            }
        }

        return $next($request);
    }

}
