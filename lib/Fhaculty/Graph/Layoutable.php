<?php

namespace Fhaculty\Graph;

use Fhaculty\Graph\Exception\OutOfBoundsException;

abstract class Layoutable {
    /**
     * associative array of layout settings
     * 
     * @var array
     */
    private $layout = array();
    
    /**
     * get array of layout settings
     * 
     * @return array
     */
    public function getLayout(){
        return $this->layout;
    }
    
    /**
     * set raw layout without applying escaping rules
     * 
     * @param string|array $name
     * @param mixed        $value
     * @return Layoutable|Graph|Vertex|Edge $this (chainable)
     */
    public function setLayoutRaw($name,$value=NULL){
        if($name === NULL){
            $this->layout = array();
            return $this;
        }
        if(!is_array($name)){
            $name = array($name=>$value);
        }
        foreach($name as $key=>$value){
            if($value === NULL){
                unset($this->layout[$key]);
            }else{
                $this->layout[$key] = $value;
            }
        }
        return $this;
    }
    
    /**
     * set multiple layout attributes
     * 
     * @param array $attributes
     * @return self $this (chainable)
     * @uses GraphViz::escape()
     * @see Layoutable::setLayoutAttribute()
     */
    public function setLayout(array $attributes){
        if($attributes === NULL){
            $this->layout = array();
            return $this;
        }
        foreach($attributes as $key=>$value){
            if($value === NULL){
                unset($this->layout[$key]);
            }else{
                $this->layout[$key] = GraphViz::escape($value);
            }
        }
        return $this;
    }
    
    /**
     * set a single layouto attribute
     * 
     * @param string $name
     * @param string $value
     * @return self
     * @see Layoutable::setLayout()
     */
    public function setLayoutAttribute($name,$value){
        if($value === NULL){
            unset($this->layout[$name]);
        }else{
            $this->layout[$name] = GraphViz::escape($value);
        }
        return $this;
    }
    
    /**
     * checks whether layout option with given name is set
     * 
     * @param string $name
     * @return boolean
     */
    public function hasLayoutAttribute($name){
        return isset($this->layout[$name]);
    }
    
    public function getLayoutAttribute($name){
        if(!isset($this->layout[$name])){
            throw new OutOfBoundsException('Given layout attribute is not set');
        }
        return $this->layout[$name]; 
    }
}
