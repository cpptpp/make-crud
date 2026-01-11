<?php

namespace Meioa\Tools;



class MakeAction
{


    const ACTIONS = ['AddRow','DeleteRow','GetColumns','PageQuery'];
    //const ACTIONS = ['add_row','delete_row','get_columus','page_query'];


    /**
     * @var string 命令空间
     */
    private $_nameSpace;

    /**
     * @var string  从命名空间解析的目录
     */
    private $_targetDir;


    /**
     * @var string 根目录
     */
    private $_rootPath;

    public function __construct($nameSpace)
    {
        $this->_nameSpace = $nameSpace;
    }

    private function  _getClassName($action): string
    {


        $tmpArr = explode('_',$action);
        $className = '';
        foreach ($tmpArr as $temp){
            $className .= ucfirst($temp);
        }
        return $className;
    }


    private function _getServiceTpl($action): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'make' . DIRECTORY_SEPARATOR.$action.'.stub';
    }


    private function _getRootPath(): string
    {
        return dirname(__DIR__,4);
    }
    private function _getPsr4Rules($rootPath): array
    {
        $response = ['error'=> 110,'message'=>''];

        //var_dump($dir);die;
        $composerJsonPath = $rootPath . '/composer.json'; // 根据实际情况调整
        if (!file_exists($composerJsonPath)) {
            $response['message'] = 'composer.json not found';
            return $response;
        }

        $composerConfig = json_decode(file_get_contents($composerJsonPath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $response['message'] = 'Invalid JSON in composer.json';
            return $response;
        }

        // 获取 PSR-4 自动加载规则
        $psr4 = $composerConfig['autoload']['psr-4'] ?? [];

        if (empty($psr4)) {
            $response['message'] = 'No PSR-4 rules defined in composer.json';
            return $response;
        }
        $response['data'] = $psr4;
        $response['error'] = 0;
        return $response;
    }


    private function _getNamespaceFromDirectory(string $targetDir, array $psr4Rules): ?string
    {


        $namespace = null;
        foreach ($psr4Rules as $namespacePrefix => $ruleDir) {
            // 规范化 baseDir
            $baseDir = rtrim(str_replace('\\', '/', $ruleDir), '/\\');

            // 检查目标目录是否以 baseDir 开头
            if (strpos('A'.$targetDir,$baseDir) ===1 ) {
                // 计算相对路径部分
                $relativePath = substr($targetDir, strlen($baseDir));
                // 将相对路径转换为命名空间后缀
                $suffix = str_replace('/', '\\', trim($relativePath, '/'));
                // 合并命名空间前缀和后缀
                $fullNamespace = rtrim($namespacePrefix, '\\') . ($suffix ? '\\' . $suffix : '');

                $namespace =  rtrim($fullNamespace, '\\');
                break;
            }
        }

        return $namespace;
    }

    /**
     * 初始化
     */
    private function _init(){

        // 规范化路径分隔符
        $this->_targetDir = str_replace('\\', '/', ltrim($this->_nameSpace,"/\\"));

        //设置根目录
        $this->_rootPath = $this->_getRootPath();
    }

    /**
     * 指定目录下，生成指定表的增、删、改、查 功能
     * @param $action
     * @return array
     */
    public function run( $action): array
    {

        $this->_init();
        $rootPath = $this->_rootPath;
        $targetDir = $this->_targetDir;

        //var_dump($targetDir);die;
        $response = ['error'=> 110,'message'=>''];

        $Psr4RulesRes = $this->_getPsr4Rules($rootPath);
        if($Psr4RulesRes['error']>0){
            return $Psr4RulesRes;
        }

        $ns = $this->_getNamespaceFromDirectory($targetDir,$Psr4RulesRes['data']);
        if(empty($ns)){
            $response['message'] = '未匹配到当前目录';
            return $response;
        }

        //var_dump($rootPath);die ;
        //var_dump($ns);die;


        if(!in_array($action,self::ACTIONS)){
            $response['message'] = '暂不支持';
            return $response;
        }
        $dir = $rootPath.DIRECTORY_SEPARATOR.$targetDir;
        //$dir = rtrim($dir,'\\');
        //var_dump($dir);
//        if(!is_dir($dir)){
//            $response['message'] = '目录不存在';
//            return $response;
//        }
        $actionDir = rtrim($dir,'/\\');
        //var_dump(is_dir($actionDir),$actionDir);die;
        //文件夹不存在或者不是目录。创建文件夹
        if ( !is_dir($actionDir)) {
            mkdir($actionDir, 0777, true);
        }
        $targetDirArr = explode('/',$targetDir);
        $table = array_pop($targetDirArr);
        $tablePk = (new TableCache())->getPk($table);
        if(empty($tablePk)){
            $response['message'] = '表'.$table.'主键查找失败';
            return $response;
        }
//        $className = $this->_getClassName($table).$this->_getClassName($action);
        $className = $this->_getClassName($action);
        $classFile =$actionDir.DIRECTORY_SEPARATOR.$className.'.php';
        //var_dump($classFile);die;

        if(!file_exists($classFile)){
            $stub = file_get_contents($this->_getServiceTpl($action));
            $classStr = str_replace(['{%className%}','{%namespace%}','{%table%}','{%tablePk%}'], [
                $className,
                $ns,
                $table,
                $tablePk
            ], $stub);
            $res = file_put_contents($classFile, $classStr);
            if($res){
                $response['error'] = 0;
                $response['message'] = '成功创建'.$className;
            }
        }else{
            $response['message'] = '文件已存在'.$className;
        }
        return $response;
    }
}
