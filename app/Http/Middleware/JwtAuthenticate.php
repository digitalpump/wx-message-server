<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/8
 * Time: 22:27
 */

namespace App\Http\Middleware;


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
            $id = $jwtAuth->authenticate();
            app('JwtUser')->setId($id);
        } catch (ExpiredException $e) {
            return $this->error(400,$e->getMessage());
        } catch (SignatureInvalidException $exception) {
            return $this->error(401,$exception->getMessage());
        } catch (BeforeValidException $exception) {
            return $this->error(402,$exception->getMessage());
        } catch (AuthHeaderNotFoundException $exception) {
            return $this->error(404,$exception->getMessage());
        } catch (AuthTokenEmptyException $exception) {
            return $this->error(405,$exception->getMessage());
        } catch (SubClaimNotFoundException $exception) {
            return $this->error(406,$exception->getMessage());
        } catch (\UnexpectedValueException $exception) {
            return $this->error(407,$exception->getMessage());
        }
        return $next($request);
    }
    private function error($code,$info="") {
        return response()->json(['info'=>$info,'code'=>$code]);
    }
}