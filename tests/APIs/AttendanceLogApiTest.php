<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\AttendanceLog;

class AttendanceLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_attendance_log()
    {
        $attendanceLog = AttendanceLog::factory()->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/attendancelogs', $attendanceLog
        );

        $this->assertApiResponse($attendanceLog);
    }

    /**
     * @test
     */
    public function test_read_attendance_log()
    {
        $attendanceLog = AttendanceLog::factory()->create();

        $this->response = $this->json(
            'GET',
            '/api/attendancelogs/'.$attendanceLog->id
        );

        $this->assertApiResponse($attendanceLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_attendance_log()
    {
        $attendanceLog = AttendanceLog::factory()->create();
        $editedAttendanceLog = AttendanceLog::factory()->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/attendancelogs/'.$attendanceLog->id,
            $editedAttendanceLog
        );

        $this->assertApiResponse($editedAttendanceLog);
    }

    /**
     * @test
     */
    public function test_delete_attendance_log()
    {
        $attendanceLog = AttendanceLog::factory()->create();

        $this->response = $this->json(
            'DELETE',
             '/api/attendancelogs/'.$attendanceLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/attendancelogs/'.$attendanceLog->id
        );

        $this->response->assertStatus(404);
    }
}
