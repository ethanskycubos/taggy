<?php

class TaggyOrderingTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        foreach ([
            'PHP' => 10,
            'Laravel' => 20,
            'Testing' => 5,
            'Redis' => 50,
            'Postgres' => 0
        ] as $tag => $count) {
            \TagStub::create([
                'name' => $tag,
                'slug' => str_slug($tag),
                'count' => $count,
            ]);
        }
    }

    /** @test */
    public function gte_scope_can_be_used()
    {
        $lesson = \LessonStub::create([
            'title' => 'A lesson title'
        ]);

        $lesson->tag(['php', 'laravel', 'testing', 'redis', 'postgres']);

        $tagsCountGreaterThanOrEqualTo45 = $lesson->tags()->usedGte(45)->get();

        // usedGt(21), because tagging above will increment count
        $tagsCountGreaterThanOrEqualTo21 = $lesson->tags()->usedGte(21)->get();

        $this->assertCount(1, $tagsCountGreaterThanOrEqualTo45);
        $this->assertCount(2, $tagsCountGreaterThanOrEqualTo21);

        foreach (['Laravel', 'Redis'] as $tag) {
            $this->assertContains($tag, $tagsCountGreaterThanOrEqualTo21->pluck('name'));
        }
    }

    /** @test */
    public function gt_scope_can_be_used()
    {
        $lesson = \LessonStub::create([
            'title' => 'A lesson title'
        ]);

        $lesson->tag(['php', 'laravel', 'testing', 'redis', 'postgres']);

        // usedGt(21), because tagging above will increment count
        $tagsCountGreaterThan20 = $lesson->tags()->usedGt(21)->get();

        $this->assertCount(1, $tagsCountGreaterThan20);
    }

    /** @test */
    public function lte_scope_can_be_used()
    {
        $lesson = \LessonStub::create([
            'title' => 'A lesson title'
        ]);

        $lesson->tag(['php', 'laravel', 'testing', 'redis', 'postgres']);

        // usedLte(11) because tagging above increments
        $tagsCountLessThanOrEqualTo11 = $lesson->tags()->usedLte(11)->get();
        $tagsCountLessThanOrEqualTo25 = $lesson->tags()->usedLte(25)->get();

        $this->assertCount(3, $tagsCountLessThanOrEqualTo11);
        $this->assertCount(4, $tagsCountLessThanOrEqualTo25);

        foreach (['Laravel', 'PHP', 'Testing', 'Postgres'] as $tag) {
            $this->assertContains($tag, $tagsCountLessThanOrEqualTo25->pluck('name'));
        }
    }

    /** @test */
    public function lt_scope_can_be_used()
    {
        $lesson = \LessonStub::create([
            'title' => 'A lesson title'
        ]);

        $lesson->tag(['php', 'laravel', 'testing', 'redis', 'postgres']);

        // usedLt(6), because tagging above will increment count
        $tagsCountLessThan6 = $lesson->tags()->usedLt(6)->get();

        $this->assertCount(1, $tagsCountLessThan6);
    }
}
