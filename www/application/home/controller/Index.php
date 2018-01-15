<?php
namespace app\home\controller;

use think\Controller;
use think\Db;

class Index extends Base
{
    /**
     * 首页
     * @return mixed
     */
    public function index()
    {
        //获取分类
        $type_list = Db::name('coupons_class')->where(array())->order('class_sort desc,class_id desc')->select();
        //获取推荐券
        $list = Db::name('coupons')->field('id,name,discount,thumd,store_address,store_phone')->where('is_recommend','1')->select();

        $this->assign([
            'list'=>$list,
            'type_lsit'=>$type_list
        ]);
        return $this->fetch();
    }

    /**
     * 线下门店优惠券
     */
    public function coupons(){
        $list = Db::name('store_coupons')->field('*')->paginate();

    }

    /**
     * 抽奖
     */
    public function award(){

        return $this->fetch();
    }

    /**
     * 计算中奖——奖品
     */
    public function lottery(){

    }

}
