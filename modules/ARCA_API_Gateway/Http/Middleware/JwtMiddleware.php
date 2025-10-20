<?php

namespace Modules\ARCA_API_Gateway\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\ValidAt;
use DateTimeImmutable;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->hasHeader('Authorization')) {
            return response()->json(['message' => 'Authorization header not found'], 401);
        }

        $tokenString = $request->bearerToken();

        if (!$tokenString) {
            return response()->json(['message' => 'Token not provided'], 401);
        }

        $config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText(env('JWT_SECRET'))
        );

        try {
            $token = $config->parser()->parse($tokenString);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        $constraints = [
            new SignedWith($config->signer(), $config->signingKey()),
            new ValidAt(new \DateTimeImmutable()),
        ];

        if (!$config->validator()->validate($token, ...$constraints)) {
            return response()->json(['message' => 'Token validation failed'], 401);
        }

        // Add the user ID from the token claims to the request headers.
        $request->headers->set('X-Authenticated-User-Id', $token->claims()->get('uid'));

        return $next($request);
    }
}
