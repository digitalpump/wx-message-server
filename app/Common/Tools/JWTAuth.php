<?php
namespace App\Common\Tools;
use Illuminate\Http\Request;

/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/4
 * Time: 09:02
 */
class JWTAuth
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    protected $token;

    public function __construct(Request $request)
    {
        $this->request = $request;

    }

    /**
     * Set the request instance.
     *
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }
}