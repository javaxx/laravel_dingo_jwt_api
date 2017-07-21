<?php
/**
 * Created by PhpStorm.
 * User: si
 * Date: 2017/5/6
 * Time: 20:56
 */

namespace App\Api\Transformers;
use App\Lesson;
use App\Payer;
use League\Fractal\TransformerAbstract;

class PayerTransformer extends TransformerAbstract
{

    public function transform(Payer $payer)
    {
        return [
            'name' => $payer['name'],
            'idCard' => $payer['idCard'],
            'user_id' => $payer['user_id'],
        ];

    }

}