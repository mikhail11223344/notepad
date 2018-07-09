<?php

namespace App\controllers;

use App\components\Database;
use League\Plates\Engine;

class TasksController
{
    private $engine;
    private $database;

    public function __construct(Engine $view, Database $database)
    {
        $this->engine = $view;
        $this->database = $database;
    }

    public function index()
    {
        $tasks = $this->database->all('tasks');
        echo $this->engine->render('index', ['tasks' => $tasks]);
    }

    public function show($id)
    {
        $task = $this->database->getOne('tasks', $id);
        echo $this->engine->render('show', ['task' => $task]);
    }

    public function create()
    {
        echo $this->engine->render('create');
    }

    public function edit($id)
    {
        $task = $this->database->getOne('tasks', $id);
        echo $this->engine->render('edit', ['task' => $task]);
    }

    public function update($id)
    {
        $this->database->update('tasks', $id, $_POST);
        header("Location: /tasks");
    }

    public function store()
    {
        $this->database->store('tasks', $_POST);
        header("Location: /tasks");
    }

    public function delete($id)
    {
        $this->database->delete('tasks', $id);
        header("Location: /tasks");
    }
}