<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use App\Http\Requests\StoreNewsletterRequest;
use App\Http\Requests\UpdateNewsletterRequest;

class NewsletterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $newsletter = Newsletter::all();
            return response()->json([
                'message' => '
            recuperation des newsletter',
                'Data' => $newsletter
            ], 200);
        } catch (\Throwable $th) {
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(StoreNewsletterRequest $request)
    {
        try {
            $newsletter = new Newsletter();
            $newsletter->email = $request->email;
            if ($newsletter->save()) {
                return response()->json([
                    'message' => 'Vous etes enregistrer merci'
                ]);
            } else {
                return response()->json([
                    'message' => 'Mail non reçu'
                ]);
            }
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNewsletterRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Newsletter $newsletter)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Newsletter $newsletter)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNewsletterRequest $request, Newsletter $newsletter)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Newsletter $newsletter)
    {
        //
    }
}
