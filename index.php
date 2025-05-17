<?php
    include "classes/class_db.php";
    include "classes/projects.php";
    include "classes/contexts/projectsContext.php";

    $db = new DBConnect();

    $post_zapros = [
        'name' => "sfdsaf",
        'description' => "sdfsadf",
        'is_public' => true,
    ];
    
    $projectContext = new ProjectsContext($db,$post_zapros);
    
    $projectContext->Add();
?>