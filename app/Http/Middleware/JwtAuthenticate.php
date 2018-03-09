<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/8
 * Time: 22:27
 */

namespace App\Http\Middleware;


use App\Common\Tools\HttpStatusCode;
use App\Common\Tools\Jwt\AuthHeaderNotFoundException;
use App\Common\Tools\Jwt\AuthTokenEmptyException;
use App\Common\Tools\Jwt\JwtAuth;
use App\Common\Tools\Jwt\SubClaimNotFoundException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Closure;

class JwtAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $jwtAuth = new JwtAuth($request);
        try {
            $payload = $jwtAuth->authenticate();
            $id = $payload->sub;
            app('JwtUser')->setId($id);
        } catch (ExpiredException $e) {
            return $this->error(HttpStatusCode::REQUEST_TIMEOUT,$e->getMessage());
        } catch (SignatureInvalidException $exception) {
            return $this->error(HttpStatusCode::UNAUTHORIZED,$exception->getMessage());
        } catch (BeforeValidException $exception) {
            return $this->error(HttpStatusCode::BAD_REQUEST,$exception->getMessage());
        } catch (AuthHeaderNotFoundException $exception) {
            return $this->error(HttpStatusCode::BAD_REQUEST,$exception->getMessage());
        } catch (AuthTokenEmptyException $exception) {
            return $this->error(HttpStatusCode::BAD_REQUEST,$exception->getMessage());
        } catch (SubClaimNotFoundException $exception) {
            return $this->error(HttpStatusCode::BAD_REQUEST,$exception->getMessage());
        } catch (\UnexpectedValueException $exception) {
            return $this->error(HttpStatusCode::BAD_REQUEST,$exception->getMessage());
        }
        return $next($request);
    }
    private function error($code,$info="") {
        return response()->json(["message"=>$info],$code);
    }
}