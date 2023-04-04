<?php

namespace App\Contracts;

interface IFileService
{
    /**
     * @param array $data
     * @return mixed
     */
    public function uploadFile(array $data);

    /**
     * @param $id
     * @param null $type
     * @return mixed
     */
    public function deleteFile($id, $type = null);

    /**
     * Buld Delete
     * @param null $type
     * @return mixed
     */
    public function deleteFiles($data, $type = null);

    /**
     * @param $projectId
     * @param $id
     * @return mixed
     */
    public function deleteFinalFile($projectId, $id);

    /**
     * @param $data
     * @return mixed
     */
    public function deleteFinalFiles($data);

    /**
     * @param $id
     * @return mixed
     */
    public function getById($id);

    /**
     * @param $projectId
     * @return mixed
     */
    public function getFiles($projectId);

    /**
     * @param $data
     * @return mixed
     */
    public function convertingPDF($data);

    /**
     * @param $arr
     * @return mixed
     */
    public function deleteMultipleFiles($arr);
}