<?php
/**
 * Created by PhpStorm.
 * User: bg
 * Date: 2019/6/11
 * Time: 20:26
 */

namespace apiadmin\controllers;
use common\components\Conver;
use common\components\PlacardTool;
use common\controllers\ApiAdminController;
use common\models\Bank001;
use common\models\Bank002;
use common\models\Card001;

use common\models\Config;
use common\models\Loanorder001;
use common\models\Notice001;
use common\models\Poster001;
use Yii;
use common\components\Encryption;
use common\models\User001;
use common\controllers\ApiController;
use yii\helpers\ArrayHelper;
use yii\data\Pagination;
use common\models\Loan001;
use common\models\Loan002;
use common\models\UserToken;


use common\components\oss\Oss;//todo  加
use common\models\Team001;
use common\models\Team002;
use common\models\Income001;
use common\models\Bbs001;
use common\models\Bbs004;

class SystemController extends ApiAdminController
{
    public function actionCa(){
        //测试git 尝试修改后 暂存修改的文件
        //第二次尝试 修改后  暂存修改的文件
        //第三次尝试 修改后 暂存修改的文件
        //第四次尝试
        //第五次尝试
        //444hhh
        //555kkkk
        //
      $array = [
            // 'key' => [
            //     'in' => ['k' => 'value']
            // ]
        ];
        // ArrayHelper::setValue($array, 'key.in', ['arr' => 'val']);
       // ArrayHelper::setValue($array, ['in','k'], ['arr' => 'val']);
       // ArrayHelper::setValue($array, 'key.in.arr0', ['arr1' => 'val']);
       ArrayHelper::setValue($array, '', ['arr1' => 'val']);

       // ArrayHelper::setValue($array, ['key', 'in'], ['arr' => 'val']);
       // $aa=  ArrayHelper::setValue($array, ['key', 'in'], ['arr' => 'val']);
        // $aa = ArrayHelper::setValue($array, 'key.in.arr0.arr1', 'val');
        self::res($array);
    }
    //系统通知
    public function actionNotice(){
        $notice_id = \Yii::$app->request->post('notice_id');
        $action = \Yii::$app->request->get('action');
        $data = \Yii::$app->request->post('value');
        $notice = Notice001::findOne($notice_id);
        switch ($action){
            case 'getNoticeAll':
                $notice =Notice001::find()->where(['notice_state'=>1,'type'=>1])->orderBy('notice_time desc');
                $pages = new Pagination(['defaultPageSize'=>10,'totalCount'=>$notice->count()]);
                $notice = $notice->limit($pages->limit)->offset($pages->offset)->asArray()->all();
                foreach ($notice as &$v){
                    $v['notice_time'] = date('Y-m-d H:i:s',$v['notice_time']);
                }
                $this->res(200,'ok',['notice'=>$notice,'pages'=>$pages]);
                break;
            case 'getNoticeOne':
                $notice = yii\helpers\ArrayHelper::toArray($notice);
                $notice['notice_time'] = date('Y-m-d H:i:s',$notice['notice_time']);
                $this->res(200,'ok',$notice);
                break;
            case 'edit':
                $notice->notice_name = $data['notice_name'];
                $notice->notice_content = $data['notice_content'];
                if ($notice->save()){
                    $this->res(200,'ok');
                }else{
                    $this->res(500,$notice->errors);
                }
                break;
            case 'add':
                $notice = new Notice001();
                $notice->notice_name = $data['notice_name'];
                $notice->notice_content = $data['notice_content'];
                $notice->notice_time = TIMESTAMP;
                $notice->type = 1;//通知
                if ($notice->save()){
                    $this->res(200,'ok');
                }else{
                    $this->res(500,$notice->errors);
                }
                break;
            case 'del':
                $notice->notice_state = 0;
                if ($notice->save()){
                    $this->res(200,'ok');
                }else{
                    $this->res(500,$notice->errors);
                }
                break;
        }
    }

