<?php

namespace Rootdown;

use \Slim\Slim;
use \Michelf\MarkdownExtra;
use \Filicious\Local\LocalAdapter;
use \Filicious\Filesystem;
use \Mni\FrontYAML;

class Page{

  private $path;
  private $YAML;
  public $children = array();

  function __construct($path){
    $this->path = $path;
    $this->YAML = file_get_contents($path);
  }

  public function __call($name, $arguments){

  }

  private function YAMLData(){
    $parser = new \Mni\FrontYAML\Parser();
    return $parser->parse($this->YAML, false);
  }

  public function path(){

    $start = strlen(Site::getMarkdownPath())-1;
    $pos   = strrpos($this->path, '/index.md');
    $str   = substr($this->path, $start, ($pos ? $pos-$start : -3));

    return $str == '' ? '/' : $str;

  }

  public function title(){
    return $this->YAMLData()->getYAML()['title'];
  }

  public function template(){
    return $this->YAMLData()->getYAML()['template'];
  }

  public function content(){
    return MarkdownExtra::defaultTransform($this->YAMLData()->getContent());
  }

  public function description(){
    return $this->YAMLData()->getYAML()['description'];
  }

  // public function attribute($attrbute){
  //   return $this->doc['frontmatter'][$attrbute];
  // }

  public function children(){
    return $this->children;
  }

}

class Site{

  public static $tree = null;
  public static $markdownPath;

  private static function parse($files, &$parent){

    foreach ($files as $file) {

      if($file->getBasename() != "index.md"){

        $page = self::mint(rtrim($file->getPathname()) . ($file->isDirectory() ? '/index.md' : ''));

        $parent->children[] = $page;

        if($file->isDirectory()){
          self::parse($file, $page);
        }

      }

    }

  }


  private static function getMarkdownFile($file){
    return rtrim(self::$markdownPath, '/').$file;
  }

  private static function setMarkdownPath($path){
    self::$markdownPath = $_SERVER['DOCUMENT_ROOT'].$path;
  }

  public static function find($pages, $url){
    foreach($pages as $page){
      if($page->path() == $url){
        return $page;
      }
      if(!empty($page->children)){
        return self::find($page->children, $url);
      }
    }
  }

  public static function getMarkdownPath(){
    return self::$markdownPath;
  }

  public static function map($markdownPath = '/markdown/'){

    self::setMarkdownPath($markdownPath);

    if(!self::$tree){

      $rootFile = '/index.md';

      if(file_exists(self::getMarkdownFile($rootFile))){

        $fs = new Filesystem(new LocalAdapter(self::$markdownPath));
        $root = self::mint($rootFile);

        self::parse($fs->getRoot(), $root);
        self::$tree = $root;

        return $root;

      } else {

        echo "Missing index.md file in /$markdownPath";

      }

    } else {

      return self::$tree;

    }

  }

  private static function mint($file) {
    $instance = new Page(self::getMarkdownFile($file));
    return $instance;
  }

  public static function page($path){
    $path = is_array($path) ? implode('/', $path) : $path;
    return self::find(array(self::map()), $path);
  }


  private static function URL(){
    return rtrim($_SERVER['REQUEST_URI'],'/');
  }

  private static function parts($path){
    return explode('/', ltrim($path, '/'));
  }

  private static function navigation($pages, $recursive = true){ ?>
    <ul>
      <?php foreach($pages as $page) :

        $class = '';
        $classes = array();

        if(strpos(self::URL(), $page->path()) !== false){
          $classes[] = "in-path";
        }

        if($page->path()==self::URL()){
          $classes[] = "selected";
        }

        if(count($classes) > 0){
          $class = ' class="'.implode(' ', $classes).'"';
        }

        ?>
        <li>
          <a href="<?=$page->path()?>"<?=$class?>><?=$page->title()?></a>
          <?php if($recursive) if(!empty($page->children)) self::navigation($page->children); ?>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php }

  public static function mainmenu(){
    self::navigation(self::map()->children);
  }

  public static function channel(){
    self::navigation(self::find(self::map()->children, "/".self::parts(self::URL())[0])->children);
  }

  public static function breadcrumb(){
    $sections = '';
    $paths = array();
    foreach(self::parts(self::URL()) as $part){
      $sections .= '/'.$part;
      array_push($paths, self::find(array(self::map()), $sections));
    }
    self::navigation($paths, false);
  }

}
