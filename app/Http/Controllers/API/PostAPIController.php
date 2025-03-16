<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePostAPIRequest;
use App\Http\Requests\API\UpdatePostAPIRequest;
use App\Models\Post;
use App\Repositories\PostRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\PostResource;
use Response;

/**
 * Class PostController
 * @package App\Http\Controllers\API
 */

class PostAPIController extends AppBaseController
{
    /** @var  PostRepository */
    private $postRepository;

    public function __construct(PostRepository $postRepo)
    {
        $this->postRepository = $postRepo;
    }

    /**
     * Display a listing of the Post.
     * GET|HEAD /posts
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $posts = $this->postRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse(PostResource::collection($posts), 'Posts retrieved successfully');
    }

    /**
     * Store a newly created Post in storage.
     * POST /posts
     *
     * @param CreatePostAPIRequest $request
     *
     * @return Response
     */
    public function store(CreatePostAPIRequest $request)
    {
        $input = $request->all();

        $post = $this->postRepository->create($input);

        return $this->sendResponse(new PostResource($post), 'Post saved successfully');
    }

    /**
     * Display the specified Post.
     * GET|HEAD /posts/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Post $post */
        $post = $this->postRepository->find($id);

        if (empty($post)) {
            return $this->sendError('Post not found');
        }

        return $this->sendResponse(new PostResource($post), 'Post retrieved successfully');
    }

    /**
     * Update the specified Post in storage.
     * PUT/PATCH /posts/{id}
     *
     * @param int $id
     * @param UpdatePostAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePostAPIRequest $request)
    {
        $input = $request->all();

        /** @var Post $post */
        $post = $this->postRepository->find($id);

        if (empty($post)) {
            return $this->sendError('Post not found');
        }

        $post = $this->postRepository->update($input, $id);

        return $this->sendResponse(new PostResource($post), 'Post updated successfully');
    }

    /**
     * Remove the specified Post from storage.
     * DELETE /posts/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Post $post */
        $post = $this->postRepository->find($id);

        if (empty($post)) {
            return $this->sendError('Post not found');
        }

        $post->delete();

        return $this->sendSuccess('Post deleted successfully');
    }
}
