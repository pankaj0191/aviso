<?php

namespace Tests\Feature\Projects;

use App\User;
use App\Project;
use Tests\TestCase;
use App\Mail\NewProject;
use App\Mail\PasswordChange;
use App\Events\ProjectCreated;
use App\Events\CustomerInvited;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectTest extends TestCase
{
   /** @test */
   public function test_a_user_can_create_a_new_design_project()
   {
       Mail::fake();
       $this->userHasLogin();

       $this->createProject(['type' => 'design']);

       $this->assertCount(1, Project::all());
       $this->assertTrue(Project::all()->first()->type === 'design');
   }

   /** @test */
   public function test_a_user_can_create_a_new_website_project()
   {
       Mail::fake();
       $this->userHasLogin();

       $this->createProject(['type' => 'website']);

       $this->assertCount(1, Project::all());
       $this->assertTrue(Project::all()->first()->type === 'website');
   }

   /** @test */
   public function test_a_user_can_create_a_new_video_project()
   {
       Mail::fake();
       $this->userHasLogin();

       $this->createProject(['type' => 'video']);

       $this->assertCount(1, Project::all());
       $this->assertTrue(Project::all()->first()->type === 'video');
   }

   /** @test */
   public function test_a_customer_invite_event_is_triggered_after_project_creation_if_the_approver_has_not_an_account()
   {
       Event::fake();
       $this->userHasLogin();

       $response = $this->createProject();
       $project = Project::findOrFail($response['data']['project_id']);

       Event::assertDispatched(CustomerInvited::class, function ($event) use ($project) {
           return $event->customer->name === 'Client name' && $project->type === 'design';
       });
   }

   /** @test */
   public function test_project_created_event_is_triggered_after_project_creation_if_the_approver_has_an_account()
   {
       Event::fake();
       $this->userHasLogin();
       $response = $this->createProject();
       $client = User::findOrFail($response['data']['client']['id']);

       $this->createProject(['email' => $client->email, 'name' => $client->name]);

       Event::assertDispatched(ProjectCreated::class, function ($event) use ($client) {
           return $event->user->name === $client->name && $event->user->email === $client->email;
       });
   }

   /** @test */
   public function test_password_change_email_is_sent_to_the_approver_if_its_a_new_user()
   {
       Mail::fake();
       $this->userHasLogin();

       $this->createProject();

       Mail::assertSent(PasswordChange::class, function ($mail) {
           return $mail->hasTo('approver@email.com') && $mail->name === 'Client name';
       });
   }

   /** @test */
   public function test_a_new_project_notification_email_is_sent_to_the_approver_if_its_an_existing_user()
   {
       Mail::fake();
       $user = $this->userHasLogin();
       $response = $this->createProject();
       $client = User::findOrFail($response['data']['client']['id']);
       $project = Project::findOrFail($response['data']['project_id']);

       $secondProject = $this->createProject(['name' => $client->name, 'email' => $client->email]);

       Mail::assertQueued(NewProject::class, function ($mail) use ($client) {
           return $mail->hasTo($client->email);
       });
   }

   protected function userHasLogin()
   {
       $user = factory(User::class)->create();
       $this->actingAs($user);

       return $user;
   }

   protected function createProject($params = [])
   {
       $response = $this->json('POST', 'api/projects/create', array_merge([
           'name' => 'Project name',
           'client_name' => 'Client name',
           'email' => 'approver@email.com',
           'company' => 'Company name',
       ], $params))->assertJson([
           'message' => 'Project saved successfully',
           'data' => [
               'client' => [
                   'name' => 'Client name',
                   'email' => 'approver@email.com',
               ]
           ]
       ]);

       return $response->decodeResponseJson();
   }
}
