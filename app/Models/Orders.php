<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/20
 * Time: 22:27
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{

    protected $table = "crs_orders";
    public function invoice() {
        return $this->hasOne("App\Models\Invoice",null,"invoice_id");
    }

}