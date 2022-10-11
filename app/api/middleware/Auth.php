<?php
namespace app\api\middleware;

class Auth{
    public function handle($request, \Closure $next){


        return $next($request);
    }
}




