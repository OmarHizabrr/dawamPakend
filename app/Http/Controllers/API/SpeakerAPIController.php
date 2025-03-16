<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSpeakerAPIRequest;
use App\Http\Requests\API\UpdateSpeakerAPIRequest;
use App\Models\Speaker;
use App\Repositories\SpeakerRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\SpeakerResource;
use Response;

/**
 * Class SpeakerController
 * @package App\Http\Controllers\API
 */

class SpeakerAPIController extends AppBaseController
{
    /** @var  SpeakerRepository */
    private $speakerRepository;

    public function __construct(SpeakerRepository $speakerRepo)
    {
        $this->speakerRepository = $speakerRepo;
    }

    /**
     * Display a listing of the Speaker.
     * GET|HEAD /speakers
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $speakers = $this->speakerRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse(SpeakerResource::collection($speakers), 'Speakers retrieved successfully');
    }

    /**
     * Store a newly created Speaker in storage.
     * POST /speakers
     *
     * @param CreateSpeakerAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateSpeakerAPIRequest $request)
    {
        $input = $request->all();

        $speaker = $this->speakerRepository->create($input);

        return $this->sendResponse(new SpeakerResource($speaker), 'Speaker saved successfully');
    }

    /**
     * Display the specified Speaker.
     * GET|HEAD /speakers/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Speaker $speaker */
        $speaker = $this->speakerRepository->find($id);

        if (empty($speaker)) {
            return $this->sendError('Speaker not found');
        }

        return $this->sendResponse(new SpeakerResource($speaker), 'Speaker retrieved successfully');
    }

    /**
     * Update the specified Speaker in storage.
     * PUT/PATCH /speakers/{id}
     *
     * @param int $id
     * @param UpdateSpeakerAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSpeakerAPIRequest $request)
    {
        $input = $request->all();

        /** @var Speaker $speaker */
        $speaker = $this->speakerRepository->find($id);

        if (empty($speaker)) {
            return $this->sendError('Speaker not found');
        }

        $speaker = $this->speakerRepository->update($input, $id);

        return $this->sendResponse(new SpeakerResource($speaker), 'Speaker updated successfully');
    }

    /**
     * Remove the specified Speaker from storage.
     * DELETE /speakers/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Speaker $speaker */
        $speaker = $this->speakerRepository->find($id);

        if (empty($speaker)) {
            return $this->sendError('Speaker not found');
        }

        $speaker->delete();

        return $this->sendSuccess('Speaker deleted successfully');
    }
}
