<?php namespace App\Providers;

use Aws\S3\S3Client;

use Illuminate\Support\ServiceProvider;

use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Cached\Storage\Predis;
use League\Flysystem\Filesystem;

class S3ServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('League\Flysystem\Filesystem', function($app) {
            $client = S3Client::factory([
                'credentials' => [
                    'key'    => env('S3_KEY'),
                    'secret' => env('S3_SECRET'),
                ],
                'region' => env('S3_REGION', 'us-east-1'),
                'version' => 'latest',
            ]);

            $s3Adapter = new AwsS3Adapter($client, env('S3_BUCKET'));
            $cacheAdapter = new CachedAdapter($s3Adapter, new Predis());

            return new Filesystem($cacheAdapter);
        });
    }
}
