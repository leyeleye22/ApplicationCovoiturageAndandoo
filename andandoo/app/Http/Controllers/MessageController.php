<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Mail\Response as reponse;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\StoreMessageRequest;
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
            $messages = Message::all();
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
            return response()->json([
                'message' => 'success',
                'SatusCode' => 200,
                'Data' => $message
            ]);
        }
    }
    public function response(Request $request)
    {
        try {
            $data = $request->contenue;
            $user = Message::where('id', $request->id)->first();
            Mail::to($user->Email)->send(new reponse($data));
            return response()->json(['message'=>'reponse envoye avec success']);
        } catch (\Throwable $th) {
            return  $th->getMessage();
        }
    }
}
