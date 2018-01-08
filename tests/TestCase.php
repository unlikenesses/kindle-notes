<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
  use CreatesApplication;

  protected function signIn($user = null)
  {
    $user = $user ?: factory('App\User')->create();

    $this->actingAs($user);

    return $this;
  }

  /** The following two methods from here:
   *  https://www.neontsunami.com/posts/asserting-that-an-eloquent-model-soft-deletes
   */
  protected function assertSoftDeletes(string $model)
  {
      $instance = new $model;
  
      $this->assertUsesTrait(\Illuminate\Database\Eloquent\SoftDeletes::class, $instance);
      $this->assertContains('deleted_at', $instance->getDates());
  }
   
  protected function assertUsesTrait($trait, $class)       
  {     
      $this->assertContains($trait, class_uses($class));        
  }
}