    //常见问题
    public function actionQuean(){
        $notice_id = \Yii::$app->request->post('notice_id');
        $action = \Yii::$app->request->get('action');
        $data = \Yii::$app->request->post('value');
        $notice = Notice001::findOne($notice_id);
        switch ($action){
            case 'getQueanAll':
                $notice =Notice001::find()->where(['notice_state'=>1,'type'=>2])->orderBy('notice_time desc');
                $pages = new Pagination(['defaultPageSize'=>10,'totalCount'=>$notice->count()]);
                $notice = $notice->limit($pages->limit)->offset($pages->offset)->asArray()->all();
                foreach ($notice as &$v){
                    $v['notice_time'] = date('Y-m-d H:i:s',$v['notice_time']);
                }
                $this->res(200,'ok',['notice'=>$notice,'pages'=>$pages]);
                break;
            case 'getQueanOne':
                $notice = yii\helpers\ArrayHelper::toArray($notice);
                $notice['notice_time'] = date('Y-m-d H:i:s',$notice['notice_time']);
                $this->res(200,'ok',$notice);
                break;
            case 'edit':
                $notice->notice_name = $data['notice_name'];
                $notice->notice_content = $data['notice_content'];
                if ($notice->save()){
                    $this->res(200,'ok');
                }else{
                    $this->res(500,$notice->errors);
                }
                break;
            case 'add':
                $notice = new Notice001();
                $notice->notice_name = $data['notice_name'];
                $notice->notice_content = $data['notice_content'];
                $notice->notice_time = TIMESTAMP;
                $notice->type = 2;//常见问题
                if ($notice->save()){
                    $this->res(200,'ok');
                }else{
                    $this->res(500,$notice->errors);
                }
                break;
            case 'del':
                $notice->notice_state = 0;
                if ($notice->save()){
                    $this->res(200,'ok');
                }else{
                    $this->res(500,$notice->errors);
                }
                break;
        }
    }

//base64图片转成Oss地址返回
    public  function actionToss() {
        // $base64string = Yii::$app->require->post('code');
        $base64string = Yii::$app->request->post('code');
         $name = null;
        $dir = BASEPATH . '/cache/PlacardTool/' . time() . rand(1000, 9999);
        $base64_string = explode(',', $base64string);
        $base64_data = base64_decode($base64_string[1]);
        file_put_contents($dir, $base64_data);
        $type = explode('/', getimagesize($dir)['mime'])[1];
        $oss = new Oss();
        $name = $name ? $name : 'Admin_Img';
        $fileName = $oss->makeFileName($name, $type);
        $Url = $oss->upload($fileName, $dir);
        if ($Url) {
            // unlink($dir);
            return $Url;
        } else {
            return false;
        }
    }

    //客服二维码更新
    public function actionServicecode(){
        $action = \Yii::$app->request->get('action');
        $imgCode = Config::findOne('service_code');
        $gzhCode = Config::findOne('gzh_code');
        $ossUrl = Config::findOne('oss_url')->config_value;
        switch ($action){
            case 'getServiceCode':
                if (strstr($imgCode->config_value,'http')){
                    self::res($imgCode->config_value);
                }else{
                    self::res($ossUrl.$imgCode->config_value);
                }
                break;
            case 'getGzhCode':
                if (strstr($gzhCode->config_value,'http')){
                    self::res($gzhCode->config_value);
                }else{
                    self::res($ossUrl.$gzhCode->config_value);
                }
                break;
            case 'updateServiceCode':
                $base64Img = Yii::$app->request->post('code');
                $base64 = Yii::$app->request->post('gzhcode');
                if (!strstr($base64Img,'http')) {//判断传进来的内容是不是64格式
                    $imgCode->config_value = PlacardTool::base64ToOss($base64Img);
                }

                if (!strstr($base64,'http')) {//判断传进来的内容是不是64格式
                    $gzhCode->config_value = PlacardTool::base64ToOss($base64);
                }
                if ($imgCode->save() && $gzhCode->save()){
                    $imgCode = $ossUrl.$imgCode->config_value;
                    $gzhCode = $ossUrl.$gzhCode->config_value;
                    self::res(['imgCode'=>$imgCode,'gzhCode'=>$gzhCode]);
                }else{
                    self::res('修改失败');
                }
                break;
        }
    }


