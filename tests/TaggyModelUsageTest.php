<?php

class TaggyModelUsageTest extends TestCase
{
    protected $lesson;

    public function setUp()
    {
        parent::setUp();

        foreach (['PHP', 'Laravel', 'Testing', 'Redis', 'Postgres', 'Fun stuff'] as $tag) {
            \TagStub::create([
                'name' => $tag,
                'slug' => str_slug($tag),
                'count' => 0,
            ]);
        }

        $this->lesson = \LessonStub::create([
            'title' => 'A lesson title'
        ]);
    }

    /** @test */
    public function can_tag_lesson()
    {
        $this->lesson->tag(\TagStub::where('slug', 'laravel')->first());

        $this->assertCount(1, $this->lesson->tags);
        $this->assertContains('Laravel', $this->lesson->tags->pluck('name'));
    }

    /** @test */
    public function can_tag_lesson_with_collection_of_tags()
    {
        $tags = \TagStub::whereIn('slug', ['laravel', 'php', 'redis'])->get();

        $this->lesson->tag($tags);

        $this->assertCount(3, $this->lesson->tags);

        foreach (['Laravel', 'PHP', 'Redis'] as $tag) {
            $this->assertContains($tag, $this->lesson->tags->pluck('name'));
        }
    }
    
    /** @test */
    public function can_untag_lesson_tag()
    {
        $tags = \TagStub::whereIn('slug', ['laravel', 'php', 'redis'])->get();

        $this->lesson->tag($tags);

        // Untag Laravel
        $this->lesson->untag($tags->first());

        $this->assertCount(2, $this->lesson->tags);
        $this->assertEquals(['PHP', 'Redis'], $this->lesson->tags->pluck('name')->toArray());
    }

    /** @test */
    public function can_untag_all_lesson_tags()
    {
        $tags = \TagStub::whereIn('slug', ['laravel', 'php', 'redis'])->get();

        $this->lesson->tag($tags);
        $this->lesson->untag();

        $this->lesson->load('tags');

        $this->assertCount(0, $this->lesson->tags);
        $this->assertEquals(0, $this->lesson->tags()->count());
    }

    /** @test */
    public function can_retag_lesson_tags()
    {
        $tags = \TagStub::whereIn('slug', ['laravel', 'testing'])->get();
        $toRetag = \TagStub::whereIn('slug', ['laravel', 'postgres', 'redis'])->get();

        $this->lesson->tag($tags);
        $this->lesson->retag($toRetag);

        $this->lesson->load('tags');

        $this->assertCount(3, $this->lesson->tags);

        foreach (['Laravel', 'Postgres', 'Redis'] as $tag) {
            $this->assertContains($tag, $this->lesson->tags->pluck('name'));
        }
    }

    /** @test */
    public function non_models_are_filtered_when_using_collection()
    {
        $tagsCollection = \TagStub::whereIn('slug', ['laravel', 'testing'])->get();
        $tagsCollection->push('not a tag model'); // ¯\_(ツ)_/¯

        $this->lesson->tag($tagsCollection);

        $this->assertCount(2, $this->lesson->tags);
    }
}
