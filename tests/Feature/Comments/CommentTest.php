<?php

namespace Tests\Feature\Comments;

use App\User;
use App\Issue;
use App\Proof;
use App\Comment;
use App\Project;
use Tests\TestCase;
use App\ProjectFile;
use App\Mail\NewComment;
use App\Events\CommentPosted;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentTest extends TestCase
{
    public $freelancer;
    public $client;
    public $project;
    public $projectFile;
    public $proof;
    public $issue;

    public function setUp()
    {
        parent::setUp();

        // Issue endpoin: api/proof/create_issue
        // Files upload endpoin: api/files/upload

        // ref="newIssueForm
        // refs['newIssueForm
        
        $this->freelancer = factory(User::class)->create();
        $this->client = factory(User::class)->create();

        $this->createProject(); // creates a new project + revision and build the relationship between freelancer, client and the project
        $this->uploadFile(); // creates a new projectFile + proof

        $this->actingAs($this->client);
        $this->createIssue(); // creates a new issue
    }

    /** @test */
    public function test_a_freelancer_can_comment_on_a_project()
    {
        $this->actingAs($this->freelancer);

        $this->addNewComment(['description' => 'a new comment']);

        $comment = Comment::where('user_id', $this->freelancer->id)->get();
        $this->assertCount(1, $comment);
        $this->assertEquals('a new comment', $comment->first()->description);
    }

    /** @test */
    public function test_CommentPosted_event_is_triggered_when_a_new_comment_is_posted()
    {
        Event::fake();
        $this->actingAs($this->freelancer);

        $response = $this->addNewComment(['description' => 'a new comment']);

        $comment = Comment::findOrFail($response['data'][0]['id'])->first();
        Event::assertDispatched(CommentPosted::class, function($event) use ($comment){
            return $event->user->getRoleOf($event->project) === 'client'
                && $event->project->id === $this->project->id
                && $event->comment->id === $comment->id;
        });
    }

    /** @test */
    public function test_a_mail_notification_is_sent_to_a_client_when_new_comment_is_added_to_a_project()
    {
        Mail::fake();
        $this->actingAs($this->freelancer);

        $response = $this->addNewComment(['description' => 'a new comment']);

        $comment = Comment::findOrFail($response['data'][0]['id'])->first();
        Mail::assertQueued(NewComment::class, function($mail) use ($comment){
            return $mail->hasTo($this->client->email)
                && $mail->name === $this->client->name
                && $mail->project->id === $this->project->id
                && $mail->comment->id === $comment->id;
        });
    }

    protected function addNewComment($params = [])
    {
        $response = $this->json('POST', 'api/proof/add_comment', array_merge([
            'issue_id' => $this->issue->id,
            'project_id' => $this->project->id,
            'revision_id' => $this->project->revisions->first()->id,
            'description' => 'Comment from freelancer',
        ], $params))->assertJson([
            'status' => 1,
            'message' => 'Comment created successfully'
        ]);

        return $response->decodeResponseJson();
    }

    protected function createProject()
    {
        $this->project = factory(Project::class)->create([
            'created_by' => $this->freelancer->id,
        ]);
        $this->project->revisions()->create([
            'status_revision' => 'draft',
            'version' => 1,
        ]);
        $this->project->users()->attach([
            ['user_id' => $this->freelancer->id, 'role' => 'freelancer'],
            ['user_id' => $this->client->id, 'role' => 'client']
        ]);
    }

    protected function uploadFile()
    {
        $this->projectFile = factory(ProjectFile::class)->create([
            'user_id' => $this->freelancer->id,
            'revision_id' => $this->project->getActiveRevision($this->project->id)->id
        ]);

        $this->proof = Proof::store($this->projectFile);
    }

    protected function createIssue()
    {
        $response = $this->json('POST', 'api/proof/create_issue', [
            'proof_id' => $this->proof->id,
            'description' => 'New issue',
            'group' => 'group_1',
            'label' => 1,
        ]);

        $this->issue = Issue::latest()->first();
    }
}
