<?php

class TaggyStringUsageTest extends TestCase
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
        $this->lesson->tag(['laravel', 'php']);

        $this->assertCount(2, $this->lesson->tags);

        foreach (['Laravel', 'PHP'] as $tag) {
            $this->assertContains($tag, $this->lesson->tags->pluck('name'));
        }
    }

    /** @test */
    public function can_untag_lesson_tag()
    {
        $this->lesson->tag(['laravel', 'php', 'testing']);
        $this->lesson->untag(['laravel']);

        $this->assertCount(2, $this->lesson->tags);
        $this->assertEquals(['PHP', 'Testing'], $this->lesson->tags->pluck('name')->toArray());
    }

    /** @test */
    public function can_untag_all_lesson_tags()
    {
        $this->lesson->tag(['laravel', 'php', 'testing']);
        $this->lesson->untag();

        $this->lesson->load('tags');

        $this->assertCount(0, $this->lesson->tags);
        $this->assertEquals(0, $this->lesson->tags()->count());
    }

    /** @test */
    public function can_retag_lesson_tags()
    {
        $this->lesson->tag(['laravel', 'testing']);
        $this->lesson->retag(['laravel', 'postgres', 'redis']);

        $this->lesson->load('tags');

        $this->assertCount(3, $this->lesson->tags);

        foreach (['Laravel', 'Postgres', 'Redis'] as $tag) {
            $this->assertContains($tag, $this->lesson->tags->pluck('name'));
        }
    }

    /** @test */
    public function non_existing_tags_are_ignored_on_tagging()
    {
        $this->lesson->tag(['laravel', 'c++', 'redis']);

        $this->assertCount(2, $this->lesson->tags);

        foreach (['Laravel', 'Redis'] as $tag) {
            $this->assertContains($tag, $this->lesson->tags->pluck('name'));
        }
    }

    /** @test */
    public function inconsistent_tag_cases_are_normalised()
    {
        $this->lesson->tag(['LARAVEL', 'testINg', 'fun stuff']);

        $this->assertCount(3, $this->lesson->tags);

        foreach (['laravel', 'testing', 'fun-stuff'] as $tagSlug) {
            $this->assertContains($tagSlug, $this->lesson->tags->pluck('slug'));
        }
    }
}
