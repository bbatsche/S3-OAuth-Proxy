<?php

return array(
    'github' => array(
        'client_id' => env('GITHUB_KEY'),
        'client_secret' => env('GITHUB_SECRET'),
        'redirect' => url('github')
    )
);
