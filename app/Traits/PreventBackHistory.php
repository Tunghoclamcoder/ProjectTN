<?php

namespace App\Traits;
trait PreventBackHistory
{
    protected function preventBackHistory($response)
    {
        if (method_exists($response, 'header')) {
            return $response->header('Cache-Control','no-cache, no-store, max-age=0, must-revalidate')
                ->header('Pragma','no-cache')
                ->header('Expires','Sun, 02 Jan 1990 00:00:00 GMT');
        }
        return $response;
    }
}
