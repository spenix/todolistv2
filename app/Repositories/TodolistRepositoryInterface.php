<?php

namespace App\Repositories;

interface TodolistRepositoryInterface
{
    public function getAll();
    public function getTodoDates();
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
