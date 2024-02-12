<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Mail\Avertissement;
use App\Mail\Response as reponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\AvertissementRequest;
use App\Http\Requests\UpdateMessageRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['send']]);
    }

    public function show()
    {
        try {
            $messages = Cache::remember('messages', 3600, function () {
                return Message::all();
            });
            $data = [];
            foreach ($messages as $message) {
                $data[] = [
                    'id' => $message['id'],
                    'nomComplet' => $message['NomComplet'],
                    'email' => $message['Email'],
                    'contenue' => $message['Contenue']
                ];
            }

            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(
                ['error' => 'Echec de recuperation des messages'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
    public function send(StoreMessageRequest $request)
    {
        $validatedData = $request->validated();
        $message = new Message();
        $message->fill($validatedData);
        if ($message->save()) {
        Artisan::call('optimize:clear');
            return response()->json([
                'message' => 'success',
                'SatusCode' => 200,
                'Data' => $message
            ]);
        }
    }
    public function response(UpdateMessageRequest $request)
    {
        try {
            $data = $request->contenue;
            if (Mail::to($request->email)->send(new reponse($data))) {
                return response()->json(['message' => 'reponse envoye avec success']);
            } else {
                return response()->json(['message' => 'reponse non envoyer']);
            }
        } catch (\Throwable $th) {
            return  $th->getMessage();
        }
    }
    public function avertissement(AvertissementRequest $request)
    {
        try {
            $data = $request->contenue;
            if (Mail::to($request->email)->send(new Avertissement($data))) {
                return response()->json(['message' => 'avertissement envoye avec success']);
            } else {
                return response()->json(['message' => 'avertissement non envoyer']);
            }
        } catch (\Throwable $th) {
            return  $th->getMessage();
        }
    }
}
