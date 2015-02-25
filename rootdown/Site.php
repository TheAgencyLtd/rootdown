<?php

//======================================================================
// R O O T D O W N
//======================================================================

/* Developed by Nick Clarkson ( @t_pk )
 * http://rootdown.io
 * http://netotaku.github.io
 * http://TheAgencyOnline.co.uk
 */

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

  public function attribute($attrbute, $alt = false){
    $data = $this->YAMLData()->getYAML();
    return isset($data[$attrbute]) ? $data[$attrbute] : $alt;
  }

  private function YAMLData(){
    $parser = new \Mni\FrontYAML\Parser();
    return $parser->parse($this->YAML, false);
  }

  public function path(){

    $start = strlen(Site::getMarkdownPath())-1;
    $pos = strrpos($this->path, '/index.md');
    $str = substr($this->path, $start, ($pos ? $pos-$start : -3));

    return $str == '' ? '/' : $str;

  }

  public function title(){
    return $this->attribute('title', 'Title missing');
  }

  public function template(){
    return $this->attribute('template', 'default.php');
  }

  public function content(){
    return MarkdownExtra::defaultTransform($this->YAMLData()->getContent());
  }

  public function description(){
    return $this->attribute('description', 'Description missing');
  }

  public function order(){
    return $this->attribute('order', 0);
  }

  public function hidden(){
    return $this->attribute('hidden', false);
  }

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

  private static function getMarkdownFile($file){
    return rtrim(self::$markdownPath, '/').$file;
  }

  private static function setMarkdownPath($path){
    self::$markdownPath = $_SERVER['DOCUMENT_ROOT'].$path;
  }

  public static function getMarkdownPath(){
    return self::$markdownPath;
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

  //-----------------------------------------------------
  // Navigation
  //-----------------------------------------------------

  public static function navigation($pages, $recursive = true){ ?>
    <ul>
      <?php foreach($pages as $page) :
        if(!$page->hidden()) :

          $class = '';
          $classes = array();

          if(strpos(self::URL(), $page->path()) !== false) $classes[] = "in-path";
          if($page->path() == self::URL()) $classes[] = "selected";
          if(count($classes) > 0) $class = ' class="'.implode(' ', $classes).'"';

          ?>
          <li>
            <a href="<?=$page->path()?>"<?=$class?>><?=$page->title()?></a>
            <?php
              if($recursive){
                if(!empty($page->children)){

                  usort($page->children, function($a,$b){
                    return $a->order() > $b->order() ? 1 : -1;
                  });

                  self::navigation($page->children);
                }
              }
            ?>
          </li>
        <?php
          endif;
        endforeach; ?>
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
