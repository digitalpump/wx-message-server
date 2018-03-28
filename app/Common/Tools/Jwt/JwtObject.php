<?php

/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/4
 * Time: 10:39
 */
class JwtObject
{

    /**
     * @var array
     */
    protected $payload;

    /**
     * @var array
     */
    protected $header;

    protected $encoder;
    /**
     * Constructor
     *
     * @param array $payload
     * @param array $header
     */
    public function __construct(array $payload, array $header)
    {
        $this->setPayload($payload);
        $this->setHeader($header);
    }
    /**
     * Returns the payload of the JWT.
     *
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Sets the payload of the current JWT.
     *
     * @param array $payload
     */
    public function setPayload(array $payload)
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * Returns the header of the JWT.
     *
     * @return array
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Sets the header of this JWT.
     *
     * @param array $header
     */
    public function setHeader(array $header)
    {
        $this->header = $header;

        return $this;
    }

}