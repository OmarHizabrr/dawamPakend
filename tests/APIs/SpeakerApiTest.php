<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\Speaker;

class SpeakerApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_speaker()
    {
        $speaker = Speaker::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/speakers', $speaker
        );

        $this->assertApiResponse($speaker);
    }

    /**
     * @test
     */
    public function test_read_speaker()
    {
        $speaker = Speaker::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/speakers/'.$speaker->id
        );

        $this->assertApiResponse($speaker->toArray());
    }

    /**
     * @test
     */
    public function test_update_speaker()
    {
        $speaker = Speaker::factory()->create();
        $editedSpeaker = Speaker::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/speakers/'.$speaker->id,
            $editedSpeaker
        );

        $this->assertApiResponse($editedSpeaker);
    }

    /**
     * @test
     */
    public function test_delete_speaker()
    {
        $speaker = Speaker::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/speakers/'.$speaker->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/speakers/'.$speaker->id
        );

        $this->response->assertStatus(404);
    }
}
