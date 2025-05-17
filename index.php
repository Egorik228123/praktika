<?php
    include "classes/project.php";

    $name = 123;
    $isPublic = false;
    $project = new Project($name, null, $isPublic);
    
    echo $project->name;
    echo $project->description;
?>