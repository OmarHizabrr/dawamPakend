<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEventAPIRequest;
use App\Http\Requests\API\UpdateEventAPIRequest;
use App\Models\Event;
use App\Repositories\EventRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\EventResource;
use Response;

/**
 * Class EventController
 * @package App\Http\Controllers\API
 */

class EventAPIController extends AppBaseController
{
    /** @var  EventRepository */
    private $eventRepository;

    public function __construct(EventRepository $eventRepo)
    {
        $this->eventRepository = $eventRepo;
    }

    /**
     * Display a listing of the Event.
     * GET|HEAD /events
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $events = $this->eventRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse(EventResource::collection($events), 'Events retrieved successfully');
    }

    /**
     * Store a newly created Event in storage.
     * POST /events
     *
     * @param CreateEventAPIRequest $request
     *
     * @return Response
     */

    public function store(CreateEventAPIRequest $request)
    {
        $input = $request->all();

        $event = $this->eventRepository->create($input);

        return $this->sendResponse(new EventResource($event), 'Event saved successfully');
    }

    /**
     * Display the specified Event.
     * GET|HEAD /events/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Event $event */
        $event = $this->eventRepository->find($id);

        if (empty($event)) {
            return $this->sendError('Event not found');
        }

        return $this->sendResponse(new EventResource($event), 'Event retrieved successfully');
    }

    /**
     * Update the specified Event in storage.
     * PUT/PATCH /events/{id}
     *
     * @param int $id
     * @param UpdateEventAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateEventAPIRequest $request)
    {
        $input = $request->all();

        /** @var Event $event */
        $event = $this->eventRepository->find($id);

        if (empty($event)) {
            return $this->sendError('Event not found');
        }

        $event = $this->eventRepository->update($input, $id);

        return $this->sendResponse(new EventResource($event), 'Event updated successfully');
    }

    /**
     * Remove the specified Event from storage.
     * DELETE /events/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Event $event */
        $event = $this->eventRepository->find($id);

        if (empty($event)) {
            return $this->sendError('Event not found');
        }

        $event->delete();

        return $this->sendSuccess('Event deleted successfully');
    }
}