    //  //关注公众号二维码更新
    // public function actionGzhcode(){
    //     $action = \Yii::$app->request->get('action');
    //     $imgCode = Config::findOne('gzh_code');
    //     $ossUrl = Config::findOne('oss_url')->config_value;
    //     switch ($action){
    //         case 'getGzhCode':
    //             if (strstr($imgCode->config_value,'http')){
    //                 self::res($imgCode->config_value);
    //             }else{
    //                 self::res($ossUrl.$imgCode->config_value);
    //             }
    //             break;
    //         case 'updateGzhCode':
    //             $base64Img = Yii::$app->request->post('code');
    //             if (!strstr($imgCode->config_value,'http')) {
    //                 $imgCode->config_value = PlacardTool::base64ToOss($base64Img);
    //             }
    //             if ($imgCode->save()){
    //                 self::res($ossUrl.$imgCode->config_value);
    //             }else{
    //                 self::res('修改失败');
    //             }
    //             break;
    //     }
    // }


    //banner图
    public function actionBanner(){
        $banner_id = Yii::$app->request->post('banner_id');
        $action = Yii::$app->request->get('action');
        $data = \Yii::$app->request->post('value');
        $banner = Poster001::findOne($banner_id);
        $ossUrl = Config::findOne('oss_url')->config_value;
        switch ($action){
            case 'getBannerAll':
                $banList =Poster001::find()->where(['group_id'=>0,'poster_type'=>0]);
                $pages = new Pagination(['defaultPageSize'=>10,'totalCount'=>$banList->count()]);
                $banList = $banList->limit($pages->limit)->offset($pages->offset)->asArray()->all();
                foreach ($banList as &$v){
                    if (!strstr($v['poster_url'],'http')){
                        $v['poster_url'] = $ossUrl.$v['poster_url'];
                    }
                    $v['rank'] = $v['post_losetime'];
                    unset($v['post_losetime']);
                }
                $this->res(200,'ok',['notice'=>$banList,'pages'=>$pages]);
                break;
            case 'getBannerOne':
                $banner = yii\helpers\ArrayHelper::toArray($banner);
                if (!strstr($banner['poster_url'],'http')){
                    $banner['poster_url'] = $ossUrl.$banner['poster_url'];
                }
                $this->res(200,'ok',$banner);
                break;
            case 'edit':
                $banner->poster_value = $data['url'];
                $banner->poster_text = $data['text'];
                if (strstr($data['bannerBase64'],'http')){
                    $banner->poster_url = $data['bannerBase64'];
                }else{
                    $imgUrl = PlacardTool::base64ToOss($data['bannerBase64'],'banner');
                    $banner->poster_url = $imgUrl;
                }
                $banner->post_losetime = $data['rank'];
                if ($banner->save()){
                    $this->res(200,'ok');
                }else{
                    $this->res(500,$banner->errors);
                }
                break;
            case 'add':
                $banner = new Poster001();
                $banner->group_id = 0;//系统图片
                $banner->poster_type = 0;//banner图
                $banner->type_id = 0;//系统图片类型0
                if (strstr($data['bannerBase64'],'http')){
                    $banner->poster_url = $data['bannerBase64'];
                }else{
                    $imgUrl = PlacardTool::base64ToOss($data['bannerBase64'],'banner');
                    $banner->poster_url = $imgUrl;
                }
                $banner->poster_value = $data['url'];//海报跳转链接
                if (strlen($data['text'])>2){
                    $banner->poster_text = $data['text'];
                }
                $banner->post_losetime = $data['rank'];
                if ($banner->save()){
                    $this->res(200,'ok');
                }else{
                    $this->res(500,$banner->errors);
                }
                break;
            case 'del':
                $banner->delete();
                $this->res(200,'ok');
                break;
            default:
                $this->res(403,'请输入完整参数');
        }
    }

    public function actionSetmobilecode($mobile,$code){
        $redis = Yii::$app->redis;
        $mobileCache = [//发送信息
            'mobile' => $mobile,//手机号
            'smscode' => $code,//验证码
            'lastTime' => TIMESTAMP,//最后一次发送时间
            'frequency' => 1,//当前发送次数
            'firstTime' => TIMESTAMP,//第一次发送时间
        ];
        //储存发送信息到redis
        $redis->set($mobile, json_encode($mobileCache));
        $redis->expire($mobile, 3600);//设置过期时间
        echo $mobile."----".$code;
    }

