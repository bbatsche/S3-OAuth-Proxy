<?php namespace App\Http\Controllers;

use DateTime;
use GrahamCampbell\Flysystem\FlysystemManager as Filesystem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Psr\Log\LoggerInterface as Log;
use Symfony\Component\HttpFoundation\ResponseHeaderBag as HeaderBag;

class S3Controller extends Controller
{
    protected $fs;

    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
    }

    public function getResource(Request $request, Log $logger, $path)
    {
        // Get date metadata and calculate ETag
        $timestamp = $this->fs->getTimestamp($path);
        $ETag      = md5($path . $timestamp);
        $date      = new DateTime("@$timestamp");

        $response = new Response();
        $response->setLastModified($date)->setEtag($ETag, true)->setPublic();

        // Check modified date and Etag before attempting to retrieve actual content
        if ($response->isNotModified($request)) {
            return $response;
        }

        $content = $this->fs->read($path);

        if (!$content) {
            // We had a false positive from the cache!!
            $this->deleteFromCache($path);

            $logger->warning('File not found (cache false positive)', array('path' => $path));

            abort(404);
        }

        $contentType = $this->fs->getMimetype($path);

        if (env('EXAMPLE_DIR') && starts_with($path, env('EXAMPLE_DIR'))) {
            // Path is within our "examples" directory, force the file to be downloaded

            $disposition = $response->headers->makeDisposition(HeaderBag::DISPOSITION_ATTACHMENT, basename($path));

            $response->header('Content-Disposition', $disposition);
        }

        return $response->header('Content-Type', $contentType)->setContent($content);
    }

    protected function deleteFromCache($path)
    {
        // Feels a bit more complex than it should be, but at
        // least we're not having to reverse engineer the cache

        $factory = app('flysystem.cachefactory');
        $config  = config('flysystem.cache.default');

        $cache = $factory->make($config, $this->fs);

        $cache->storeMiss($path);
    }
}
