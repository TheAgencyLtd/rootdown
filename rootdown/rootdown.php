<?php

namespace Rootdown;

use \Slim\Slim;
use \Michelf\MarkdownExtra;
use \Filicious\Local\LocalAdapter;
use \Filicious\Filesystem;
use \Mni\FrontYAML;


class Site{
  public static function meth(){

  }
}

class Page {
  public $children = array();
  public function content(){
    return $this->doc['content'];
  }
  public function title(){
    return $this->doc['frontmatter']['title'];
  }
  public function description(){
    return $this->doc['frontmatter']['description'];
  }
  public function attribute($attrbute){
    return $this->doc['frontmatter'][$attrbute];
  }
  public function path(){
    return $this->path;
  }
  public function children(){
    return $this->children;
  }
  public function mainmenu(){
    $this->rd->mainmenu();
  }
  public function channel(){
    $this->rd->channel();
  }
  public function breadcrumb(){
    $this->rd->breadcrumb();
  }
  public function pagelist($root){
    return $this->rd->pagelist($root);
  }
  public function data(){
    return $this->data;
  }

}

class Rootdown {

  private $siteCached = null;

  function __construct() {
    $this->markdownPath = $_SERVER['DOCUMENT_ROOT'].'/markdown/';
  }

  public function URL(){
    return rtrim($_SERVER['REQUEST_URI'],'/');
  }

  private function parts($path){
    return explode('/', ltrim($path, '/'));
  }

  public function find($pages, $url){
    foreach($pages as $page){
      if($page->path == $url){
        return $page;
      }
      if(!empty($page->children)){
        return $this->find($page->children, $url);
      }
    }
  }

  private function markdownFile($file){
    return rtrim($this->markdownPath, '/').$file;
  }

  private function page($file) {
    $parser = new \Mni\FrontYAML\Parser();
    $md = file_get_contents($this->markdownFile($file));
    $doc = $parser->parse($md, false);
    return array(
      "frontmatter" => $doc->getYAML(),
      "content" => MarkdownExtra::defaultTransform($doc->getContent())
    );
  }

  private function parse($files, &$parent){
    foreach ($files as $file) {
      if($file->getBasename() != "index.md"){

        $page = new Page;
        $page->data = $file;
        $page->path = rtrim($file->getPathname(), '.md');

        $parent->children[] = $page;

        if($file->isDirectory()){
          $page->doc = $this->page($file->getPathname().'/index.md');
          $this->parse($file, $page);
        } else {
          $page->doc = $this->page($file->getPathname());
        }
      }
    }
  }

  public function site(){

    $rootFile = '/index.md';

    if(file_exists($this->markdownFile($rootFile))){
      $fs = new Filesystem(new LocalAdapter($this->markdownPath));
      $root = new Page;
      $root->doc = $this->page($rootFile);
      $this->parse($fs->getRoot(), $root);
      return $root;
    } else {
      echo "Missing index.md file in /markdown";
    }

  }

  public function render($file = null, $template = 'default.php', $data = null){

    if($file){
      $page = new Page;
      $page->doc = $this->page($file);
    } else {
      $page = $this->find($this->site()->children, $this->URL());
      $template = $page->doc['frontmatter']["template"];
    }

    if($data){
      $page->data = $data;
    }

    $page->rd = $this;

    Slim::getInstance()->render($template, array(
      "page" => $page
    ));

  }

  public function navigation($pages, $recursive = true){ ?>
    <ul>
      <?php foreach($pages as $page) :

        $class = '';
        $classes = array();

        if(strpos($this->URL(), $page->path()) !== false){
          $classes[] = "in-path";
        }

        if($page->path()==$this->URL()){
          $classes[] = "selected";
        }

        if(count($classes) > 0){
          $class = ' class="'.implode(' ', $classes).'"';
        }

        ?>
        <li>
          <a href="<?=$page->path()?>"<?=$class?>><?=$page->title()?></a>
          <?php if($recursive) if(!empty($page->children)) $this->navigation($page->children); ?>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php }

  function breadcrumb(){
    $sections = '';
    $paths = array();
    foreach($this->parts($this->URL()) as $part){
      $sections .= '/'.$part;
      array_push($paths, $this->find($this->site()->children, $sections));
    }
    $this->navigation($paths, false);
  }

  function channel(){
    $this->navigation($this->find($this->site()->children, "/".$this->parts($this->URL())[0])->children);
  }

  function mainmenu(){
    $this->navigation($this->site()->children);
  }

  function pagelist($root){
    return $this->find($this->site()->children, $root)->children;
  }

}
