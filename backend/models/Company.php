<?php

namespace backend\models;

use Yii;


/**
 * This is the model class for table "{{%tv_company}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $adress
 * @property string $phone
 * @property integer $node_count
 * @property integer $cma_count
 */
class Company extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tv_company}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'adress', 'phone'], 'required','message'=>'不能为空'],
            ['name', 'match','pattern'=>'/^[(\x{4E00}-\x{9FA5})a-zA-Z]+[(\x{4E00}-\x{9FA5})a-zA-Z_\d]*$/u','message'=>'单位名称由字母，汉字，数字，下划线组成，且不能以数字和下划线开头。'],
            ['adress', 'match','pattern'=>'/^[(\x{4E00}-\x{9FA5})a-zA-Z]+[(\x{4E00}-\x{9FA5})a-zA-Z_\d]*$/u','message'=>'单位地址由字母，汉字，数字，下划线组成，且不能以数字和下划线开头。'],
            // ['name','required','message'=>'公司名称不能为空'],
            // ['phone','required','message'=>'电话号码不能为空'],
            [['node_count', 'cma_count'], 'integer'],
            [['name', 'adress'], 'string', 'max' => 255],
            ['phone', 'match','pattern'=>'/(^(86)\-(0\d{2,3})\-(\d{7,8})\-(\d{1,4})$)|(^0(\d{2,3})\-(\d{7,8})$)|(^0(\d{2,3})\-(\d{7,8})\-(\d{1,4})$)|(^(86)\-(\d{3,4})\-(\d{7,8})$)/','message'=>'电话号码格式不正确'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '单位名称',
            'adress' => '地址',
            'phone' => '联系电话',
            'node_count' => '节点数',
            'cma_count' => '摄像头数',
        ];
    }

    /*
    * Set AllCompany Redis
    */
    public function setAllRedis(){
        $com_id = Yii::$app->cache->redis->INCR($this->id);   //add 1
        Yii::$app->cache->redis->hmset('company:'.$com_id,    //'com:'.$this->id,Redis的哈希键值对的集合，对于Redis命令 com:1 是键
            [
                'id'=>$this->id,
                'name'=>$this->name,
                'adress'=>$this->adress,
                'phone'=>$this->phone,
                'node_cout'=>$this->node_count,
                'cma_count'=>$this->cma_count
            ]
        );
    }

    /*
    * Set Company Redis
    */
    public function setCompanyRedis(){
        Yii::$app->cache->redis->set('time',time());        //保存该单位最后修改的时间信息
        Yii::$app->cache->redis->hmset('company:'.$this->id,    //'com:'.$this->id,Redis的哈希键值对的集合，对于Redis命令 com:1 是键
            'id',$this->id,
            'name',$this->name,
            'adress',$this->adress,
            'phone',$this->phone,
            'node_cout',$this->node_count,
            'cma_count',$this->cma_count
        );
    }

    /*
     * Get Company Redis
     */
    public function getCompanytRedis($id){
        $redis_arr = [];
        $redis_arr['id'] = Yii::$app->cache->redis->HGET('company:'.$id,'id');
        $redis_arr['name'] = Yii::$app->cache->redis->HGET('company:'.$id,'name');
        $redis_arr['adress'] = Yii::$app->cache->redis->HGET('company:'.$id,'adress');
        $redis_arr['phone'] = Yii::$app->cache->redis->HGET('company:'.$id,'phone');
        $redis_arr['node_count'] = Yii::$app->cache->redis->HGET('company:'.$id,'node_count');
        $redis_arr['cma_count'] = Yii::$app->cache->redis->HGET('company:'.$id,'cma_count');
        return $redis_arr;
    }

    /*
     * Delete Company Redis
     */
    public function delCompanyRedis(){
        Yii::$app->cache->redis->del('time');
        Yii::$app->cache->redis->del('company:'.$this->id);
    }
}
