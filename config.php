<?php
// Folders configuration
NWF::config()->mvc_view_folder = './App/View/';
NWF::config()->mvc_model_folder = './App/Models/';
NWF::config()->mvc_assets_folder = './App/Assets/';
NWF::config()->mvc_preprocess_folder = './App/Preprocess/';
NWF::config()->mvc_controller_folder = './App/Controllers/';
NWF::config()->mvc_compiled_folder = './App/Compiled/';


// Put here all configuration information
// E.g. SQL Database informations, etc.

NWF::config()->sql_database = "urbajob";
NWF::config()->sql_username = "urbajob";
NWF::config()->sql_password = "";
NWF::config()->sql_debug = true;
NWF::config()->debug_active = true;

NWF::config()->mvc_no_logged = "index?nologged=1";

// Custom paths
//NWF::setPath("un/code/secret", "partenaires-list");
