<?php

namespace App\Contracts;

interface IClientRequestService
{
    /**
     * @param $user
     * @return mixed
     */
    public function getFreelancersByUser($user);
    /**
     * @param $user
     * @return mixed
     */
    public function getWrokers($user);
    /**
     * @param array $data
     * @return mixed
     */
    public function createProject(array $data);
    /**
     * @param array $data
     * @return mixed
     */
    public function fetchRequest($data);
    /**
     * @param array $data
     * @return mixed
     */
    public function getFiles($projectID);
    /**
     * @param array $data
     * @return mixed
     */
    public function createInstruction($data);
    /**
     * @param array $data
     * @return mixed
     */
    public function createDimension($data);
    /**
     * @param array $data
     * @return mixed
     */
    public function createDescription($data);
    /**
     * @param array $data
     * @return mixed
     */
    public function createSummary($data);
    /**
     * @param array $data
     * @return mixed
     */
    public function draft($data);
    /**
     * @param array $data
     * @return mixed
     */
    public function updateName($data);
}