       //上传文件至oss
    public function actionUploadoss(){
        $file = 'D:\ChangZhi\zhaolian2019126.jpg';
//        $file = file_get_contents($file);
//        v($file);
//        $name = 'Admin_Img';
        $name = 'zhaolian_code2019126';
        $oss = new Oss();
        $type = substr(strrchr($file, '.'), 1);//获取类型
        $fileName = $oss->makeFileName($name,$type);
        //http://kashenimg.oss-cn-shenzhen.aliyuncs.com/Edu_Img/2019/12/15753637527243.png
        // $fileName = '/Admin_Img/2019/12/15754455597243.jpg';
        // v($fileName);die;

//        $file = file_get_contents($file);
//        v($file);
        $Url = $oss->upload($fileName, $file);
        //拼接起来
        //http://ts.rong298.cn/html/spread?codeurl=http://kashenimg.oss-cn-shenzhen.aliyuncs.com. $Url
        //string(43) "/jd_code20191127/2019/11/15748235386942.png"
        v($Url);
    }


    public function actionAffiche(){
        $data =  Yii::$app->request->post('value');//添加公告内容
        $config = Config::findOne('affiche');
        $action = Yii::$app->request->get('action');//行为
        switch ($action) {
            case 'getAffiche'://得到
                $config = $config->config_value;
                self::res($config);
                break;
            case 'update'://更新
                if (!$data) {
                    self::res(403,'公告内容不能为空');
                }
                $config->config_value = $data['affiche'];//公告内容
                if ($config->save()){
                    $this->res(200,'ok');
                }else{
                    $this->res(500,$config->errors);
                }
                break;
            default:
                self::res(403,'非法请求');
                break;
        }
    }


    //公告前台
    public function actionAffiches(){
        $Affiche = Config::find()->select('config_name,config_value')->one();
        self::res($Affiche);
    }




//     //上传文件至oss
//     public function actionUploadoss(){
//         $file = 'D:\ChangZhi\androidv3.0.1.2.apk';
// //        $file = file_get_contents($file);
// //        v($file);
// //        $name = 'Admin_Img';
//         // $name = 'androidv3.0.0.8';
//        // $name = 'androidv3.0.1.0';

//         $oss = new Oss();
//         $type = substr(strrchr($file, '.'), 1);
//         // $fileName = $oss->makeFileName($name,$type);
//         $fileName = '/androidv3.0.1.2/12.apk'; //远程保存路径
// //        $file = file_get_contents($file);
// //        v($file);
//                // v($fileName);die;
//         $Url = $oss->upload($fileName, $file);
//         //"android":"http://kashenimg.oss-cn-shenzhen.aliyuncs.com/10.apk
//         v($Url);
//     }

    // public function actionMakefilename($type='androidv3.0.1.0',$ext='apk')
    // {
    //     $randNum = rand(1000,9999);
    //     return '/'.$type.date('/Y/m/').TIMESTAMP.$randNum.(strpos($ext, '.')!==FALSE?$ext:'.'.$ext);
    // }

    // public function actionUpdateapk()
    // {
    //     $src1= Config::find()->where(['config_name'=>'APK_NAME'])->one();
    //     $src = Yii::$app->params['oss2'].$src1['config_value'];//指的是线上的地址 oss2' =>'http://majialoan.oss-cn-shenzhen.aliyuncs.com', 在common中的params中

    //     set_time_limit(0);
    //     // if(Yii::$app->request->isAjax){
    //         $transaction = Yii::$app->db->beginTransaction();
    //         try{
    //             $request = Yii::$app->request;
              
    //             $oss = new Oss();
    //             $model = Config::findOne(['config_name'=>'APK_NAME']);
    //             if($model){
    //                 //安卓包
    //                 $apk_Name = 'config_name';
    //                 if(isset($_FILES[$apk_Name]) && !$_FILES[$apk_Name]['error']){
    //                     //删除旧图片
    //                     if(!empty($model->apk_name)){
    //                         $oss->remove($model->apk_name);
    //                     }
    //                     $file = $_FILES[$apk_Name];

    //                     $fileName = $oss->makeFileName('system/apk', File::getFileExt($file['name']));
    //                     $picUrl = $oss->upload($fileName, $file['tmp_name']);

    //                     if($picUrl){
    //                         $model->config_value = $picUrl;
    //                     }
    //                 }
                    
