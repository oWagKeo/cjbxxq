<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/12/14
 * Time: 16:11
 *
 * 线下门店优惠券控制器
 */
namespace app\admin\controller;

use app\admin\model\CouponsClassModel;
use app\admin\model\CouponsModel;
use app\admin\model\StoreModel;
use think\Db;
use think\Validate;

class Coupons extends  Base
{

    /**
     * 优惠券领取记录
     */
    public function coupons_log(){
        $where = array();
        if( !empty($_GET['is_use']) ){
            $where['a.is_use'] = trim($_GET['is_use'])-1;
        }
        $list = Db::name('coupons_log')->alias('a')
                  ->field('a.*,b.name,c.member_name')
                  ->join('__COUPONS__ b','a.coupons_id=b.id','LEFT')
                  ->join('__MEMBER__ c','a.member_id=c.id','LEFT')
                  ->where($where)->order('a.id desc')->paginate();

//        print_r($list);
        $this->assign('list',$list);
        $this->assign('get',$_GET);
        return $this->fetch();
    }

    /**
     *优惠券分类
     */
    public function coupons_class_list(){
        $model = new CouponsClassModel();
        $list = $model->getClassList();
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 添加分类
     */
    public function coupons_class_add(){
        if( request()->isAjax() ){
            $model = new CouponsClassModel();
            $data = [
                'class_name' => input('param.name'),
                'class_thumd' => input('param.thumd'),
                'class_sort' => input('param.sort'),
                'add_time' => time()
            ];
            $re = $model->addClass($data);
            return json($re);
        }else{
            return $this->fetch();
        }
    }

    /**
     * 修改分类
     */
    public function coupons_class_edit(){
        $model = new CouponsClassModel();
        if( request()->isAjax() ){
            $data = [
                'class_name' => input('param.name'),
                'class_thumd' => input('param.thumd'),
                'class_sort' => input('param.sort'),
            ];
            $re = $model->editClass(input('param.id'),$data);
            return json($re);
        }else{
            $id = input('param.id');
            $info = $model->getClassById( $id );
            $this->assign('info',$info);
            return $this->fetch();
        }
    }

    /**
     * 删除分类
     */
    public function coupons_class_del(){
        $id = input('param.id');
        $model = new CouponsClassModel();
        $re = $model->delClassById($id);
        return json($re);
    }

    /**
     * 上传分类缩略图
     */
    public function class_uploadImg(){
        if(request()->isAjax()){
            $file = request()->file('file');
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->move(ROOT_PATH . 'public' . DS . 'upload' . DS . '/coupons_class');
            if($info){
                $src =  '/upload/coupons_class' . '/' . date('Ymd') . '/' . $info->getFilename();
                return json(msg(1, ['src' => $src], ''));
            }else{
                // 上传失败获取错误信息
                return json(msg(-1, '', $file->getError()));
            }
        }
    }
    /**
     * 优惠券管理列表
     */
    public function coupons_list(){
        $where = array();
        if( !empty($_GET['name']) ){
            $where['name'] = ['like','%'.trim($_GET['name']).'%'];
        }
        $model = new CouponsModel();
        $list = $model->getCouponsList($where);

        $this->assign('list',$list);
        $this->assign('get',$_GET);
        return $this->fetch();
    }

    /**
     * 重置密码
     * 密码为登录手机号码
     */
    public function reset_pass(){
        $id = input('param.id');
        $model = new CouponsModel();
        $login_phone = $model->getCouponsById($id,'login_phone');
        $re = $model->resetPasswordById($id,md5($login_phone['login_phone']));
        return json($re);
    }

    /**
     * 编辑
     */
    public function coupons_edit(){
        $cModel = new CouponsModel();

        if( request()->isAjax() ){
            $data = [
                'name' => input('param.name'),
                'desc' => input('param.desc'),
                'thumd' => input('param.thumd'),
                'discount' => input('param.discount'),
                'store_address' => input('param.store_address'),
                'store_phone' => input('param.store_phone'),
                'start_time' => strtotime(input('param.start_time')),
                'end_time' => strtotime(input('param.end_time')),
                'status' => input('param.status'),
                'class_id' => input('param.class_id'),
//                'login_phone' => input('param.login_phone'),
                'is_recommend' => input('param.is_recommend'),
                'user_id' => session('id')
            ];
            $re = $cModel->editCouponsById(input('param.id'),$data);
            return json($re);
        }else{
            $id = input('param.id');
            $info = $cModel->getCouponsById( $id );

            $model = new CouponsClassModel();
            $list = $model->getClassList( null,'*','class_id asc' );

            $this->assign([
                'c_list'=>$list,
                'info'=>$info,
            ]);
            return $this->fetch();
        }
    }
    /**
     * 增加优惠券
     */
    public function coupons_add(){

        if(request()->isAjax()){
            $cModel = new CouponsModel();
            $data = [
                'name' => input('param.name'),
                'desc' => input('param.desc'),
                'thumd' => input('param.thumd'),
                'discount' => input('param.discount'),
                'store_address' => input('param.store_address'),
                'store_phone' => input('param.store_phone'),
                'start_time' => strtotime(input('param.start_time')),
                'end_time' => strtotime(input('param.end_time')),
                'add_time' => time(),
                'status' => input('param.status'),
                'class_id' => input('param.class_id'),
//                'login_phone' => input('param.login_phone'),
//                'login_password' => md5(input('param.login_phone')),
                'is_recommend' => input('param.is_recommend'),
                'user_id' => session('id')
            ];
            $re = $cModel->insertCoupons($data);
            return json($re);
        }else{
            $model = new CouponsClassModel();
            $list = $model->getClassList( null,'*','class_id asc' );
            $this->assign('c_list',$list);
            return $this->fetch();
        }
    }

    /**
     * 优惠券删除
     */
    public function coupons_del(){
        $id = input('param.id');
        $awardModel = new CouponsModel();
        $re = $awardModel->delCouponsById($id);
        return json($re);
    }


    /**
     * 上传优惠券缩略图片
     */
    public function uploadImg(){
        if(request()->isAjax()){
            $file = request()->file('file');
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->move(ROOT_PATH . 'public' . DS . 'upload' . DS . '/coupons');
            if($info){
                $src =  '/upload/coupons' . '/' . date('Ymd') . '/' . $info->getFilename();
                return json(msg(0, ['src' => $src], ''));
            }else{
                // 上传失败获取错误信息
                return json(msg(-1, '', $file->getError()));
            }
        }
    }

    /**
     * 门店列表
     */
    public function store_list(){
        $where = array();
        if( !empty($_GET['name']) ){
            $where['a.name'] = ['like','%'.trim($_GET['name']).'%'];
        }
        $s_model = new StoreModel($where);
        $list = $s_model->getStoreList();
        $this->assign('get',$_GET);
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 门店编辑
     */
    public function store_edit(){
        if( request()->isAjax() ){
            $s_model = new StoreModel();
            $data = [
                'store_name' => input('param.name'),
                'store_thumd' => input('param.thumd'),
                'store_phone' => input('param.phone'),
                'store_address' => input('param.address'),
//                'store_unique' => md5(uniqid(time())),
                'coupons_id' => input('param.coupons_id'),
//                'add_time' => time(),
                'status' => input('param.status'),
                'user_id' => session('id'),
            ];
            $re = $s_model->editStoreById(input('param.id'),$data);
            return json($re);
        }else{
            //优惠券列表
            $c_model = new CouponsModel();
            $c_list = $c_model->getCouponsAll(null,'a.id,a.name');

            //门店详情
            $id = input('param.id');
            $s_model = new StoreModel();
            $info = $s_model->getStoreById( $id );
            $this->assign('info',$info);
            $this->assign('c_list',$c_list);
            return $this->fetch();
        }
    }

    /**
     * 删除门店
     */
    public function store_del(){
        $id = input('param.id');
        $s_model = new StoreModel();
        $re = $s_model->delStoreById($id);
        return json($re);
    }

    /**
     * 添加门店
     * @return mixed
     */
    public function store_add(){
        if( request()->isAjax() ){
            $s_model = new StoreModel();
            $data = [
                'store_name' => input('param.name'),
                'store_thumd' => input('param.thumd'),
                'store_phone' => input('param.phone'),
                'store_address' => input('param.address'),
                'store_unique' => md5(uniqid(time())),
                'coupons_id' => input('param.coupons_id'),
                'add_time' => time(),
                'status' => input('param.status'),
                'user_id' => session('id'),
            ];
            $re = $s_model->insertStore($data);
            return json($re);
        }else{
            $c_model = new CouponsModel();
            //优惠券列表
            $c_list = $c_model->getCouponsAll(null,'a.id,a.name');
            $this->assign('c_list',$c_list);
            return $this->fetch();
        }
    }

    /**
     * 上传门店缩略图
     */
    public function store_uploadImg(){
        if(request()->isAjax()){
            $file = request()->file('file');
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->move(ROOT_PATH . 'public' . DS . 'upload' . DS . '/store');
            if($info){
                $src =  '/upload/store' . '/' . date('Ymd') . '/' . $info->getFilename();
                return json(msg(0, ['src' => $src], ''));
            }else{
                // 上传失败获取错误信息
                return json(msg(-1, '', $file->getError()));
            }
        }
    }


}