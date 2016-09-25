<?php
/**
 * "Compress" PHP source file into a single PHAR file
 *
 * @package aduh95/HTMLGenerator
 * @license MIT
 */
const   PHAR_FILE = __DIR__.DIRECTORY_SEPARATOR.'HTMLGenerator.phar',
        API_DIR = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'src',
        DEFAULT_FILE = 'autoloader.php';

/**
 * Put all the files and recursively the directories of a directory into an array
 * @param string $dir Absolute path of the directory
 * @return array
 */
function dirToArray($dir)
{
    $result = array();

    $cdir = array_diff(scandir($dir), array('..', '.', '.git'));
    foreach ($cdir as $key => $value) {
        $cv = $dir . DIRECTORY_SEPARATOR . $value;
        if (is_dir($cv))
            $result[$value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value);
        else
            $result[] = $value;
    }

    return $result;
}

/**
 * Zip all the files within the files hierarchy of the array given
 *
 * @param Phar $phar
 * @param array $array Hierarchy of the files & directories to ZIP
 * @param string $local_dir_name
 * @param string $path Absolute path of the parent directory
 *
 */
function arrayToPHAR($phar, $array, $local_dir_name, $path)
{
    if (!empty($local_dir_name)) {
        $phar->addEmptyDir($local_dir_name);
        $local_dir_name.='/';
    }
    foreach ($array as $dir_name=>$file_name) {
        if (is_array($file_name))
            arrayToPHAR($phar, $file_name, $local_dir_name.$dir_name, $path.DIRECTORY_SEPARATOR.$dir_name);
        else if (strrchr($file_name,'.')==='.php')
            // Compress PHP files
            $phar->addFromString(
                $local_dir_name . $file_name,
                preg_replace(
                    ['# (\.|:|,|\$)#', '#, (-?\d)#', '# ?(\{|\}|\?|\(|\)|\&|\||=|;|\[|\]|\n) ?#', '#\', #'],
                    ['$1', ',$1', '$1', '\','],
                    php_strip_whitespace($path.DIRECTORY_SEPARATOR.$file_name)
                )
            );
        //  Uncomment the next two lines if you need to add non PHP files to your PHAR
        // else
        //  $phar->addFile($path.DIRECTORY_SEPARATOR.$file_name, $local_dir_name.$file_name);
    }
}

