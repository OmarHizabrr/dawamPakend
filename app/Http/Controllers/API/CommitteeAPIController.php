<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCommitteeAPIRequest;
use App\Http\Requests\API\UpdateCommitteeAPIRequest;
use App\Models\Committee;
use App\Repositories\CommitteeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\CommitteeResource;
use Response;

/**
 * Class CommitteeController
 * @package App\Http\Controllers\API
 */

class CommitteeAPIController extends AppBaseController
{
    /** @var  CommitteeRepository */
    private $committeeRepository;

    public function __construct(CommitteeRepository $committeeRepo)
    {
        $this->committeeRepository = $committeeRepo;
    }

    /**
     * Display a listing of the Committee.
     * GET|HEAD /committees
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $committees = $this->committeeRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse(CommitteeResource::collection($committees), 'Committees retrieved successfully');
    }

    /**
     * Store a newly created Committee in storage.
     * POST /committees
     *
     * @param CreateCommitteeAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCommitteeAPIRequest $request)
    {
        $input = $request->all();

        $committee = $this->committeeRepository->create($input);

        return $this->sendResponse(new CommitteeResource($committee), 'Committee saved successfully');
    }

    /**
     * Display the specified Committee.
     * GET|HEAD /committees/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Committee $committee */
        $committee = $this->committeeRepository->find($id);

        if (empty($committee)) {
            return $this->sendError('Committee not found');
        }

        return $this->sendResponse(new CommitteeResource($committee), 'Committee retrieved successfully');
    }

    /**
     * Update the specified Committee in storage.
     * PUT/PATCH /committees/{id}
     *
     * @param int $id
     * @param UpdateCommitteeAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCommitteeAPIRequest $request)
    {
        $input = $request->all();

        /** @var Committee $committee */
        $committee = $this->committeeRepository->find($id);

        if (empty($committee)) {
            return $this->sendError('Committee not found');
        }

        $committee = $this->committeeRepository->update($input, $id);

        return $this->sendResponse(new CommitteeResource($committee), 'Committee updated successfully');
    }

    /**
     * Remove the specified Committee from storage.
     * DELETE /committees/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Committee $committee */
        $committee = $this->committeeRepository->find($id);

        if (empty($committee)) {
            return $this->sendError('Committee not found');
        }

        $committee->delete();

        return $this->sendSuccess('Committee deleted successfully');
    }
}
