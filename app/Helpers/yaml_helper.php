<?php
use Dallgoot\Yaml;
use Stringy\Stringy;
if(!function_exists('readyaml')){
    function readyaml(string $strConf){
        $yaml_file = APPPATH.'Config/App.yaml';
        $yaml = Yaml::parseFile($yaml_file);
        $arData = json_decode(json_encode($yaml),true);
        return $arData[$strConf];
    }
}
if(!function_exists('myString')){
    function myString(string $strConf){
        return Stringy::create($strConf);
    }
}