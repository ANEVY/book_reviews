<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Cache;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = $request->input("title");
        $filter = $request->input("filter");
        $books = Book::when($title,function($query,$title){
            return $query->title($title); 
        });
        $books = match ($filter) {  
            "highest_rated_last_month"=>$books->popularLastMonth(),
            "highest_rated_last_6months"=>$books->popularLast6Months(),
            default => $books->lastest(),
         };
        //$books = $books->get();
        // $books = Cache::remember(" books",0, fn() =>$books->get());
        $cacheKey = "book::".$filter."::".$title;
        $books = cache()->remember($cacheKey,0, fn() =>$books->get());

        
        return view("books.index",["books"=>$books]);
         
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $cacheKey = 'book:' . $book->id;

        $book = cache()->remember($cacheKey, 3600, fn() => $book->load([
            'reviews' => fn($query) => $query->latest()
        ]));

        return view('books.show', ['book' => $book]);
        // return view("books.show",["book"=> $book->load(["reviews"=> fn($query)=>$query->latest()])]);
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
