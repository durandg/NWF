<?php
class       Controller {
   private  $nwf;
   private  $vars;

   public   function Controller($nwf) {
      $this->nwf = $nwf;
      $this->vars = array();
      $this->controllerActions();
      $this->loadControllers();
   }

   private  function controllerActions() {

   }

   private  function loadControllers() {

   }

   public   function getViewVars() {
      return $this->vars;
   }

   public   function __destruct() {
      if (isset($this->vars))
         unset($this->vars);
   }
}
