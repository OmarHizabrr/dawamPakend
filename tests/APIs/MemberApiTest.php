<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\Member;

class MemberApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_member()
    {
        $member = Member::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/members', $member
        );

        $this->assertApiResponse($member);
    }

    /**
     * @test
     */
    public function test_read_member()
    {
        $member = Member::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/members/'.$member->id
        );

        $this->assertApiResponse($member->toArray());
    }

    /**
     * @test
     */
    public function test_update_member()
    {
        $member = Member::factory()->create();
        $editedMember = Member::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/members/'.$member->id,
            $editedMember
        );

        $this->assertApiResponse($editedMember);
    }

    /**
     * @test
     */
    public function test_delete_member()
    {
        $member = Member::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/members/'.$member->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/members/'.$member->id
        );

        $this->response->assertStatus(404);
    }
}
