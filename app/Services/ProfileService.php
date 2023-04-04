<?php

namespace App\Services;


use App\User;
use App\Profile;
use App\ProfileType;
use App\ProfileDescription;
use App\Contracts\IProfileService;
use Illuminate\Support\Facades\Storage;

class ProfileService implements IProfileService
{
    /**
     * @var User
     */
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Update Pofile by Id
     * @param $user
     * @return mixed
     */
    public function updateDescription($user, $data)
    {
        ProfileDescription::updateOrCreate(
            ['id' => $data->id],
            [
                'profle_name' => 'General Profile',
                'title' => $data->title,
                'body' => $data->desc,
                'profile_id' => $user->currentProfile->id
            ]
        );
        return [
            'profile' => ProfileDescription::where('profile_id', $data['username'])->first(),
            'userData' => $user->load('profiles', 'projectReviews.projectTimers'),
        ];
    }

    /**
     * Update Pofile by Id
     * @param $user
     * @return mixed
     */
    public function updateHourly($user, $data)
    {
        $user->currentProfile->profileDescription->update(
            [
                'hourly_rate' => $data->hourly_rate,
            ]
        );
        return [
            'profile' => ProfileDescription::where('profile_id', $data['username'])->first(),
            'userData' => $user->load('profiles', 'projectReviews.projectTimers'),
        ];
    }

    /**
     * Update Pofile by Id
     * @param $user
     * @return mixed
     */
    public function updateNotify($user, $data)
    {
        $user->emailNotifications->update(
            [
                'review_project' => $data->review_project,
            ]
        );
        return $user;
    }

    /**
     * Create new profile
     * @param $user
     * @param $data
     * @return mixed
     */
    public function createProfile($user, $data)
    {
        $profileType = ProfileType::where('name', $data->profile)->first();
        $profile = Profile::create([
            'user_id' => $user->id,
            'profile_type_id' => $profileType->id,
            'name' => $data->name
        ]);

        ProfileDescription::updateOrCreate(
            ['profile_id' => $profile->id],
            [
                'profle_name' => 'General Profile',
                'title' => '',
                'body' => '',
                'profile_id' => $profile->id
            ]
        );
        
        return $user->profiles->load('profileType');
    }

    /**
     * Create new profile
     * @param $user
     * @param $data
     * @return mixed
     */
    public function updateProfile($user, $data, $id)
    {
        $profile = Profile::find($id);
        $profile->name = $data['name'];
        $profile->save();
        
        return $user->profiles->load('profileType');
    }

    /**
     * Create new profile
     * @param $user
     * @param $data
     * @return mixed
     */
    public function updateUserInfo($user, $data)
    {
        if ($data->has('email') || $data->has('name')) {
            $user->currentProfile->name = $data['name'];
            $user->email = $data['email'];
        }
        if ($data->hasFile('photo')) {
            Storage::disk(config('filesystems.default'))->delete($user->photo_url);
            $url = env('APP_SPACES_PREFIX') . '/' . $data['photo']->store('profiles', config('filesystems.default'));
            $user->photo_url = $url;
        }
        $user->save();
        $user->currentProfile->save();
        
        return $user;
    }

    /**
     * Create new profile
     * @param $user
     * @param $data
     * @return mixed
     */
    public function updateNotification($user, $data)
    {
        $user->emailNotifications()->update($data->all());
        return $user->emailNotifications;
    }

    /**
     * Create new profile
     * @param $user
     * @param $data
     * @return mixed
     */
    public function updatePassword($user, $data)
    {
        $user->password = \Hash::make($data['password']);
        $user->save();
        
        return $user;
    }

    /**
     * switch profile
     * @param $user
     * @param $data
     * @return mixed
     */
    public function switchProfile($user, $data)
    {
        $user->current_profile_id = $data->profile_id;
        $user->save();
        return $user->current_profile_id;
    }

    /**
     * switch profile
     * @param $user
     * @param $data
     * @return mixed
     */
    public function switchTeam($user, $data)
    {
        $user->current_team_id = $data->team_id;
        $user->save();
        return $user->current_team_id;
    }

    /**
     * switch profile
     * @param $user
     * @param $data
     * @return mixed
     */
    public function destroy($user, $id)
    {
        Profile::destroy($id);
        return $user->profiles->load('profileType');
    }

    /**
     * switch profile
     * @param $user
     * @param $data
     * @return mixed
     */
    public function deletePhoto($user)
    {
        Storage::disk(config('filesystems.default'))->delete($user->photo_url);
        $user->photo_url = null;
        $user->save();
        return $user;
    }
}
