<?php
/**
 * Created by PhpStorm.
 * User: si
 * Date: 2017/7/30
 * Time: 10:20
 */

namespace App\Api\Controllers;


class BannerController extends BaseController
{

    public function getLocation()
    {

        return [
            'palce'=>[['商丘->张家港', '张家港->商丘'], ['沙集', '营郭', '宋集', '黄冢', '杜集', '麦仁', '刘店', '商丘市区', '柘城']],
            'palceCunt'=> [['沙集', '营郭', '宋集', '黄冢', '杜集', '麦仁', '刘店', '商丘市区', '柘城'], ['妙桥', '九大队', '新桥服务区', '张家港市区']],
           'palceInfo'=> [
                [
                    ['沙集', '沙集汽车站内', ['6:00', '8:00'], ['34.189710','115.767700',['沙集汽车站','13837028118']]],
                    ['营郭', '营郭十字路,营郭高速加油站', ['6:30', '8:30']],
                    ['宋集', '宋集', ['6:00', '8:00']],
                    ['黄冢', '黄冢', ['6:00', '8:00']],
                    ['杜集', '杜集', ['6:00', '8:00']],
                    ['麦仁', '麦仁', ['6:00', '8:00']],
                    ['刘店', '刘店', ['6:00', '8:00']],
                    ['商丘市区', '商丘303长途汽车站', ['9:00', '(请到站内购票)']],
                    ['柘城', '柘城长途汽车站', ['9:00', '(请到站内购票)']],
                ],
                [
                    ['妙桥', '妙桥', ['6:00', '8:00']],
                    ['九大队', '九大队', ['6:00', '8:00']],
                    ['新桥服务区', '新桥服务区', ['6:00', '8:00']],
                    ['张家港市区', '张家港市区', ['6:00', '(请到站内购票)']],
                ]
            ]


        ];
    }
    public function getBanner()
    {

        $url = 'https://www.numbersi.cn/banner/';
        return
        [
            'banner'=>[
            $url . '1.jpg',
            $url . '2.jpg',
            $url . '3.jpg',
        ],
            'palce'=>[['商丘->张家港', '张家港->商丘'], ['沙集', '营郭', '宋集', '黄冢', '杜集', '麦仁', '刘店', '商丘市区', '柘城']],
            'palceCunt'=> [['沙集', '营郭', '宋集', '黄冢', '杜集', '麦仁', '刘店', '商丘市区', '柘城'], ['妙桥', '九大队', '新桥服务区', '张家港市区']],
            'palceInfo'=> [
                [
                    ['沙集', '沙集汽车站内', ['6:00', '8:00'], ['34.189710','115.767700',['沙集汽车站','13837028118']]],
                    ['营郭', '营郭十字路,营郭高速加油站', ['6:30', '8:30']],
                    ['宋集', '宋集', ['6:00', '8:00']],
                    ['黄冢', '黄冢', ['6:00', '8:00']],
                    ['杜集', '杜集', ['6:00', '8:00']],
                    ['麦仁', '麦仁', ['6:00', '8:00']],
                    ['刘店', '刘店', ['6:00', '8:00']],
                    ['商丘市区', '商丘303长途汽车站', ['9:00', '(请到站内购票)']],
                    ['柘城', '柘城长途汽车站', ['9:00', '(请到站内购票)']],
                ],
                [
                    ['妙桥', '妙桥', ['6:00', '8:00']],
                    ['九大队', '九大队', ['6:00', '8:00']],
                    ['新桥服务区', '新桥服务区', ['6:00', '8:00']],
                    ['张家港市区', '张家港市区', ['6:00', '(请到站内购票)']],
                ]
            ],
            'contacts'=>['13827028118']

        ];
    }

}