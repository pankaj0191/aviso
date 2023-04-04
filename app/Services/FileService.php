<?php

namespace App\Services;

use Image;
use App\User;
use App\Issue;
use App\Proof;
use App\Comment;
use App\Project;
use App\Revision;
use App\IssueFile;
use Carbon\Carbon;
use VideoThumbnail;
use App\CommentFile;
use App\ProjectFile;
use App\Notification;
use App\ProjectAssets;
use App\UnreadComment;
use App\Events\PdfReaded;
use App\Events\PdfUploaded;
use Illuminate\Support\Str;
use App\Contracts\IFileService;
use App\Events\PdfPageConverted;
use App\Services\BootstrapService;
use App\Contracts\IBootstrapService;
use Illuminate\Support\Facades\Storage;

class FileService implements IFileService
{
    protected $disk;
    protected $testDisk;
    protected $bootstrapService;

    /**
     * File upload service constructor
     */
    public function __construct()
    {
        $this->disk = Storage::disk(config('filesystems.default'));
        $this->testDisk = Storage::disk('public');
    }

    public function uploadFile(array $data, $requestType = null)
    {
        $folderType = '';
        switch ($data['owner_type']) {
            case 'issue':
                $folderType = 'Proofs/Issues';
                break;
            case 'comment':
                $folderType = 'Proofs/Comments';
                break;
            case 'proof':
                $folderType = 'Proofs';
                break;
            case 'assets':
                $folderType = 'Design-Request';
                break;
        }
        $proofService = new ProofService(
            new NotificationService(new Notification()),
            new UnreadCommentsService(new UnreadComment())
        );
        if (isset($data['user_id']) && $data['user_id'] > 0) {
            $user = User::find($data['user_id']);
        } else {
            $user = auth()->user();
        }
        $project = Project::where('id', $data['project_id'])->first();
        $assetsCount = $project->projectAssets()->count();
        $countProofs = Proof::where('revision_id', $project->getActiveRevision($data['project_id'])->id)->count();

        if ($requestType) {
            try {
                if ($requestType == 'PDF') {
                    $file = $data['photos'];


                    $stream_image = Image::make($this->testDisk->get($file))->encode('jpg', 95)->stream();
                    // $stream_thumb = Image::make($this->testDisk->get($file))->encode('jpg', 95)->stream();

                    switch ($data['owner_type']) {
                        case 'assets':
                            $big = 'projects/' . Carbon::now()->year . '/' . Str::slug($project->name)  . '-' . $project->id . '/' . $folderType . '/' . Str::slug($project->name) . 'UploadedPhoto' . ($assetsCount + 1) . '.jpg';
                            $thumb = $big;
                            // $thumb = 'projects/' . Carbon::now()->year . '/' . Str::slug($project->name) . '-' . $project->id . '/' . $folderType . '/' . Str::slug($project->name) . 'UploadedPhotoThumb' . ($assetsCount + 1) . '.jpg';
                            break;
                        case 'proof':
                            $big = 'projects/' . Carbon::now()->year . '/' . Str::slug($project->name)  . '-' . $project->id . '/' . $folderType . '/' . Str::slug($project->name) . 'RR' . ($countProofs + 1) . '.jpg';
                            $thumb = $big;
                            // $thumb = 'projects/' . Carbon::now()->year . '/' . Str::slug($project->name)  . '-' . $project->id . '/' .  $folderType . '/' . Str::slug($project->name) . 'RRThumb' . ($countProofs + 1) . '.jpg';
                            break;
                        default:
                            $big = 'projects/' . Carbon::now()->year . '/' . Str::slug($project->name)  . '-' . $project->id . '/' . $folderType . '/' . Str::random(40) . '.jpg';
                            $thumb = $big;
                            // $thumb = 'projects/' . Carbon::now()->year . '/' . Str::slug($project->name)  . '-' . $project->id . '/' .  $folderType . '/' . Str::random(40) . '.jpg';
                            break;
                    }

                    $this->disk->put($big, $stream_image, 'public');
                    // $this->disk->put($thumb, $stream_thumb, 'public');
                    $this->testDisk->delete($file);
                }
                else {
                    if ($data['file_type'] == 'video') {
                        $video = $this->testDisk->putFile('projects', $data['photos']);

                        $this->check_user_storage_capacity($user, $video);

                        $newVideo = storage_path('app/public/projects/' . Str::random(40) . '.webm');
                        exec('ffmpeg -i ' . storage_path('app/public/' . $video) . ' -vcodec copy -acodec copy ' . $newVideo);
                        $this->testDisk->delete($video);
                        $pathInfo = pathinfo($data['photos']->getClientOriginalName());
                        $bigFile = 'projects/' . Carbon::now()->year . '/' . Str::slug($project->name)  . '-' . $project->id . '/' . 'Proofs';
                        $thumbFile = 'projects/' . Carbon::now()->year . '/' . Str::slug($project->name)  . '-' . $project->id . '/' . 'Proofs';
                        $this->disk->putFileAs($bigFile, $data['photos'], Str::slug($project->name) . 'RRVideo' . ($countProofs + 1) . '.' . $pathInfo['extension'],  'public');
                        $this->disk->putFileAs($thumbFile, $data['photos'], Str::slug($project->name) . 'RRThumbVideo' . ($countProofs + 1) . '.' . $pathInfo['extension'],  'public');
                        $big = 'projects/' . Carbon::now()->year . '/' . Str::slug($project->name)  . '-' . $project->id . '/' . 'Proofs' . '/' . Str::slug($project->name) . 'RRVideo' . ($countProofs + 1) . '.' . $pathInfo['extension'];
                        $thumb = 'projects/' . Carbon::now()->year . '/' . Str::slug($project->name)  . '-' . $project->id . '/' . 'Proofs' . '/' . Str::slug($project->name) . 'RRThumbVideo' . ($countProofs + 1) . '.' . $pathInfo['extension'];

                        // $path = 'projects/' . Carbon::now()->year . '/' . Str::slug($project->name)  . '-' . $project->id . '/' . $folderType . '/';

                        // $thumb = Str::slug($project->name) . 'RRThumb' . ($countProofs + 1) . '.jpg';
                        // $big = Str::slug($project->name) . 'RR' . ($countProofs + 1) . '.' . $pathInfo['extension'];

                        // if (file_exists(storage_path('app/public/' . $newVideo))) {
                        //     VideoThumbnail::createThumbnail(storage_path('app/public/' . $newVideo), storage_path('app/public/projects/'), $thumb,  2, 600, 600);

                        //     $data['file_type'] = 'video';
                        //     $thumbEdit = 'projects/' . $thumb;

                        //     $this->disk->put($path . '/' . $thumb, $this->testDisk->get($thumbEdit),  'public');
                        //     $this->disk->putFileAs($path, $data['photos'], $big,  'public');

                        //     $this->testDisk->delete($newVideo);
                        //     $this->testDisk->delete($thumbEdit);

                        //     $thumb = $path . $thumb;
                        //     $big = $path . $big;
                        // }
                    } else {
                        $name = 'projects/' . Str::random(40) . '.jpg';
                        $this->testDisk->put($name, $data['photos']);
                        $this->check_user_storage_capacity($user, $video);

                        $stream_image = Image::make($this->testDisk->get($name))->encode('jpg', 95)->stream();
                        // $stream_thumb = Image::make($this->testDisk->get($name))->encode('jpg', 95)->stream();

                        switch ($data['owner_type']) {
                            case 'assets':
                                $big = 'projects/' . Carbon::now()->year . '/' . Str::slug($project->name)  . '-' . $project->id . '/' . $folderType . '/' . Str::slug($project->name) . 'UploadedPhoto' . ($assetsCount + 1) . '.jpg';
                                $thumb = $big;
                                // $thumb = 'projects/' . Carbon::now()->year . '/' . Str::slug($project->name) . '-' . $project->id . '/' . $folderType . '/' . Str::slug($project->name) . 'UploadedPhotoThumb' . ($assetsCount + 1) . '.jpg';
                                break;
                            case 'proof':
                                $big = 'projects/' . Carbon::now()->year . '/' . Str::slug($project->name)  . '-' . $project->id . '/' . $folderType . '/' . Str::slug($project->name) . 'RR' . ($countProofs + 1) . '.jpg';
                                $thumb = $big;
                                // $thumb = 'projects/' . Carbon::now()->year . '/' . Str::slug($project->name) . '-' . $project->id . '/' . $folderType . '/' . Str::slug($project->name) . 'RRThumb' . ($countProofs + 1) . '.jpg';
                                break;
                            default:
                                $big = 'projects/' . Carbon::now()->year . '/' . Str::slug($project->name)  . '-' . $project->id . '/' . $folderType . '/' . Str::random(40) . '.jpg';
                                $thumb = $big;
                                // $thumb = 'projects/' . Carbon::now()->year . '/' . Str::slug($project->name) . '-' . $project->id . '/' . $folderType . '/' . Str::random(40) . '.jpg';
                                break;
                        }

                        $this->disk->put($big, $stream_image, 'public');
                        // $this->disk->put($thumb, $stream_thumb, 'public');
                        $this->testDisk->delete($name);
                    }
                }
            } catch (\Exception $e) {
                return null;
            }
        }
        else {
            $videoCheck = array('video/mp4', 'video/quicktime', 'video/x-ms-wmv', 'video/x-msvideo', 'video/webm', 'video/mpeg-2', 'video/x-flv');

            if (!in_array($data['photos']->getMimeType(), $videoCheck)) {

                if ($data['owner_type'] == 'final') {
                    $finalCount = $project->finalFiles()->count();
                    $pathInfo = pathinfo($data['photos']->getClientOriginalName());
                    $image = $this->testDisk->putFile('projects/final-files', $data['photos']);
                    $this->check_user_storage_capacity($user, $image);
                    $path = 'projects/' . Carbon::now()->year . '/' . Str::slug($project->name)  . '-' . $project->id . '/' .  'Final Files';
                    $file =  $this->disk->putFileAs($path, $data['photos'], Str::slug($project->name) . 'FinalFile' . ($finalCount + 1) . '.' . $pathInfo['extension'],  'public');
                    $size = $this->disk->size($file);
                    $finalFile = $project->finalFiles()->create(['path' => $file, 'size', $size]);
                    $this->testDisk->delete($image);
                    return [
                        'id' => $finalFile->id,
                        'path' => $finalFile->path,
                    ];
                }
                $image = $this->testDisk->putFile('projects', $data['photos']);
                $this->check_user_storage_capacity($user, $image);

                $stream_image = Image::make($this->testDisk->get($image))->stream();
//                $stream_image = Image::make($this->testDisk->get($image))->encode('jpg', 95)->stream();

                // $stream_thumb = Image::make($this->testDisk->get($image))->encode('jpg', 95)->stream();


                switch ($data['owner_type']) {
                    case 'assets':
                        $big = 'projects/' . Carbon::now()->year . '/' . Str::slug($project->name)  . '-' . $project->id . '/' . $folderType . '/' . Str::slug($project->name) . 'UploadedPhoto' . ($assetsCount + 1) . Str::random(6) . '.jpg';
                        $thumb = $big;
                        // $thumb = 'projects/' . Carbon::now()->year . '/' . Str::slug($project->name) . '-' . $project->id . '/' . $folderType . '/' . Str::slug($project->name) . 'UploadedPhotoThumb' . ($assetsCount + 1) . Str::random(6) . '.jpg';
                    case 'proof':
                        $big = 'projects/' . Carbon::now()->year . '/' . Str::slug($project->name)  . '-' . $project->id . '/' . $folderType . '/' . Str::slug($project->name) . 'RR' . ($countProofs + 1) . Str::random(6) . '.jpg';
                        $thumb = $big;
                        // $thumb = 'projects/' . Carbon::now()->year . '/' . Str::slug($project->name) . '-' . $project->id . '/' . $folderType . '/' . Str::slug($project->name) . 'RRThumb' . ($countProofs + 1) . Str::random(6) . '.jpg';
                        break;
                    default:
                        $big = 'projects/' . Carbon::now()->year . '/' . Str::slug($project->name)  . '-' . $project->id . '/' . $folderType . '/' . Str::random(40) . '.jpg';
                        $thumb = $big;
                        // $thumb = 'projects/' . Carbon::now()->year . '/' . Str::slug($project->name)  . '-' . $project->id . '/' .  $folderType . '/' . Str::random(40) . '.jpg';
                        break;
                }

                $this->disk->put($big, $stream_image, 'public');
                // $this->disk->put($thumb, $stream_thumb, 'public');
                $this->testDisk->delete($image);
            }
            else {
                $video = $this->testDisk->putFile('projects', $data['photos']);
                $this->check_user_storage_capacity($user, $video);
                $path = 'projects/' . Carbon::now()->year . '/' . Str::slug($project->name)  . '-' . $project->id . '/' . $folderType . '/';

                $big = Str::slug($project->name) . 'RR' . ($countProofs + 1) . '.' . (pathinfo($video, PATHINFO_EXTENSION));
                $thumb = Str::slug($project->name) . 'RRThumb' . ($countProofs + 1) . '.jpg';

                if (file_exists(storage_path('app/public/' . $video))) {
                    $videoThumb = VideoThumbnail::createThumbnail(storage_path('app/public/' . $video), storage_path('app/public/projects/'), $thumb,  2, 600, 600);

                    $data['file_type'] = 'video';
                    $thumbEdit = 'projects/' . $thumb;

                    $this->disk->put($path . '/' . $thumb, $this->testDisk->get($thumbEdit),  'public');
                    $this->disk->putFileAs($path, $data['photos'], $big,  'public');

                    // $this->disk->put($path .$thumb, $stream_thumb, 'public');
                    // $this->disk->putFileAs($path, $data['photos'], $big);
                    $this->testDisk->delete($video);
                    $this->testDisk->delete($thumbEdit);

                    $thumb = $path . $thumb;
                    $big = $path . $big;
                }
                // \Log::error($this->disk);
            }
        }

        if ($thumb && $big) {
            $data['thumb_path'] = $thumb;
            $data['path'] = $big;
            $data['size'] = $this->disk->size($data['path']);
            $this->traffic($data['size'], $project->id, $data['user_id']);
            switch ($data['owner_type']) {
                case 'issue':
                    $issue = Issue::findOrFail($data['issue_id']);
                    $uploadedFile = $issue->files()->create([
                        'path' => $big,
                        'thumb_path' => $thumb,
                        'user_id' => $data['user_id'],
                        'size' => $data['size']
                    ]);
                    break;
                case 'comment':
                    $comment = Comment::findOrFail($data['comment_id']);
                    $uploadedFile = $comment->files()->create([
                        'path' => $big,
                        'thumb_path' => $thumb,
                        'user_id' => $data['user_id'],
                        'size' => $data['size']
                    ]);
                    break;
                case 'proof':
                    $uploadedFile = ProjectFile::store($data);
                    $proof = $proofService->createProof($uploadedFile);
                    break;
                case 'assets':
                    $project_asset = new ProjectAssets();
                    $project_asset->project_id = $data['project_id'];
                    $project_asset->url = $data['path'];
                    $project_asset->thumb_path = $data['thumb_path'];
                    $project_asset->size = $data['size'];
                    $project_asset->type = 'assets';

                    $project_asset->save();
                    $uploadedFile = $project_asset;
                    break;
            }

            return [
                'id' => $uploadedFile->id,
                'path' => $uploadedFile->path ?: $uploadedFile->url,
                'thumb' => $uploadedFile->thumb_path
            ];
        }
    }

