<?php namespace Tests\Repositories;

use App\Models\AttendanceLog;
use App\Repositories\AttendanceLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class AttendanceLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var AttendanceLogRepository
     */
    protected $attendanceLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->attendanceLogRepo = \App::make(AttendanceLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_attendance_log()
    {
        $attendanceLog = AttendanceLog::factory()->make()->toArray();

        $createdAttendanceLog = $this->attendanceLogRepo->create($attendanceLog);

        $createdAttendanceLog = $createdAttendanceLog->toArray();
        $this->assertArrayHasKey('id', $createdAttendanceLog);
        $this->assertNotNull($createdAttendanceLog['id'], 'Created AttendanceLog must have id specified');
        $this->assertNotNull(AttendanceLog::find($createdAttendanceLog['id']), 'AttendanceLog with given id must be in DB');
        $this->assertModelData($attendanceLog, $createdAttendanceLog);
    }

    /**
     * @test read
     */
    public function test_read_attendance_log()
    {
        $attendanceLog = AttendanceLog::factory()->create();

        $dbAttendanceLog = $this->attendanceLogRepo->find($attendanceLog->id);

        $dbAttendanceLog = $dbAttendanceLog->toArray();
        $this->assertModelData($attendanceLog->toArray(), $dbAttendanceLog);
    }

    /**
     * @test update
     */
    public function test_update_attendance_log()
    {
        $attendanceLog = AttendanceLog::factory()->create();
        $fakeAttendanceLog = AttendanceLog::factory()->make()->toArray();

        $updatedAttendanceLog = $this->attendanceLogRepo->update($fakeAttendanceLog, $attendanceLog->id);

        $this->assertModelData($fakeAttendanceLog, $updatedAttendanceLog->toArray());
        $dbAttendanceLog = $this->attendanceLogRepo->find($attendanceLog->id);
        $this->assertModelData($fakeAttendanceLog, $dbAttendanceLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_attendance_log()
    {
        $attendanceLog = AttendanceLog::factory()->create();

        $resp = $this->attendanceLogRepo->delete($attendanceLog->id);

        $this->assertTrue($resp);
        $this->assertNull(AttendanceLog::find($attendanceLog->id), 'AttendanceLog should not exist in DB');
    }
}