    //                 if($model->save()){
    //                     $transaction->commit();
    //                     $model->config_value = Yii::$app->params['oss2'].$picUrl;
    //                     // var_dump($model->config_value);die;
    //                     return ['res'=>1,'msg'=>'修改成功'];
    //                 }
    //                 $transaction->rollBack();
    //                 return ['res'=>0,'msg'=>'修改失败'];
    //             }
    //             $transaction->rollBack();
    //             return ['res'=>0,'msg'=>'该用户不存在或已被删除!'];
    //         }catch (\Exception $e){
    //             $transaction->rollBack();
    //             return ['res'=>0,'msg'=>'修改失败'.$e->getMessage()];
    //         }
    //     }

    // const PHONEREG = "/^1\d{10}$/"; // 手机号码正则(普通)
    // public static function checkPhone($src ='16650606837')
    // {
    //     if(!preg_match(static::PHONEREG, $src)) return FALSE;
    //     return TRUE;
    // } 


    //  public function actionUpdate(){
    //     $newVersion = Config::findOne('app_version');
    //     $downUrl = json_decode($newVersion->config_text,true);
    //     self::res(['src'=>$downUrl['android'],'ios_src'=>$downUrl['ios']]);
    // }


    //   public  function actionChe($id_card=654128199310010675,$real_name='乌兰·铁了哈孜'){
    //     //请求数据
    //     $sendData = array(
    //         "id_card"=>"$id_card",
    //         "real_name"=>"$real_name"
    //     );
    //     $url = 'https://api-jiesuan.yunzhanghu.com/authentication/verify-id';//请求地址
    //     $mess = rand(0,9).rand(0,9);//签名随机数
    //     $sendData = json_encode($sendData,JSON_UNESCAPED_UNICODE);//待发送数据转化成字符串
    //     // $sendData = self::encrypt($sendData);//加密后待发送数据
    //     // $signData = 'data='.$sendData.'&mess='.$mess.'&timestamp='.TIMESTAMP."&key=J3VI3n91o50872CQZN6hUSpiu6Iv7Xzr";//签名数据
    //     // $sign = self::sign($signData);//签名结果
    //     $data = array(
    //         'data'=>$sendData,
    //         'mess'=>$mess,
    //         'timestamp'=>TIMESTAMP,
    //         // 'sign'=>$sign,
    //         'sign_type'=>'sha256'
    //     );
    //     $request_id = date('YmdHis').rand(0,9).rand(0,9);//唯一请求id
    //     return self::sendData($request_id,$data,$url);
    // }