    /**
     * Check if user has enough space to upload file
     *
     * @param $user_id
     * @param int $file_size
     * @param $temp_filename
     */
    private function check_user_storage_capacity($user, $temp_filename): void
    {

        $file_size = Storage::disk('public')->size($temp_filename);
        $used = $this->userStorage($user)['total'];
        // Get user storage percentage and get storage_limitation setting
        $user_storage_used = user_storage_percentage($used, $user, $file_size);

        // Check if user can upload
        if ($user_storage_used >= 100) {

            // Delete file
            Storage::disk('public')->delete($temp_filename);

            // Abort uploading
            abort(423, 'You exceed your storage limit!');
        }
    }


    /**
     * Get user profile
     * @param $user
     * @return mixed
     */
    public function userStorage($user)
    {
        $projectFiles = $user->load(['currentProfile.subCategories.files', 'projects' => function ($q) use ($user) {
            $q->where('created_by', $user->id)->select('id', 'name', 'created_by')->with('projectAssets', 'finalFiles', 'revisions.proofs.issues.comments');
        }]);

        $sizeArray = [];
        $projectFileSize = 0;
        $projectAssetSize = 0;
        $projectFinalFileSize = 0;
        $issueFilesSize = 0;
        $commentFilesSize = 0;
        $subCategoryFilesSize = 0;


        if ($projectFiles->currentProfile->subCategories && count($projectFiles->currentProfile->subCategories) > 0) {
            // Current Profile get sub categories fiels
            foreach ($projectFiles->currentProfile->subCategories as $subCat) {
                // Subactegoies Files
                if (count($subCat->files) > 0) {
                    foreach ($subCat->files as $subFiles) {
                        // Size Files of sub categoreis
                        $subCategoryFilesSize += $subFiles->size;
                    }
                }
            }
        }

        if (count($projectFiles->projects) > 0) {
            // Projects
            foreach ($projectFiles->projects as $project) {
                if (count($project->finalFiles) > 0) {
                    // Final Files
                    foreach ($project->finalFiles as $finalFile) {
                        // Final Size
                        $projectFinalFileSize += $finalFile->size;
                    }
                }
                if (count($project->projectAssets) > 0) {
                    // Final Files
                    foreach ($project->projectAssets as $asset) {
                        // Asset Size
                        $projectAssetSize += $asset->size;
                    }
                }

                if (count($project->revisions) > 0) {
                    // Revisions
                    foreach ($project->revisions as $revision) {
                        if (count($revision->proofs) > 0) {
                            // Project Files
                            foreach ($revision->proofs as $proof) {
                                // Size of Project Files
                                $projectFileSize += $proof->projectFiles->size;
                                if (count($proof->issues) > 0) {
                                    // Issues
                                    foreach ($proof->issues as $issue) {
                                        if (count($issue->files) > 0) {
                                            // Issue Files
                                            foreach ($issue->files as $issueFile) {
                                                // Issue file size
                                                $issueFilesSize += $issueFile->size;
                                            }
                                        }
                                        if (count($issue->comments) > 0) {
                                            // Comments
                                            foreach ($issue->comments as $comment) {
                                                if (count($comment->files) > 0) {
                                                    // Comment Files
                                                    foreach ($comment->files as $commentFile) {
                                                        // Comment file size
                                                        $commentFilesSize += $commentFile->size;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $sizeArray['project_size'] = $projectFileSize;
        $sizeArray['project_asset_size'] = $projectAssetSize;
        $sizeArray['project_final_size'] = $projectFinalFileSize;
        $sizeArray['issue_size'] = $issueFilesSize;
        $sizeArray['comment_size'] = $commentFilesSize;
        $sizeArray['sub_category_size'] = $subCategoryFilesSize;
        $sizeArray['total'] = $projectFileSize + $projectAssetSize + $projectFinalFileSize + $issueFilesSize + $commentFilesSize + $subCategoryFilesSize;

        $sizeArray['total_formate'] = formatBytes($sizeArray['total']);
        return $sizeArray;
    }

    private function traffic($file_size, $project_id)
    {
        $project = Project::find($project_id);
        if (count($project->userRoleAgency) > 0) {
            User::find($project->userRoleAgency[0]->id)->currentProfile->record_upload($file_size);
        } else if (count($project->userRoleFreelancer) == 1) {
            User::find($project->userRoleFreelancer[0]->id)->currentProfile->record_upload($file_size);
        } else {
            User::find($project->userRoleClient[0]->id)->currentProfile->record_upload($file_size);
        }
    }


    public function getById($id)
    {
        if ($id > 0) {
            return ProjectFile::findOrFail($id);
        }
    }

    public function getFiles($projectId)
    {
        $project = new Project();
        $revision = new Revision();
        $project = $project->with('instructions', 'subCategories.category', 'subCategories.files', 'projectAssets', 'projectPhotos', 'userRoleFreelancer', 'owner')->where('id', $projectId)->first();


        if ($projectId > 0) {
            $activeRevision = $project->getActiveRevision($projectId);
            $revision = $revision->getRevisionById($activeRevision->id);
            return [
                'revision' => $revision->proofs,
                'project' => $project,
            ];
        }
    }

    /**
     * @param $id
     * @param $type
     * @return mixed
     */
    public function deleteFile($id, $type = null)
    {
        switch ($type) {
            case 'issue':
                $file = IssueFile::findOrFail($id);
                break;
            case 'comment':
                $file = CommentFile::findOrFail($id);
                break;
            case 'assets':
                $file = ProjectAssets::findOrFail($id);
                break;
            case 'proof':
                $file = ProjectFile::findOrFail($id);
                $proof = Proof::where('project_files_id', $file->id)->first();
                $issues = $proof->issues;

                //Deleting proof issues comments
                foreach ($issues as $issue) {
                    $issue->comments()->delete();
                }

                //Deleting proof issues
                $proof->issues()->delete();

                //Delete proof
                $proof->delete();
            default:
                $file = ProjectFile::findOrFail($id);
                break;
        }

        $path = $file->path;
        $thumb_path = $file->thumb_path;
        //        $owner_type = $file->owner_type;

        //Deleting file from server
        $this->disk->delete($path);
        $this->disk->delete($thumb_path);

        //TODO: Change Logic when deleting single proof
        //Updating data in database
        //        if ($owner_type == 'proof') {
        //            //Deleting file data from database
        //            $file->delete();
        //            $proof = Proof::where('project_files_id', $id)->first();
        //            $proof->delete();
        //            return $proof;
        //        }

        return $file->delete();
    }

    /**
     * @param $id
     * @param $type
     * @return mixed
     */
    public function deleteFiles($data, $type = null)
    {
        foreach ($data['files'] as $file) {
            $file = json_decode($file);
            switch ($type) {
                case 'issue':
                    $file = IssueFile::findOrFail($file->id);
                    break;
                case 'comment':
                    $file = CommentFile::findOrFail($file->id);
                    break;
                case 'assets':
                    $file = ProjectAssets::findOrFail($file->id);
                    break;
                case 'proof':
                    $projectFile = ProjectFile::findOrFail($file->id);
                    $proof = Proof::where('project_files_id', $projectFile->id)->first();
                    $issues = $proof->issues;

                    //Deleting proof issues comments
                    foreach ($issues as $issue) {
                        $issue->comments()->delete();
                    }

                    //Deleting proof issues
                    $proof->issues()->delete();

                    //Delete proof
                    $proof->delete();
                default:
                    $file = ProjectFile::findOrFail($file->id);
                    break;
            }

            $path = $file->path;
            $thumb_path = $file->thumb_path;
            //        $owner_type = $file->owner_type;

            //Deleting file from server
            $this->disk->delete($path);
            $this->disk->delete($thumb_path);

            //TODO: Change Logic when deleting single proof
            //Updating data in database
            //        if ($owner_type == 'proof') {
            //            //Deleting file data from database
            //            $file->delete();
            //            $proof = Proof::where('project_files_id', $id)->first();
            //            $proof->delete();
            //            return $proof;
            //        }
            $file->delete();
        }
        return '';
    }

    /**
     * @param $arr
     * @return mixed|void
     */
    public function deleteMultipleFiles($arr)
    {
        $this->disk->delete($arr);
    }

    /**
     * Delete final file
     * @param $projectId
     * @param $id
     * @return mixed
     */
    public function deleteFinalFile($projectId, $id)
    {
        $project = Project::where('id', $projectId)->first();
        $file = $project->finalFiles()->where('id', $id)->first();
        $this->disk->delete($file->path);
        $file->delete();
        return $file;
    }

    /**
     * Delete project final files
     * @param $data
     * @return mixed
     */
    public function deleteFinalFiles($data)
    {
        $project = Project::where('id', $data['projectId'])->first();
        $files = $project->finalFiles()->whereIn('id', $data['ids'])->get();
        if ($files) {
            foreach ($files as $file) {
                $this->disk->delete($file->path);
                $file->delete();
            }
        }
        return $project;
    }

    /**
     * Converting PDF pages to images
     * @param $data
     * @return array
     * @throws \ImagickException
     */
    public function convertingPDF($data)
    {
        $convertedImages = [];
        $user = User::find($data['user_id']);
        //Saving PDF file as temporary file
        $pdf = $this->testDisk->putFile('pdf', $data['photos']);
        $this->check_user_storage_capacity($user, $pdf);

        //Broadcast PdfUploaded event
        broadcast(new PdfUploaded());

        if ($data['owner_type'] == 'final') {
            $project = Project::where('id', $data['project_id'])->first();
            $finalFile = $project->finalFiles()->create(['path' => $pdf]);
            return [
                'id' => $finalFile->id,
                'path' => $finalFile->path,
                'type' => 'pdf'
            ];
        }



        //Reading PDF file
        // $pdflib = new PDFLib();
        // $pdflib->setPdfPath('storage/' . $pdf);
        // $pdflib->setOutputPath('storage/pictures');
        // $pdflib->setImageFormat(PDFLib::$IMAGE_FORMAT_JPEG);
        // $pdflib->setDPI(300);
        // $pdflib->setPageRange(1, $pdflib->getNumberOfPages());
        // $pdflib->convert();

        $imgExt = new \Imagick();
        $imgExt->setResolution(300, 300);
        $imgExt->readImage('storage/' . $pdf);
        $imgExt->setImageResolution(300, 300);
        $imgExt->setImageFormat("jpg");
        $imgExt->setImageCompression(\imagick::COMPRESSION_JPEG);
        $imgExt->setImageCompressionQuality(100);

        $imgExt->writeImages('storage/pictures/page.jpg', true);

        //Get total number of pages
        $totalPages = $imgExt->getNumberImages();

        //Broadcast PdfReaded event
        broadcast(new PdfReaded($totalPages));


        //Create proofs from converted images
        for ($i = 1; $i <= $totalPages; $i++) {
            $data['photos'] = 'pictures/page-' . ($i - 1) . '.jpg';
            $file = $this->uploadFile($data, 'PDF');
            //Broadcast PdfPageConverted event
            broadcast(new PdfPageConverted($i, $file ? true : false));

            //Saving converted image data
            if ($file) {
                $convertedImages[] = [
                    'id' => $file['id'],
                    'path' => $file['path'],
                    'thumb' => array_key_exists('thumb_path', $file) ? $file['thumb_path'] : ''
                ];
            }
        }

        //Deleting temporary PDF file
        $this->testDisk->delete($pdf);

        //Return converted images
        return $convertedImages;
    }

    /**
     * move all old files to space
     */
    public function moveToCloud()
    {
        $publicManager = Storage::disk('local');
        try {
            $subDirs = $publicManager->allFiles('');
            foreach ($subDirs as $dir) {
                $file = $publicManager->get($dir);
                $this->disk->put($dir, $file, 'public');
                //$publicManager->delete($dir);
            }
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
        }
    }
}
