<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DriveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $routes = [];

        $user = Auth::user();

        $projectFiles = User::with('projects.revisions.proofs.issues.comments', 'projects.finalFiles')->find($user->id);

        foreach ($projectFiles->projects as $project) {

            foreach ($project->finalFiles as $finalFile) {
                $routes[] = '/' . $finalFile->path;
            }
            foreach ($project->revisions as $revision) {
                foreach ($revision->proofs as $proof) {
                    $routes[] = '/' . $proof->projectFiles->path;
                    foreach ($proof->issues as $issue) {
                        foreach ($issue->files as $issueFile) {
                            $routes[] = '/' . $issueFile->path;
                        }
                        foreach ($issue->comments as $comment) {
                            foreach ($comment->files as $commentFile)
                                $routes[] = '/' . $commentFile->path;
                        }
                    }
                }
            }
        }
        // return $routes;

        $routeList = $this->routesToArray($routes);
        // dd($routeList);
        $routeTree = [
            'data' => $this->getTreeBranches($routeList)
        ];
        return json_encode($routeTree, JSON_PRETTY_PRINT);
    }

    public function routesToArray($routes)
    {

        $routeList = [];

        foreach ($routes as $route) {
            if (Storage::disk('spaces')->exists($route)) {
                if (substr_count($route, '/') > 1) {
                    $pathName = Str::after($route, '/');
                    $pathName = $this->beforeLast($pathName, '/');
                    $pathName = $pathName . '/!@_files';
                } else {
                    $pathName = '!@_files';
                }

                $pathName = str_replace('/', '.', $pathName);

                $fileName = $this->afterLast($route, '/');

                $existingFiles = Arr::get($routeList, $pathName) ?? [];

                // $newPathName = Str::after($route, '/');
                // strstr($newPathName, '/');
                // $newPathName = Str::after($route, '/');
                $pathInfo = pathinfo($route);
                $newFile = [
                    [
                        'file' => $fileName,
                        'url' => $route,
                        'type' => $pathInfo['extension'],
                        // 'size' => Storage::disk('spaces')->size($route)
                        'size' => ''
                    ]
                ];

                $files = array_merge($existingFiles, $newFile);

                Arr::set($routeList, $pathName, $files);
            }
        }

        return $routeList;
    }

    public function getTreeBranches($paths)
    {
        $tree = [];

        foreach ($paths as $key => $path) {
            if ($key === '!@_files') {
                $tree = array_merge($tree, $path);
                continue;
            }

            $tree[] = [
                'label' => strval($key),
                'children' => $this->getTreeBranches($path),
            ];
        }

        return $tree;
    }

    /**
     * Return the remainder of a string after the last occurrence of a given value.
     *
     * @param  string  $subject
     * @param  string  $search
     * @return string
     */
    public static function afterLast($subject, $search)
    {
        if ($search === '') {
            return $subject;
        }

        $position = strrpos($subject, (string) $search);

        if ($position === false) {
            return $subject;
        }

        return substr($subject, $position + strlen($search));
    }

    /**
     * Get the portion of a string before the last occurrence of a given value.
     *
     * @param  string  $subject
     * @param  string  $search
     * @return string
     */
    public static function beforeLast($subject, $search)
    {
        if ($search === '') {
            return $subject;
        }

        $pos = mb_strrpos($subject, $search);

        if ($pos === false) {
            return $subject;
        }

        return substr($subject, 0, $pos);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request)
    {
        return Storage::download($request->url);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
