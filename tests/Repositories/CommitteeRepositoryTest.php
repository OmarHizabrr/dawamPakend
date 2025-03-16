<?php namespace Tests\Repositories;

use App\Models\Committee;
use App\Repositories\CommitteeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CommitteeRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CommitteeRepository
     */
    protected $committeeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->committeeRepo = \App::make(CommitteeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_committee()
    {
        $committee = Committee::factory()->make()->toArray();

        $createdCommittee = $this->committeeRepo->create($committee);

        $createdCommittee = $createdCommittee->toArray();
        $this->assertArrayHasKey('id', $createdCommittee);
        $this->assertNotNull($createdCommittee['id'], 'Created Committee must have id specified');
        $this->assertNotNull(Committee::find($createdCommittee['id']), 'Committee with given id must be in DB');
        $this->assertModelData($committee, $createdCommittee);
    }

    /**
     * @test read
     */
    public function test_read_committee()
    {
        $committee = Committee::factory()->create();

        $dbCommittee = $this->committeeRepo->find($committee->id);

        $dbCommittee = $dbCommittee->toArray();
        $this->assertModelData($committee->toArray(), $dbCommittee);
    }

    /**
     * @test update
     */
    public function test_update_committee()
    {
        $committee = Committee::factory()->create();
        $fakeCommittee = Committee::factory()->make()->toArray();

        $updatedCommittee = $this->committeeRepo->update($fakeCommittee, $committee->id);

        $this->assertModelData($fakeCommittee, $updatedCommittee->toArray());
        $dbCommittee = $this->committeeRepo->find($committee->id);
        $this->assertModelData($fakeCommittee, $dbCommittee->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_committee()
    {
        $committee = Committee::factory()->create();

        $resp = $this->committeeRepo->delete($committee->id);

        $this->assertTrue($resp);
        $this->assertNull(Committee::find($committee->id), 'Committee should not exist in DB');
    }
}
