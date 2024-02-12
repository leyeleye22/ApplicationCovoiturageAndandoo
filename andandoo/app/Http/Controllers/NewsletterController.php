<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
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
            $newsletters = Cache::remember('Newsletter', 3600, function () {
                return Newsletter::all();
            });
            $data = [];
            foreach ($newsletters as $newsletter) {
                $data[] = [
                    'id' => $newsletter['id'],
                    'email' => $newsletter['email'],
                ];
            }
            return response()->json($data);
        } catch (\Throwable $th) {
            return $th->getMessage();
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
                Artisan::call('optimize:clear');
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
}
