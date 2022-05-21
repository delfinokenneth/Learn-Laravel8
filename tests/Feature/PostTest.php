<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testNoBlogPostsWhenNothingInDatabase()
    {
        $response = $this->get('/posts');
        $response->assertSeeText('No posts found!');
    }

    public function testSee1BlogPostWhenThereIs1()
    {
        //Arrange
       $post = new BlogPost();
       $post->title = 'New title';
       $post->content = 'Content of the blog post';
       $post->save();

       //Act
       $response = $this->get('/posts');

       // Assert
       $response->assertSeeText('New title');
       $this->assertDatabaseHas('blog_posts', [
           'title' => 'New title'
        ]);
    }
    
    public function testStoreValid()
    {
        $params = [
            'title' => 'Valid title',
            'content' => 'At least 10 characters'
        ];
        
        $this->post('/posts', $params)
            ->assertStatus(302)
            ->assertSessionHas('status');

        $this->assertEquals(session('status'), 'The blog post was created!');
    }

    public function testStoreFail()
    {
        $params = [
            'title' => 'x',
            'content' => 'x'
        ];

        $this->post('/posts', $params)
        ->assertStatus(302)
        ->assertSessionHas('errors');

        $messages = session('errors')->getMessages();
        $this->assertEquals($messages['title'][0], 'The title must be at least 5 characters.');
        $this->assertEquals($messages['content'][0], 'The content must be at least 10 characters.');
    }

    public function testUpdateValid()
    {
       $post = new BlogPost();
       $post->title = 'New title';
       $post->content = 'Content of the blog post';
       $post->save();

       $this->assertDatabaseHas('blog_posts', $post->getAttributes());

       $params = [
           'title' => 'A new named title',
           'content' => 'Content was changed'
       ];

       $this->put("/posts/{$post->id}", $params)
        ->assertStatus(302)
        ->assertSessionHas('status');

       $this->assertEquals(session('status'), 'Blog post was updated!');
       $this->assertDatabaseMissing('blog_posts', $post->getAttributes());
       $this->assertDatabaseHas('blog_posts', [
           'title' => 'A new named title'
       ]);
    }

}
