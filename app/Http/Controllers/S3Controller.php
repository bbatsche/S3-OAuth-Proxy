<?php namespace App\Http\Controllers;

use DateTime;
use GrahamCampbell\Flysystem\FlysystemManager as Filesystem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class S3Controller extends Controller
{
    protected $fs;

    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
    }

    public function getResource(Request $request, $path)
    {
        // Get date metadata and calculate ETag
        $timestamp   = $this->fs->getTimestamp($path);
        $ETag        = md5($path . $timestamp);
        $date        = new DateTime("@$timestamp");

        $response = new Response();
        $response->setLastModified($date)->setEtag($ETag, true)->setPublic();

        // Check modified date and Etag before attempting to retrieve actual content
        if ($response->isNotModified($request)) {
            return $response;
        }

        $content     = $this->fs->read($path);
        $contentType = $this->fs->getMimetype($path);

        return $response->header('Content-Type', $contentType)->setContent($content);
    }
}
