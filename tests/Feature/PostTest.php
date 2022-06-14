<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\BlogPost;
use App\Models\Comment;

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

    public function testSee1BlogPostWhenThereIs1WithNoComments()
    {
        //Arrange
    //    $post = new BlogPost();
    //    $post->title = 'New title';
    //    $post->content = 'Content of the blog post';
    //    $post->save();
        $post = $this->createDummyBlogPost();

       //Act
       $response = $this->get('/posts');

       // Assert
       $response->assertSeeText('New title');
       $response->assertSeeText('No comments yet!');

       $this->assertDatabaseHas('blog_posts', [
           'title' => 'New title'
        ]);
    }

    public function testSee1BlogPostWithComments()
    {
        // Arrange
        $post = $this->createDummyBlogPost();

        Comment::factory(4)->create([
            'blog_post_id' => $post->id
        ]);

        $response = $this->get('/posts');

        $response->assertSeeText('4 comments');
    }
    
    public function testStoreValid()
    {
        $params = [
            'title' => 'Valid title',
            'content' => 'At least 10 characters'
        ];
        
        $this->actingAs($this->user())
            ->post('/posts', $params)
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

        $this->actingAs($this->user())
            ->post('/posts', $params)
            ->assertStatus(302)
            ->assertSessionHas('errors');

        $messages = session('errors')->getMessages();
        $this->assertEquals($messages['title'][0], 'The title must be at least 5 characters.');
        $this->assertEquals($messages['content'][0], 'The content must be at least 10 characters.');
    }

    public function testUpdateValid()
    {
    //    $post = new BlogPost();
    //    $post->title = 'New title';
    //    $post->content = 'Content of the blog post';
    //    $post->save();
        $user = $this->user();
       $post = $this->createDummyBlogPost($user->id);

       $this->assertDatabaseHas('blog_posts', $post->getAttributes());

       $params = [
           'title' => 'A new named title',
           'content' => 'Content was changed'
       ];

       $this->actingAs($user)
            ->put("/posts/{$post->id}", $params)
            ->assertStatus(302)
            ->assertSessionHas('status');

       $this->assertEquals(session('status'), 'Blog post was updated!');
       $this->assertDatabaseMissing('blog_posts', $post->getAttributes());
       $this->assertDatabaseHas('blog_posts', [
           'title' => 'A new named title'
       ]);
    }

    public function testDelete()
    {
        $user = $this->user();
        $post = $this->createDummyBlogPost($user->id);

        
        $this->actingAs($user)
            ->delete("/posts/{$post->id}")
            ->assertStatus(302)
            ->assertSessionHas('status');

        $this->assertEquals(session('status'), 'Blog post was deleted!');
        $this->assertDatabaseMissing('blog_posts', $post->getAttributes());

    }

    public function createDummyBlogPost($userId = null): BlogPost
    {
        // $post = new BlogPost();
        // $post->title = 'New title';
        // $post->content = 'Content of the blog post';
        // $post->save();
        return BlogPost::factory()->states('new-title')->create(
            [
                'user_id' => $userId ?? $this->user()->id
            ]
        );
        // return $post;
    }

}