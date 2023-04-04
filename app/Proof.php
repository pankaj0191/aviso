<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Issue;
use Illuminate\Support\Facades\Auth;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class Proof extends Model
{
    use HasEagerLimit;

    protected $fillable = [
        'canvas_data',
        'revision_id',
        'project_files_id',
        'status',
        'page_num'
    ];

    /**
     * Proof revision
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function revision()
    {
        return $this->belongsTo(Revision::class);
    }

    /**
     * Proof file
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function projectFiles()
    {
        return $this->belongsTo(ProjectFile::class);
    }

    /**
     * Proof issues
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function issues()
    {
        return $this->hasMany(Issue::class)->with('files')->with('user');
    }

    /**
     * Proof unread comments
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function unreadComments()
    {
        return $this->hasMany(UnreadComment::class, 'proof_id')->where('user_id', '=', Auth::user()->id);
    }


    public static function store($project_file)
    {
        try {
            $proof = new Proof();
            $countProofs = $proof->where('revision_id', $project_file->revision->id)->count();
            $proof->page_num = $countProofs + 1;
            $proof->status = 'created';
            $proof->projectFiles()->associate($project_file);
            $proof->revision()->associate($project_file->revision);
            $proof->save();
            return $proof;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function getByRevisionId($revision_id)
    {
        try {
            $proofs = Proof::where('revision_id', $revision_id)->orderBy('id', 'DESC')->get();
            return $proofs;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function getByRevisionVersion($version)
    {
        try {
            $proofs = Proof::where('revision_id', $version)->orderBy('id', 'DESC')->get();
            return $proofs;
        } catch (\Exception $e) {
            return null;
        }
    }

}
