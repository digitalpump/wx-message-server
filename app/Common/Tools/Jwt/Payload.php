<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/11
 * Time: 09:35
 */

namespace App\Common\Tools\Jwt;

use Illuminate\Support\Str;
use Carbon\Carbon;
abstract class Payload implements IPayload
{

    /**
     * @var int
     */
    protected $ttl = 60;
    /**
     * @var array
     */
    protected $claims = [];

    public function getClaims()
    {
        return $this->claims;
    }


    abstract function getDefaultClaims();

    /**
     * Add a claim to the Payload.
     *
     * @param  string  $name
     * @param  mixed   $value
     * @return $this
     */
    public function addClaim($name, $value)
    {
        $this->claims[$name] = $value;

        return $this;
    }
    /**
     * Build the default claims.
     *
     * @param  array  $customClaims
     * @return $this
     */
    public function buildClaims(array $customClaims)
    {
        // add any custom claims first
        $this->addClaims($customClaims);

        foreach ($this->defaultClaims as $claim) {
            if (! array_key_exists($claim, $customClaims)) {
                $this->addClaim($claim, $this->$claim());
            }
        }

        return $this;
    }



    public function iat() {
        return Carbon::now()->timestamp;
    }

    public function exp() {
        return Carbon::now()->addMinutes($this->getTTL())->timestamp;
    }

    public function nbf() {
        return Carbon::now()->timestamp;
    }

    protected function jti() {
        return Str::random();
    }
    /**
     * Set the token ttl (in minutes).
     *
     * @param  int  $ttl
     * @return $this
     */
    public function setTTL($ttl)
    {
        $this->ttl = $ttl;

        return $this;
    }

    /**
     * Get the token ttl.
     *
     * @return int
     */
    public function getTTL()
    {
        return $this->ttl;
    }

    /**
     * Magically add a claim.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return Payload
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        $this->addClaim($method, $parameters[0]);
        return $this;
    }

}