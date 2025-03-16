<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\Committee;

class CommitteeApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_committee()
    {
        $committee = Committee::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/committees', $committee
        );

        $this->assertApiResponse($committee);
    }

    /**
     * @test
     */
    public function test_read_committee()
    {
        $committee = Committee::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/committees/'.$committee->id
        );

        $this->assertApiResponse($committee->toArray());
    }

    /**
     * @test
     */
    public function test_update_committee()
    {
        $committee = Committee::factory()->create();
        $editedCommittee = Committee::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/committees/'.$committee->id,
            $editedCommittee
        );

        $this->assertApiResponse($editedCommittee);
    }

    /**
     * @test
     */
    public function test_delete_committee()
    {
        $committee = Committee::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/committees/'.$committee->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/committees/'.$committee->id
        );

        $this->response->assertStatus(404);
    }
}
