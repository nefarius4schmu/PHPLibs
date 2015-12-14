<?php
/**
 * Nefa Libs
 * Table Class
 * html code generator for table layouts
 *
 * Copyright 2015 Steffen Lange
 * Licensed under the WTFPL license
 *
 * @author Steffen Lange
 * @version 1.1.0
 */
class Table{

    private $defaults = [];

    function __construct($globals=[]){
        $this->defaults = array_merge($this->defaults, $globals);
    }

    public function basic($data=[], $options=[]){
        $o = new TableOptions(array_merge($this->defaults, $options));

        $ths = '';
        $tbody = '';
        $first = true;
        foreach($data as $row){
            $tds = '';
            foreach($row as $th=>$td){
                if($first) $ths .= $this->_elem('th', $th);
                $tds .= $this->_elem('td', $td);
            }
            $tbody .= $this->_elem('tr', $tds);
            $first = false;
        }
        $thead = $this->_elem('thead', $ths);
        return $this->_elem('table', $thead.$tbody, $o);
    }

    public function multi($data=[], $options=[]){
        $o = new TableOptions(array_merge($this->defaults, $options));

        $ths = '';
        $tbody = '';
        $first = true;
        foreach($data as $row){
            $tds = '';
            foreach($row as $th=>$td){
                if($first) $ths .= $this->_elem('th', $th);
                if(!is_array($td)){
                    $tds .= $this->_elem('td', $td);
                }else{
                    $tds .= $this->multi($td);
                }

            }
            $tbody .= $this->_elem('tr', $tds);
            $first = false;
        }
        $thead = $this->_elem('thead', $ths);
        return $this->_elem('table', $thead.$tbody, $o);
    }

    public function listing($data=[], $options=[]){
        $o = new TableOptions(array_merge($this->defaults, $options));

        $tbody = '';
        foreach($data as $index=>$td){
            $tds = $this->_elem('td', $index);
            $tds .= $this->_elem('td', $td);
            $tbody .= $this->_elem('tr', $tds);
        }
        return $this->_elem('table', $tbody, $o);
    }

    /**
     * @param $content
     * @param TableOptions $options
     * @return string
     */
    private function _elem($tag, $content=null, $options=null){
        $content = !is_array($content) ? $content : print_r($content, true);
        if($options === null) return "<${tag}>${content}</${tag}>";
        else return '<'.$tag.$options->id().$options->classes().'>'.$content.'</'.$tag.'>';
    }

}

/**
 * Nefa Libs
 * Helper Class TableOptions
 * parse options
 * get value
 * convert to DOM element attribute string
 *
 * Copyright 2015 Steffen Lange
 * Licensed under the WTFPL license
 *
 * @author Steffen Lange
 * @version 1.1.0
 */
class TableOptions{
    private $data=[];

    function __construct($option){
        foreach($option as $key=>$value)
            $this->data[$key] = $value;
    }

    function __get($attr){return isset($this->data[$attr]) ? $this->data[$attr] : null;}
    function _exists($a){return isset($this->data[$a]);}
    function _is($a){return $this->_exists($a) && $this->data[$a];}

    function _attr($a){
        $c = $this->__get($a);
        return $c !== null ? " ${a}=\"${c}\"" : null;
    }

    function id(){return $this->_attr('d');}
    function classes(){return $this->_attr('class');}
}