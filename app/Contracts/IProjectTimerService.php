<?php

namespace App\Contracts;

interface IProjectTimerService
{
    public function getProjectTimersByUser($user);
    public function storeTimer($user, $data);
    public function updateTimer($data, $id);
    public function getOneProjectTimer($id);
    public function deleteTimer($id);
}
