<?php

namespace Meioa\Tools;


use FilesystemIterator;
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

    /**
     * 获取当前缓存目录下所有已存在的缓存标识
     * @access public
     * @return array
     */
    public function getKeys(): array
    {
        $dirname = rtrim($this->options['path'], DIRECTORY_SEPARATOR);

        if ($this->options['prefix']) {
            $dirname .= DIRECTORY_SEPARATOR . $this->options['prefix'];
        }

        return $this->scanKeys($dirname);
    }

    /**
     * 递归扫描缓存文件，还原缓存标识
     * @access private
     * @param string $dirname 待扫描目录
     * @param string $prefix  已扫描到的子目录相对路径
     * @return array
     */
    private function scanKeys(string $dirname, string $prefix = ''): array
    {
        if (!is_dir($dirname)) {
            return [];
        }

        $keys  = [];
        $items = new FilesystemIterator($dirname);

        foreach ($items as $item) {
            if ($item->isDir() && !$item->isLink()) {
                // 子目录（cache_subdir 开启时是 name 的前两位），
                // 直接拼接即可还原原始缓存标识，不能补目录分隔符
                $keys = array_merge($keys, $this->scanKeys($item->getPathname(), $prefix . $item->getBasename()));
            } elseif ($item->isFile() && $item->getExtension() === 'php') {
                $keys[] = $prefix . $item->getBasename('.php');
            }
        }

        return $keys;
    }


}
