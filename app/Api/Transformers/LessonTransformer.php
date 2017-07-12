<?php
/**
 * Created by PhpStorm.
 * User: si
 * Date: 2017/5/6
 * Time: 20:56
 */

namespace App\Api\Transformers;
use App\Lesson;
use League\Fractal\TransformerAbstract;

class LessonTransformer extends TransformerAbstract
{

    public function transform(Lesson $lesson)
    {
        return [
            'title' => $lesson['title'],
            'content' => $lesson['body'],
            'is_free' =>(boolean) $lesson['free'],
        ];

    }

}