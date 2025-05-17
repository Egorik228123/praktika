<?php
    include "classes/class_db.php";
    include "classes/projects.php";
    include "classes/contexts/projectsContext.php";

    $db = new DBConnect();

    
    $name = "sfdsaf";
    $description = "sdfsadf";
    $isPublic = true;

    $projectContext = new ProjectsContext($db,$name, $description, $isPublic);
    
    $projectContext->Add();
?>