      //客户管理
    public function actionCustomer($type=1){
        $ossUrl = Config::findOne('oss_url')->config_value;
        $all = User001::find()->where(['user_leader_id'=>'116702']);//总客户数
        $applyed= Yii::$app->db->createCommand(
            "SELECT order_user_id FROM loanorder_001 WHERE order_end_user_id = ".'116702'." AND  order_user_id IN (SELECT id FROM user_001 WHERE user_leader_id = ".'116702'.")  GROUP BY order_user_id UNION  (SELECT order_user_id FROM cardorder_001 WHERE order_end_user_id = ".'116702'." AND  order_user_id IN (SELECT id FROM user_001 WHERE user_leader_id = ".'116702'.")  GROUP BY order_user_id)"
        )->queryColumn();//已申请人对象数组   
        // v($applyed);//得到已申请 用户的id
        $applyFor = $all->count('id') - count($applyed);//未申请人数
        $pages = new Pagination(['defaultPageSize' => 10]);
        if ($type == 1 ){//已申请列表
            $pages->totalCount = count($applyed);
            $applyedList= Yii::$app->db->createCommand(
                "SELECT * FROM (SELECT `order_user_id`,order_create_time FROM `loanorder_001` WHERE `order_end_user_id` = ".'116702'." AND `order_user_id` IN (SELECT `id` FROM `user_001` WHERE `user_leader_id` = ".'116702'." ) GROUP BY `order_user_id`
                        UNION
                        (SELECT `order_user_id`,order_create_time FROM `cardorder_001` WHERE `order_end_user_id` =".'116702'."  AND `order_user_id` IN (SELECT `id` FROM `user_001` WHERE `user_leader_id` = ".'116702'." ) GROUP BY `order_user_id`)) a order by order_create_time desc limit ".$pages->limit ." offset ".$pages->offset)
                ->queryColumn();//已申请人对象数组
            // v($applyedList);//得到已申请 用户按时间排序的id  在这里在写一次是因为 要用分页 显示一次    
            $list = [];
            foreach ($applyedList as $v){
                $data = Yii::$app->db->createCommand("
                SELECT * FROM (SELECT
                    `order_create_time`,order_loan_id as a_id,order_to_mobile,order_nickname,order_wxicon,order_state,order_query_time
                FROM
                    `loanorder_001` a
                WHERE
                    `order_user_id` = $v
                UNION
                SELECT
                    `order_create_time`,order_card_id as a_id,order_to_mobile,order_nickname,order_wxicon,order_state,order_query_time
                FROM
                    `cardorder_001` b
                WHERE
                    `order_user_id` = $v) c ORDER BY `c`.`order_create_time` DESC limit 1
                ")
                ->queryOne();
                // v($data);//得到的是申请用户的信息
                $a = Yii::$app->db->createCommand("select loan_name as a_name,loan_icon as a_icon,loan_order_losetime as losetime from loan_001 where id = {$data['a_id']}")->queryOne();
                // v($data['a_id']);
                if ($a){
                    $data['a_id'] = '通过我推荐申请的贷款';
                }else{
                    $a = Yii::$app->db->createCommand("SELECT `card_001`.`card_name` AS 'a_name', `bank_001`.`bank_icon` AS 'a_icon',card_order_losetime as losetime  FROM `card_001` LEFT JOIN `bank_001` ON (`card_001`.`card_bank` = `bank_001`.`id`) WHERE `card_001`.`id` = {$data['a_id']}")->queryOne();
                    $data['a_id'] = '通过我推荐申请的拉新产品';
                }
                $data['order_query_time'] = date('Y-m-d',$data['order_query_time']);
                // $data['order_to_mobile'] = Conver::phone_tuomin($data['order_to_mobile']);
                $data['order_to_mobile'] = $data['order_to_mobile'];

                $data['a_name'] = $a['a_name'];
                $data['a_icon'] = $a['a_icon'];
                if (!strstr($data['order_wxicon'],'http')){
                    $data['order_wxicon'] = $ossUrl.$data['order_wxicon'];
                }
                if (!strstr($data['a_icon'],'http')){
                    $data['a_icon'] = $ossUrl.$data['a_icon'];
                }
                array_push($list,$data);
            }
            // v($list);die;
            self::res(['ysq'=>count($applyed),'wsq'=>$applyFor,'ysqL'=>$list,'pages'=>$pages]);
        }else{//未申请列表
            $applyForList = $all->select('user_avatar,user_nickname,user_mobile,user_create_time')->andWhere(['not in','id',$applyed])->orderBy('user_create_time desc');//未申请人列表
            $pages->totalCount = $applyForList->count();
            $applyForList =  $applyForList->limit($pages->limit)->offset($pages->offset)->asArray()->all();//未申请人列表
            foreach ($applyForList as &$v){
                if(!strstr($v['user_avatar'],'http')){
                    $v['user_avatar'] = $ossUrl.$v['user_avatar'];
                }
                // $v['user_mobile'] = Conver::phone_tuomin($v['user_mobile']);
                $v['user_mobile'] = $v['user_mobile'];

                $v['user_create_time'] = date('Y-m-d H:i:s',$v['user_create_time']);
            }
            self::res(['ysq'=>count($applyed),'wsq'=>$applyFor,'wsqL'=>$applyForList,'pages'=>$pages]);
        }
    }



      public function actionQuicklogin($id){
        $userInfo = User001::find()->where(['id'=>$id])->one();
        if ($userInfo){
            Yii::$app->user->login($userInfo);//登录
            to('/web/#/pages/personal/personal');
            self::res('登录成功');
        }else{//未注册
            self::res(503,'密码错误');
        }
    }

     public function actionUpdate($version = null){
        $newVersion = Config::findOne('app_version');
        $updateLog = Config::findOne('app_update_log');
        if($version){
            $version = $str=preg_replace('|[.a-zA-Z/]+|','',$version);
            if ($newVersion->config_value > $version){//需要更新
                $downUrl = json_decode($newVersion->config_text,true);
                self::res(['newVersion'=>'v'.$newVersion->config_value,'updateLog'=>$updateLog->config_value,'is_force'=>1,'src'=>$downUrl['android'],'ios_src'=>$downUrl['ios']]);
            }else{
                self::res('ok');
            }
        }else{
            $downUrl = json_decode($newVersion->config_text,true);
            self::res(['src'=>$downUrl['android'],'ios_src'=>$downUrl['ios']]);
        }
    }

}
