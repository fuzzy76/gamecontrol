#!/usr/bin/php
<?php

// Use sudo to run this script as your gameserver user

$gamecontroller = new GameController;
$gamecontroller->execute($argv);

Class GameController {

  public $basedir = '';
  public $gameserver = NULL;

  function __construct() {
    $this->basedir = realpath(dirname(__FILE__));
    chdir($this->basedir);
  }

  function execute($args) {
    switch($args[1]) {
      case 'start':
        $this->start($args[2]);
        break;
      case 'stop':
        $this->stop();
        break;
    }
  }

  function start($configname) {
    $date = date('m/d/y H:i:s');
    $runline = "./coduo_lnxded +set gamestartup \"$date\" +set com_hunkmegs 512 ";
    $runline .= "+set sv_punkbuster 0 +set fs_homepath {$this->basedir} ";
    $runline .= "+set fs_game BrothersInArms_mod +set dedicated 2 ";
    $runline .= "+exec awe.cfg +exec dedicateduo.cfg +set net_port 28960";
    $this->gameserver = new Process($runline);
// @todo save to pid file

  }

}


/* An easy way to keep in track of external processes.
 * Ever wanted to execute a process in php, but you still wanted to have somewhat controll of the process ? Well.. This is a way of doing it.
 * @compability: Linux only. (Windows does not work).
 * @author: Peec
 */
class Process{
    private $pid;
    private $command;

    public function __construct($cl=false){
        if ($cl != false){
            $this->command = $cl;
            $this->runCom();
        }
    }
    private function runCom(){
        $command = 'nohup '.$this->command.' > /dev/null 2>&1 & echo $!';
        exec($command ,$op);
        $this->pid = (int)$op[0];
    }

    public function setPid($pid){
        $this->pid = $pid;
    }

    public function getPid(){
        return $this->pid;
    }

    public function status(){
        $command = 'ps -p '.$this->pid;
        exec($command,$op);
        if (!isset($op[1]))return false;
        else return true;
    }

    public function start(){
        if ($this->command != '')$this->runCom();
        else return true;
    }

    public function stop(){
        $command = 'kill '.$this->pid;
        exec($command);
        if ($this->status() == false)return true;
        else return false;
    }
}
