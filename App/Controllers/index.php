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
     $this->nwf->mvc->loadController("index", 50000);
     $this->nwf->mvc->loadModel("Test");
     $this->nwf->test->World();
   }

   public   function getViewVars() {
      return $this->vars;
   }

   public   function __destruct() {
      if (isset($this->vars))
         unset($this->vars);
   }
}
