<?php

/**
 * COMMAND LIST:
 * 
 * create {model|controller|view} {name}: create a model, controller or view
 * mvc-scaffold {name}: create the triplet MVC
 * create-posttype {name} {identificator}: create an posttype
 * create-migrations: create tables and put tables in activator
 * run-migrations: make all migrations (CAUTION: can erase data)
 * 
 */

$command = $argv[1];
$params = array_slice($argv, 2);

switch ($command) {
    case 'create':
        create($params);
        break;
    case 'mvc-scaffold':
        createMVC($params);
        break;
    case 'create-posttype':
        createPostType($params);
        break;
    case 'create-migrations':
        createMigrations();
        break;
    case 'run-migrations':
        runMigrations();
        break;
    case 'make-build':
        makeBuild();
        break;
    default:
        echo 'Invalid option';
        break;
}

function createMVC($params)
{
    create(['controller',$params[0]]);
    create(['model',$params[0]]);
    create(['view',$params[0]]);
}

function create($params)
{
    $mvc_structure_path = '../mvc-structure/';
    $plugin_path = './src/classes/';

    if (count($params) < 1) {
        echo "ERROR: Parameters are missing";
        return;
    }
    $filedata = [
        'words' => [
            ['original' => '/Plugin0Name/', 'new' => 'Polymorph_Bot'],
            ['original' => '/Example/', 'new' => $params[1]],
            ['original' => '/example/', 'new' => strtolower($params[1])],
        ],
        'filenames' => [
            ['original' => 'example', 'new' => strtolower($params[1])],
        ]
    ];
    switch ($params[0]) {
        case 'model':
            createDirStructure($mvc_structure_path . 'models', $plugin_path . 'models', $filedata);
            createFileV2($filedata, $mvc_structure_path . 'examples', $plugin_path . 'models', 'example_m.php');
            break;
        case 'controller':
            createDirStructure($mvc_structure_path . 'controllers', $plugin_path . 'controllers', $filedata);
            createFileV2($filedata, $mvc_structure_path . 'examples', $plugin_path . 'controllers', 'example_c.php');
            break;
        case 'view':
            createDirStructure($mvc_structure_path . 'views', $plugin_path . 'views', $filedata);
            file_put_contents($plugin_path . 'views/templates/' . strtolower($params[1]) . '.php', '');
            break;
        default:
            echo 'Unavailable option';
            break;
    }
}

function createDirStructure($origin, $destiny, $filedata) {
    if (!is_dir($destiny)) {
        mkdir($destiny, 0777, true);
    }
  
    $files = scandir($origin);
  
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
          
            if (is_dir($origin . '/' . $file)) {
                createDirStructure($origin . '/' . $file, $destiny . '/' . $file, $filedata);
            } else {
                createFileV2($filedata, $origin, $destiny, $file);
            }
        }
    }
}

function createFile($plugin_str, $origin, $destiny, $file)
{
    $main_file = file_get_contents($origin . '/' . $file);
    $content = preg_replace('/Polymorph Bot/', $plugin_str['p_name'], $main_file);
    $content = preg_replace('/Polymorph_Bot/', str_replace(' ', '_', $plugin_str['p_name']), $content);
    $content = preg_replace('/polymorph_bot/', strtolower(str_replace(' ', '_', $plugin_str['p_name'])), $content);
    $content = preg_replace('/POLYMORPH_BOT/', strtoupper(str_replace(' ', '_', $plugin_str['p_name'])), $content);
    $content = preg_replace('/polymorph-bot/', strtolower(str_replace(' ', '-', $plugin_str['p_name'])), $content);

    $content = preg_replace('/Example/', $plugin_str['classname'], $content);
    $content = preg_replace('/example/', $plugin_str['classname'], $content);

    if(preg_match('/.*example.*/', $file)) {
        $newFile = preg_replace('/example/', strtolower(str_replace(' ', '_', $plugin_str['classname'])), $file);

        file_put_contents($destiny . '/' . $newFile, $content);

    } else {
        file_put_contents($destiny . '/' . $file, $content);
    }
}

/**
 * Create files
 * 
 * @param array $changes all the words to change
 * @param string $origin the origin path
 * @param string $destiny the destiny path
 * @param string $file the original filename
 * @return void
 */
function createFileV2(array $changes, string $origin, string $destiny, string $file)
{
    $file_content = file_get_contents($origin . '/' . $file);

    foreach($changes['words'] as $word){
        $file_content = preg_replace($word['original'], $word['new'], $file_content);
    }

    foreach($changes['filenames'] as $filename){
        if(preg_match('/.*'.$filename['original'].'.*/', $file)) {
            $newFile = preg_replace('/'.$filename['original'].'/', $filename['new'], $file);
    
            file_put_contents($destiny . '/' . $newFile, $file_content);
        } else {
            file_put_contents($destiny . '/' . $file, $file_content);
        }
    }
}

function createPostType($params)
{
    $structure_path = '../single-files/';
    $posttypes_path = './src/posttypes/';

    if (count($params) < 1) {
        echo "ERROR: Parameters are missing";
        return;
    }

    if (!is_dir($posttypes_path)) {
        mkdir($posttypes_path, 0777, true);
    }

    $filedata = [
        'words' => [
            ['original' => '/Name/', 'new' => str_replace(' ', '_', $params[0])],
            ['original' => '/name/', 'new' => str_replace(' ', '_', strtolower($params[0]))],
            ['original' => '/posttype/', 'new' => str_replace(' ', '_', $params[0])],
        ],
        'filenames' => [
            ['original' => 'name', 'new' => str_replace(' ', '_', strtolower($params[0]))],
        ]
    ];
    createFileV2($filedata, $structure_path, $posttypes_path, 'posttype-name.php');
}

function createMigrations()
{

}

function runMigrations()
{

}

function makeBuild()
{
    //Remplazar el uso de DB y Meta
}