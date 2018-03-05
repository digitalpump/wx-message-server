<?php

namespace App\Common\Tools\Jwt;
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/4
 * Time: 23:45
 */
use Illuminate\Support\Str;
use Carbon\Carbon;
class PayloadFactory
{
    /**
     * @var int
     */
    protected $ttl = 60;


    /**
     * @var array
     */
    protected $defaultClaims = ['iat', 'exp', 'nbf', 'jti'];

    /**
     * @var array
     */
    protected $claims = [];


    public function __construct($ttl = 60)
    {
        $this->ttl = $ttl;
    }

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

    public function getClaims() {
        return $this->claims;
    }

    /**
     * Add an array of claims to the Payload.
     *
     * @param  array  $claims
     * @return $this
     */
    public function addClaims(array $claims)
    {
        foreach ($claims as $name => $value) {
            $this->addClaim($name, $value);
        }

        return $this;
    }

    public function makePayloadWithUserId($userId) {
        foreach ($this->defaultClaims as $claim) {
            $this->addClaim($claim, $this->$claim());
        }
        $this->addClaim('sub',$userId);
        return $this->getClaims();
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
     * @return PayloadFactory
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        $this->addClaim($method, $parameters[0]);

        return $this;
    }


}