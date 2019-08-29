<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Requests\StoreBook;
use App\Http\Resources\BookCollection;
use App\Http\Resources\BookResource;
use Symfony\Component\HttpFoundation\Response;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \App\Http\Resources\BookCollection
     */
    public function index()
    {
        return BookCollection::make(
            Book::paginate(10)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreBook $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreBook $request)
    {
        $book = Book::create(
            $request->validated()
        );

        return BookResource::make($book)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Book $book
     *
     * @return \App\Http\Resources\BookResource
     */
    public function show(Book $book)
    {
        $book->load('authors');

        return BookResource::make($book);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\StoreBook $request
     * @param \App\Book $book
     *
     * @return \App\Http\Resources\BookResource
     */
    public function update(StoreBook $request, Book $book)
    {
        $book->fill(
            $request->validated()
        )
            ->save();

        return BookResource::make($book);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Book $book
     *
     * @throws \Exception
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
        $book->delete();

        return response('', Response::HTTP_NO_CONTENT);
    }
}
