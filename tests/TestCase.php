<?php

namespace Tests;

use Illuminate\Http\Request;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Define routes setup.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function defineRoutes($router)
    {
        $router->post('eval', function (Request $request) {
            $code = sprintf('%s;', $request->input('code'));

            rescue(fn () => eval($code));

            return response()->noContent();
        });
    }
}
