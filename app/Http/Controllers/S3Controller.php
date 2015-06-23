<?php namespace App\Http\Controllers;

use DateTime;
use League\Flysystem\Filesystem;
use Illuminate\Http\Request;

class S3Controller extends Controller
{
    protected $fs;

    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
    }

    public function getResource(Request $request, $path)
    {
        $content     = $this->fs->read($path);
        $contentType = $this->fs->getMimetype($path);
        $timestamp   = $this->fs->getTimestamp($path);

        $ETag = md5($path . $timestamp);
        $date = new DateTime("@$timestamp");

        return response($content)
            ->header('Content-Type', $contentType)
            ->setLastModified($date)
            ->setEtag($ETag, true)
            ->setPublic();
    }
}