try {
    if(is_file(PHAR_FILE))
        unlink(PHAR_FILE);
    $phar = new Phar(PHAR_FILE);
    arrayToPHAR($phar, dirToArray(API_DIR), null, API_DIR);
    arrayToPHAR($phar, [DEFAULT_FILE], null, __DIR__);
    $stub = <<<'PHP'
<?php
$web='index.php';if(in_array('phar',stream_get_wrappers())&&class_exists('Phar',0)){Phar::interceptFileFuncs();set_include_path('phar://'. __FILE__. PATH_SEPARATOR. get_include_path());Phar::webPhar(null,$web);include 'phar://'. __FILE__. '/'. Extract_Phar::START;return;}class Extract_Phar{static$temp;static$origdir;const GZ=0x1000;const BZ2=0x2000;const MASK=0x3000;const START='
PHP;
    $stub.= DEFAULT_FILE."'; const LEN =".(3542+strlen(DEFAULT_FILE));
    $stub.= <<<'PHP'
;CONST LEN_READ=8192;static function go($return=false){$fp=fopen(__FILE__, 'rb');fseek($fp, self::LEN);$L=unpack('V',$a=(binary)fread($fp,4));$m=(binary)'';do{$read=self::LEN_READ;if($L[1]- strlen($m)< self::LEN_READ){$read=$L[1]- strlen($m);}$last=(binary)fread($fp,$read);$m.=$last;}while(strlen($last)&&strlen($m)<$L[1]);if(strlen($m)<$L[1])exit('ERROR: manifest length read was "'. strlen($m).'" should be "'.$L[1]. '"');$info=self::_unpack($m);$f=$info['c'];$temp=self::tmpdir();if(!$temp||!is_writable($temp)){$sessionpath=session_save_path();if(strpos($sessionpath, ";")!==false)$sessionpath=substr($sessionpath, strpos($sessionpath, ";")+1);if(!file_exists($sessionpath)||!is_dir($sessionpath)){die('Could not locate temporary directory to extract phar');}$temp=$sessionpath;}$temp.='/pharextract/'.basename(__FILE__, '.phar');self::$temp=$temp;self::$origdir=getcwd();@mkdir($temp,0777, true);$temp=realpath($temp);if(!file_exists($temp. DIRECTORY_SEPARATOR. md5_file(__FILE__))){self::_removeTmpFiles($temp, getcwd());@mkdir($temp,0777, true);@file_put_contents($temp. '/'. md5_file(__FILE__), '');foreach($info['m']as$path=>$file){$a=!file_exists(dirname($temp. '/'.$path));@mkdir(dirname($temp. '/'.$path),0777, true);clearstatcache();if($path[strlen($path)- 1]=='/'){@mkdir($temp. '/'.$path,0777);}else{file_put_contents($temp. '/'.$path, self::extractFile($path,$file,$fp));@chmod($temp. '/'.$path,0666);}}}chdir($temp);if(!$return)include self::START;}static function tmpdir(){if(strpos(PHP_OS, 'WIN')!==false){if($var=getenv('TMP')?getenv('TMP'): getenv('TEMP'))return$var;if(is_dir('/temp')||mkdir('/temp'))return realpath('/temp');return false;}if($var=getenv('TMPDIR'))return$var;return realpath('/tmp');}static function _unpack($m){$info=unpack('V',substr($m,0,4));$l=unpack('V',substr($m,10,4));$m=substr($m,14 +$l[1]);$s=unpack('V',substr($m,0,4));$o=0;$start=4 +$s[1];$ret['c']=0;for($i=0;$i <$info[1];$i++){$len=unpack('V',substr($m,$start,4));$start +=4;$savepath=substr($m,$start,$len[1]);$start +=$len[1];$ret['m'][$savepath]=array_values(unpack('Va/Vb/Vc/Vd/Ve/Vf',substr($m,$start,24)));$ret['m'][$savepath][3]=sprintf('%u',$ret['m'][$savepath][3]&0xffffffff);$ret['m'][$savepath][7]=$o;$o +=$ret['m'][$savepath][2];$start +=24 +$ret['m'][$savepath][5];$ret['c']|=$ret['m'][$savepath][4]&self::MASK;}return$ret;}static function extractFile($path,$entry,$fp){$data='';$c=$entry[2];while($c){if($c < 8192){$data.=@fread($fp,$c);$c=0;}else{$c -=8192;$data.=@fread($fp,8192);}}if($entry[4]&self::GZ){$data=gzinflate($data);}elseif($entry[4]&self::BZ2){$data=bzdecompress($data);}if(strlen($data)!=$entry[0]){die("Invalid internal.phar file(size error ". strlen($data). " !=".$stat[7]. ")");}if($entry[3]!=sprintf("%u", crc32((binary)$data)&0xffffffff)){die("Invalid internal.phar file(checksum error)");}return$data;}static function _removeTmpFiles($temp,$origdir){chdir($temp);foreach(glob('*')as$f){if(file_exists($f)){is_dir($f)?@rmdir($f): @unlink($f);if(file_exists($f)&&is_dir($f))self::_removeTmpFiles($f, getcwd());}}@rmdir($temp);clearstatcache();chdir($origdir);}}Extract_Phar::go();__HALT_COMPILER();?>
PHP;

    $phar->setStub($stub);
    $phar->setMetadata([
        'author'=>'aduh95',
        'mime-type'=>'application/php',
        'version'=>'1.0.0',
        'package'=>'HTMLGenerator',
        'source'=>'https://github.com/aduh95/html-generator/',
        ]);
} catch (PharException $e) {
    echo 'This requires the php.ini setting phar.readonly to be set to 0 in order to work for Phar objects.'."\n".
        $e->getMessage()."\n";
}
