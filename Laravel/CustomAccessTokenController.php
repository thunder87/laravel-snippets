<?php

namespace App\Http\Controllers\Vendor;

use App\User;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Passport\Http\Controllers\AccessTokenController;

class CustomAccessTokenController extends AccessTokenController
{
    /**
     * Custom accessTokenController to make it possible to pass data
     * together with the token generated using laravel passport.
     *
     * To make it work, replace oauth/token route @ issueToken with
     * this custom controller.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request
     * @return \Illuminate\Http\Response
     */
    public function issueToken(ServerRequestInterface $request)
    {
        try {
            $user = $this->getUser($request->getParsedBody()['username']);

            $tokenResponse = parent::issueToken($request);
            $content = $tokenResponse->getContent();
            $token = json_decode($content, true);

            // Append user to the token
            $token['user'] = $user;

            if (isset($token['error']))
                throw new OAuthServerException('The user credentials were incorrect.', 6, 'invalid_credentials', 401);

            return response()->json($token);

        } catch (ModelNotFoundException $e) {

            return response()->json([
                'message' => 'The user credentials were incorrect.',
                'error' => 'invalid_credentials'
            ], 500);

        } catch (OAuthServerException $e) {

            return response()->json([
                'message' => 'The user credentials were incorrect.',
                'error' => 'invalid_credentials'
            ], 500);

        } catch (Exception $e) {

            return response()->json(['message' => 'Internal server error'], 500);

        }
    }

    public function getUser($email)
    {
        return User::whereEmail($email)->firstOrFail();
    }
}
