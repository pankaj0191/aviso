<?php

namespace App\Contracts;


interface IProfileService
{
    /**
     * Update Profile
     * @param $user
     * @return mixed
     */
    public function updateDescription($user, $data);
    /**
     * Update Profile
     * @param $user
     * @return mixed
     */
    public function updateHourly($user, $data);
    /**
     * Update Profile
     * @param $user
     * @return mixed
     */
    public function updateNotify($user, $data);
    /**
     * Create Profile
     * @param $user
     * @param $data
     * @return mixed
     */
    public function createProfile($user, $data);
    /**
     * Create Profile
     * @param $user
     * @param $data
     * @return mixed
     */
    public function updateProfile($user, $data, $id);
    /**
     * Create Profile
     * @param $user
     * @param $data
     * @return mixed
     */
    public function updateUserInfo($user, $data);
    /**
     * Create Profile
     * @param $user
     * @param $data
     * @return mixed
     */
    public function updateNotification($user, $data);
    /**
     * Create Profile
     * @param $user
     * @param $data
     * @return mixed
     */
    public function updatePassword($user, $data);
    /**
     * Switch Profile
     * @param $user
     * @param $data
     * @return mixed
     */
    public function switchProfile($user, $data);
    /**
     * Switch Team
     * @param $user
     * @param $data
     * @return mixed
     */
    public function switchTeam($user, $data);
    /**
     * Switch Team
     * @param $user
     * @param $data
     * @return mixed
     */
    public function destroy($user, $id);
    /**
     * Switch Team
     * @param $user
     * @param $data
     * @return mixed
     */
    public function deletePhoto($user);

}
