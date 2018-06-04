<?php
/**
 * Created by PhpStorm.
 * User: Jade
 * Date: 2018/6/4
 * Time: 下午1:49
 */

namespace IdCard;

class IdCard
{
    //性别显示方式：中文
    const GENDER_CN = 'cn';
    //性别显示方式：英文首字母
    const GENDER_EN = 'en';
    /**
     * 单例实例
     * @var class object
     */
    private static $_instance;

    /**
     * 身份证号
     * @var string
     */
    private $id;


    /**
     * 归属地
     * @var array
     */
    private $area;

    /**
     * 地区列表
     * @var array
     */
    private static $areas;

    /**
     * 身份证号是否合法
     * @var bool
     */
    private $isValid = false;

    /**
     * 加权因子
     * @var array
     */
    private static $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);

    /**
     * 校验码对应值
     * @var array
     */
    private static $code = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');

    /**
     * 返回单例实例/初始化地区列表
     * @param string $id 身份证号
     * @return class|IdCard
     */
    public static function create ($id = '')
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
            $path            = dirname(dirname(__FILE__));
            $areas           = file_get_contents($path . '/data/areas.json');
            self::$areas     = json_decode($areas);
        }

        if (empty($id)) {
            self::$_instance->setId($id);
        }
        return self::$_instance;
    }

    /**
     * 设置身份证号码
     * @param string $id 身份证号
     */
    public function setId ($id)
    {
        if (empty($id)) throw new \InvalidArgumentException('Id Card must not be empty.');

        $this->id       = strtoupper(trim($id));
        $this->isValid  = false;
        return self::$_instance;
    }

    /**
     * 返回当前设置的身份证号
     * @return string
     */
    public function getId ()
    {
        return $this->id;
    }

    /**
     * 检查身份证号码格式是否正确
     * 基础格式 18位长度 6位地区码+8位日期+3位随机数+1位校验码
     * @return bool
     */
    private function checkFormat ()
    {
        return preg_match("/^[\d]{6}(18|19|20)\d{2}(0[1-9]|1[012])(0[1-9]|[12]\d|3[01])\d{3}[xX\d]$/", $this->id);
    }

    /**
     * 检查身份证号码上的地区码是否合法
     * @return bool
     */
    private function checkArea ()
    {
        $area_code = substr($this->id, 0, 6);
        return isset(self::$areas[$area_code]);
    }

    /**
     * 检查身份证号码上出生日期是否合法
     * @return bool
     */
    private function checkBirthday ()
    {
        $year  = intval(substr($this->id, 6, 4));
        $month = intval(substr($this->id, 10, 2));
        $day   = intval(substr($this->id, 12, 2));
        return checkdate($month, $day, $year);
    }


    /**
     * 检验最后一位校验码是否正确
     * @return bool
     */
    private function checkCode ()
    {
        // 取出本体码
        $idcard_base = substr($this->id, 0, 17);
        // 取出校验码
        $verify_code = substr($this->id, 17, 1);
        // 根据前17位计算校验码
        $total = 0;
        for ($i = 0; $i < 17; $i++) {
            $total += substr($idcard_base, $i, 1) * self::$factor[$i];
        }
        // 取模
        $mod = $total % 11;
        return $verify_code == self::$code[$mod];
    }

    /**
     * 设置isValid属性，该方法始终返回true
     * @param bool $isValid 是否合法
     * @return true
     */
    private function setValid ($isValid = false)
    {
        $this->isValid = $isValid;
        return true;
    }

    /**
     * 根据身份证信息获取其性别
     * @param string $lang 性别显示方式 en-英文， cn-中文
     * @return bool|string
     */
    public function getGender ($lang = self::GENDER_CN)
    {
        // 倒数第2位
        $gender = substr($this->id, 16, 1);
        if ($lang == self::GENDER_CN) {
            $gender = ($gender % 2 == 0) ? '女' : '男';
        } else {
            $gender = ($gender % 2 == 0) ? 'f' : 'm';
        }
        return $gender;
    }


    /**
     * 根据身份证号获取地址
     * @return string
     */
    public function getArea ()
    {
        $province = substr($this->id, 0, 2) . '0000';
        $city     = substr($this->id, 0, 4) . '00';
        $district = substr($this->id, 0, 6);
        return [
            'province' => self::$areas[$province],
            'city'     => self::$areas[$city],
            'county'   => self::$areas[$district]
        ];
    }
}
