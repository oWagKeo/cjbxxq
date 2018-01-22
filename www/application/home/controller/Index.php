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
    public function index(){
        //获取分类
        $type_list = Db::name('coupons_class')->where(array())->order('class_sort desc,class_id desc')->select();
        //获取推荐券
        $list = Db::name('coupons')->field('id,name,discount,thumd,store_address,store_phone')
                    ->where('is_recommend','1')->order('id desc')->select();

        $this->assign([
            'list'=>$list,
            'type_lsit'=>$type_list
        ]);
        return $this->fetch();
    }

    /**
     * 券列表分类
     */
    public function  coupons_list(){
        if( request()->isAjax() ){

            $limit = 10;
            $offset = (input('param.page') - 1) * $limit;

            //优惠券列表
            $list = Db::name('coupons')->field('id,name,discount,thumd,store_address,store_phone')
                ->where('class_id','1')->limit($offset,$limit)->order('id desc')->select();
            $class_info = Db::name('coupons_class')->field('class_id,class_thumd')->where('class_id',input('param.class_id'))->find();
            //整理数据
            foreach( $list as $k=>$v ){
                   if( empty($v['thumd'])||$v['thumd']=='' ){
                        $list[$k]['thumd'] = $class_info['class_thumd'];
                   }
            }
            return json(array('data'=>$list));

        }else{
            //分类id
            $class_id = input('param.id');
            $class_info = Db::name('coupons_class')->field('class_id,class_name')->where('class_id',$class_id)->find();
            $this->assign('class_info',$class_info);
            return $this->fetch();
        }
    }

    /**
     * 领取优惠券
     */
    public function add_coupons_log(){
        $id = input('param.id');
        if( !is_numeric( $id ) ){
            return json(msg(-1,'','数据错误！'));
        }
        session('member_id','1');
        session('member_phone','15858282359');
        //查看领取券中是否有未使用的该种券
        $where = [
            'member_id' => session('member_id'),
            'coupons_id' => $id,
            'is_validate' => 0
        ];
        $info = Db::name('coupons_log')->field('id')->where( $where )->find();
        if( !empty($info) ){
            return json(msg(-1,'','该券已领取！'));
        }

        $data = [
            'member_id' => session('member_id'),
            'member_phone' => session('member_phone'),
            'add_time' => time(),
            'coupons_id' => $id,
        ];
        $insert = Db::name('coupons_log')->insert($data);
        if( $insert ){
            return json(msg(1,'','领取成功！'));
        }else{
            return json(msg(-1,'','领取失败！'));
        }
    }

    /**
     * 抽奖
     */
    public function award(){
        echo '抽奖页';
    }

    /**
     * 计算中奖——奖品
     */
    public function lottery(){

    }

}
