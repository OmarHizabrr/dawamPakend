<?php namespace Tests\Repositories;

use App\Models\Speaker;
use App\Repositories\SpeakerRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SpeakerRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SpeakerRepository
     */
    protected $speakerRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->speakerRepo = \App::make(SpeakerRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_speaker()
    {
        $speaker = Speaker::factory()->make()->toArray();

        $createdSpeaker = $this->speakerRepo->create($speaker);

        $createdSpeaker = $createdSpeaker->toArray();
        $this->assertArrayHasKey('id', $createdSpeaker);
        $this->assertNotNull($createdSpeaker['id'], 'Created Speaker must have id specified');
        $this->assertNotNull(Speaker::find($createdSpeaker['id']), 'Speaker with given id must be in DB');
        $this->assertModelData($speaker, $createdSpeaker);
    }

    /**
     * @test read
     */
    public function test_read_speaker()
    {
        $speaker = Speaker::factory()->create();

        $dbSpeaker = $this->speakerRepo->find($speaker->id);

        $dbSpeaker = $dbSpeaker->toArray();
        $this->assertModelData($speaker->toArray(), $dbSpeaker);
    }

    /**
     * @test update
     */
    public function test_update_speaker()
    {
        $speaker = Speaker::factory()->create();
        $fakeSpeaker = Speaker::factory()->make()->toArray();

        $updatedSpeaker = $this->speakerRepo->update($fakeSpeaker, $speaker->id);

        $this->assertModelData($fakeSpeaker, $updatedSpeaker->toArray());
        $dbSpeaker = $this->speakerRepo->find($speaker->id);
        $this->assertModelData($fakeSpeaker, $dbSpeaker->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_speaker()
    {
        $speaker = Speaker::factory()->create();

        $resp = $this->speakerRepo->delete($speaker->id);

        $this->assertTrue($resp);
        $this->assertNull(Speaker::find($speaker->id), 'Speaker should not exist in DB');
    }
}
