<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class Admin
{
    /**
     
Handle an incoming request.*
@param  \Illuminate\Http\Request  $request
@param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
@return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
*/
public function handle(Request $request, Closure $next){

        $user = User::query()->where('remember_token', '=', $request->bearerToken())->first();

        if($user && $user->admin == true){
            return $next($request);
        }

        return response()->json(['message' => 'Forbidden for you'], 403);

    }
}