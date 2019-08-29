<?php

namespace App\Http\Controllers;

use App\Author;
use App\Http\Requests\StoreAuthor;
use App\Http\Resources\AuthorCollection;
use App\Http\Resources\AuthorResource;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \App\Http\Resources\AuthorCollection
     */
    public function index()
    {
        return AuthorCollection::make(
            Author::paginate(10)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreAuthor $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreAuthor $request)
    {
        $author = Author::create(
            $request->validated()
        );

        return AuthorResource::make($author)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Author $author
     *
     * @return \App\Http\Resources\AuthorResource
     */
    public function show(Author $author)
    {
        return AuthorResource::make($author);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\StoreAuthor $request
     * @param \App\Author $author
     *
     * @return \App\Http\Resources\AuthorResource
     */
    public function update(StoreAuthor $request, Author $author)
    {
        $author->fill(
            $request->validated()
        )
        ->save();

        return AuthorResource::make($author);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Author $author
     *
     * @throws \Exception
     * @return \Illuminate\Http\Response
     */
    public function destroy(Author $author)
    {
        $author->delete();

        return response('', Response::HTTP_NO_CONTENT);
    }
}
