<?php

namespace Meioa\Tools;


use think\cache\driver\File;

class FileCache extends File
{

    /**
     * @param false $enbelSubdir
     */
    public function setSubdir($enbelSubdir=false){
        $this->options['cache_subdir'] = $enbelSubdir;
    }

    /**
     * @param string $prefix
     */
    public function setPrefix($prefix){
        $this->options['prefix'] = $prefix;
    }
    /**
     * 取得变量的存储文件名
     * @access public
     * @param string $name 缓存变量名
     * @return string
     */
    public function getCacheKey(string $name): string
    {

//        $name = hash($this->options['hash_type'], $name);

        if ($this->options['cache_subdir']) {
            // 使用子目录
            $name = substr($name, 0, 2) . DIRECTORY_SEPARATOR . substr($name, 2);
        }

        if ($this->options['prefix']) {
            $name = $this->options['prefix'] . DIRECTORY_SEPARATOR . $name;
        }

        return $this->options['path'] . $name . '.php';
    }


}
