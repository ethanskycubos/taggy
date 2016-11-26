<?php

class TaggyModelWithTagsTest extends TestCase
{
    protected $lesson;

    public function setUp()
    {
        parent::setUp();

        foreach (['PHP', 'Laravel', 'Testing'] as $tag) {
            \TagStub::create([
                'name' => $tag,
                'slug' => str_slug($tag),
                'count' => 0,
            ]);
        }

        \LessonStub::create([
            'title' => 'A lesson title'
        ])->tag(['php', 'laravel', 'testing']);

        \LessonStub::create([
            'title' => 'Another lesson title'
        ])->tag(['php', 'laravel']);

        \LessonStub::create([
            'title' => 'Yet another lesson title'
        ])->tag(['php']);
    }

    /** @test */
    public function can_get_model_with_any_given_tag()
    {
        $lessons = LessonStub::withAnyTag(['php'])->get();

        $this->assertCount(3, $lessons);
    }

    /** @test */
    public function can_get_model_with_only_given_tags()
    {
        $lessons = LessonStub::withAllTags(['php', 'laravel', 'testing'])->get();

        $this->assertCount(1, $lessons);
    }

    /** @test */
    public function can_get_model_with_any_given_tag_where_tag_not_attached()
    {
        $lessons = LessonStub::withAnyTag(['php', 'laravel', 'no such tag'])->get();

        $this->assertCount(3, $lessons);
    }

    /** @test */
    public function can_get_model_with_only_given_tags_ignores_tag_not_attached()
    {
        $lessons = LessonStub::withAllTags(['php', 'no such tag'])->get();

        $this->assertCount(0, $lessons);
    }
}
