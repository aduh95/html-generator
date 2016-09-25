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
    arrayToPHAR($phar, [__DIR__.DIRECTORY_SEPARATOR.DEFAULT_FILE], null, __DIR__);
    $phar->setDefaultStub(DEFAULT_FILE);
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
