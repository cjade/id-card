## idcard
二代身份证号码验证，可用于验证二代身份证号码是否合法、从身份证号码中获取生日/性别/年龄/地区信息 

### 安装
```php
composer require cjade/id-card 
```
### 环境要求
- PHP >= 5.6.0
### 使用方法
```php
use IdCard\IdCard;

$idCard = IdCard::create();
$idCard->setId('******************');
或者
$idCard = IdCard::create('******************');

// 获取生日，格式YYYY mm dd
$identity->getBirthday('-');
// 2018-06-01
$identity->getBirthday('/');
// 2018/06/01

//验证身份证号码格式是否正确
idCard = IdCard::create('******************');
$result = $idCard->check()
//true或false
或者
$idCard = IdCard::create('******************')->check();
//true或false

//获取身份证号
$id = $idCard->getId();

//

```
