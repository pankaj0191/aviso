<?php

namespace App\Observers;

use App\Contracts\IFileService;
use App\Project;

/**
 * Class ProjectObserve
 * @package App\Observers
 */
class ProjectObserve
{
    protected $fileService;

    /**
     * ProjectObserve constructor.
     */
    public function __construct(IFileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * @param Project $project
     */
    public function deleted(Project $project)
    {
        $files = [];
        foreach ($project->revisions as $revision) {
            foreach ($revision->proofs as $proof) {
                array_push($files, $proof->projectFiles->path);
            }
        }
        $this->fileService->deleteMultipleFiles($files);
    }
}
