<?php
/**
 * Template
 * renderer for external template files
 */
class Template{

    private $path;
    private $data = [];
    /** @var callable[] */
    private $parser = [];
    /** @var callable */
    private $defaultParser;

    function __construct($path, $options=[]){
        $this->defaultParser = isset($option["parser"]) && is_callable($option["parser"]) ? $option["parser"] : function($v){return $v;};

        if(is_dir($path)){
            $this->path = $this->trailing($path);
        }else{
            throw new Exception(__CLASS__.': path \''.$path.'\' is no dir or does not exists');
        }
    }

    /**
     * render template from template path
     * @param string $name
     * @param array $data (optional)
     * @param bool $return (optional)
     * @return string|null
     */
    public function render($name, $data=[], $return=false){
        $path = $this->path.$name.'.php';
        $this->parse($data);

        // build template
        if(!$return){
            TemplateAPI::init($path, $this->data);
        }else{
            ob_start();
            TemplateAPI::init($path, $this->data);
            return ob_get_clean();
        }
    }

    /**
     * parse incoming data set using configured parser
     * @param array $data
     */
    private function parse($data){
        $defaultParser = $this->defaultParser;
        foreach($data as $key=>$item){
            $this->data[$key] = isset($this->parser[$key])
                ? $this->parser[$key]($item)
                : $defaultParser($item);
        }
    }



    /**
     * add trailing slash at the end of an string
     * @param string $str
     * @param string $trail
     * @return string
     */
    private function trailing($str, $trail='/'){
        return strpos($str, -strlen($trail)) !== false ? $str : $str.$trail;
    }
}

/**
 * Class TemplateAPI
 */
class TemplateAPI{
    private $data;

    /** @var  TemplateAPI */
    private static $self;

    protected final function __clone(){}
    protected final function __construct(){}

    /**
     * init template renderer
     * @param $path
     * @param $data
     * @return TemplateAPI
     * @throws Exception
     */
    public static final function init($path, $data) {
        // get singleton instance
        if(self::$self === null) self::$self = new self($data);
        // set template data
        self::$self->data = $data;
        // load template
        self::$self->__load($path);

        return self::$self;
    }

    /**
     * load template
     * @param $path
     * @throws Exception
     */
    function __load($path){
        if(file_exists($path)){
            include($path);
        }else{
            throw new Exception(__CLASS__.': missing template \''.$path.'\'');
        }
    }

    /**
     * getter for data set
     * @param $key
     * @return null
     */
    function __get($key){
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }
}