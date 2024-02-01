<?php

namespace App\Http\Controllers;

use App\Models\Message;
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
}
