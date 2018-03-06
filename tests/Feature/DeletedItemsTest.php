<?php
namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeletedItemsTest extends TestCase
{
  use RefreshDatabase;

  public function setUp()
  {
    parent::setUp();
    $user = factory('App\User')->create();
    $this->be($user);
    $this->book = factory('App\Book')->create(['user_id' => $user->id]);
    $this->note = factory('App\Note')->create(['user_id' => $user->id, 'book_id' => $this->book->id]);
  }

  /** @test */
  public function soft_deleted_notes_can_be_viewed()
  {
    $this->get('/deleted-items/')
      ->assertDontSee($this->note->note);

    $this->delete('/notes/' . $this->note->id);

    $this->get('/deleted-items/')
      ->assertSee($this->note->note);
  }

  /** @test */
  public function the_soft_deleted_notes_of_another_user_cannot_be_viewed()
  {
    $this->delete('/notes/' . $this->note->id);

    $newUser = factory('App\User')->create();
    $this->be($newUser);

    $this->get('/deleted-items/')
      ->assertDontSee($this->note->note);
  }
  
  /** @test */
  public function only_soft_deleted_notes_are_viewable_in_the_delete_notes_section()
  {
    $this->get('/deleted-items/')
      ->assertDontSee($this->note->note);
  }

  /** @test */
  public function a_soft_deleted_note_can_be_restored()
  {
    $this->delete('/notes/' . $this->note->id);

    $this->get('/deleted-items/')
      ->assertSee($this->note->note);

    $this->assertDatabaseMissing('notes', [
      'id' => $this->note->id,
      'note' => $this->note->note,
      'deleted_at' => null
    ]);

    $this->get('/restoreNote/' . $this->note->id);

    $this->get('/deleted-items/')
      ->assertDontSee($this->note->note);

    $this->assertDatabaseHas('notes', [
      'id' => $this->note->id,
      'note' => $this->note->note,
      'deleted_at' => null
    ]);
  }

  /** @test */
  public function a_soft_deleted_note_cannot_be_restored_by_a_different_user()
  {
    $this->delete('/notes/' . $this->note->id);

    $newUser = factory('App\User')->create();
    $this->be($newUser);

    $this->get('/restoreNote/' . $this->note->id)
      ->assertStatus(403);

    $this->assertDatabaseMissing('notes', [
      'id' => $this->note->id,
      'note' => $this->note->note,
      'deleted_at' => null
    ]);
  }
 
  /** @test */
  public function a_soft_deleted_note_can_be_permanently_deleted()
  {
    $this->delete('/notes/' . $this->note->id);

    $this->get('/deleted-items/')
      ->assertSee($this->note->note);

    $this->assertDatabaseMissing('notes', [
      'id' => $this->note->id,
      'note' => $this->note->note,
      'deleted_at' => null
    ]);

    $this->get('/permadeleteNote/' . $this->note->id);

    $this->get('/deleted-items/')
      ->assertDontSee($this->note->note);

    $this->assertDatabaseMissing('notes', [
      'id' => $this->note->id,
      'note' => $this->note->note
    ]);
  }

  /** @test */
  public function a_soft_deleted_note_of_another_user_cannot_be_permanently_deleted()
  {
    $this->delete('/notes/' . $this->note->id);

    $newUser = factory('App\User')->create();
    $this->be($newUser);

    $this->get('/permadeleteNote/' . $this->note->id)
      ->assertStatus(403);

    $this->assertDatabaseHas('notes', [
      'id' => $this->note->id,
      'note' => $this->note->note
    ]);
  }
}
