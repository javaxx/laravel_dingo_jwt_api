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
    public function getBanner()
    {
        $url = 'https://t.numbersi.cn/banner/';
        return
            [
                'banner' => [
                    $url . '1.jpg',
                    $url . '2.jpg',
                    $url . '3.jpg',
                ],
                'palce' => [['商丘->张家港', '张家港->商丘'], ['沙集', '营郭', '宋集', '黄冢', '杜集', '麦仁', '刘店', '商丘市区', '柘城']],
                'palceCunt' => [['沙集', '营郭', '宋集', '黄冢', '杜集', '麦仁', '刘店', '商丘市区', '柘城'], ['妙桥', '九大队', '新桥服务区', '张家港市区']],
                'palceInfo' =>
                    [
                        [['沙集', '沙集汽车站内', ['6:00', '8:00'], ['34.189710', '115.767700', ['沙集汽车站', '13837028118']]],
                            ['营郭', '营郭十字路,营郭高速加油站', ['6:30', '8:30'], ['34.104347', '115.807341', ['营郭高速加油站', '13837028118']]],
                            ['宋集', '宋集', ['6:00', '8:00'], ['34.093000,115.699000', ['宋集', '13837028118']]],
                            ['黄冢', '黄冢', ['6:00', '8:00'], ['34.104143', '115.865979', ['黄冢', '13837028118']]],
                            ['杜集', '杜集', ['6:00', '8:00'], ['34.183938', '115.823520', ['杜集', '13837028118']]],
                            ['麦仁', '麦仁', ['6:00', '8:00'], ['34.275800', '115.854700', ['麦仁', '13837028118']]], //34.244980,115.841610
                            ['刘店', '刘店高速南加油站', ['6:00', '8:00'], ['34.275800', '115.854700', ['刘店高速南加油站', '13837028118']]],
                            ['商丘市区', '商丘303长途汽车站', ['9:00', '(请到站内购票)'], ['34.442590', '115.657610', ['商丘303长途汽车站', '13837028118']]],
                            ['柘城', '柘城长途汽车站', ['9:00', '(请到站内购票)']],
                        ],
                        [
                            ['妙桥', '妙桥', ['6:00', '8:00'], ['31.806116', '120.705172', ['妙桥', '13837028118']]],//31.806116,120.705172
                            ['九大队', '九大队', ['6:00', '8:00'], ['31.798870', '120.661890', ['妙桥', '13837028118']]],//31.798870,120.661890
                            ['新桥服务区', '新桥服务区', ['6:00', '8:00'], ['31.810023', '120.495043', ['妙桥', '13837028118']]],//31.810023,120.495043
                            ['张家港市区', '张家港市区', ['6:00', '(请到站内购票)'], ['31.847360', '120.564380', ['妙桥', '13837028118']]],//31.847360,120.564380
                        ]
                    ],
                'contacts' => ['13837028118','17739388881'],
                'gzh'=>['微信搜索关注公众号"沙集客运",便捷了解客车资讯,留言乘车意见与建议']
            ];
    }
}