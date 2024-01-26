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
        $this->middleware('auth:api',['except' => ['send']]);
    }

    public function show()
    {
        try {
            $messages =Message::all();
            return response()->json(
                [   'messages' => 'Success',
                    'StatusCode'=>200,
                    'data' => $messages],
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            return response()->json(
                ['error' => 'Failed to retrieve message. Unexpected error.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
    public function send(StoreMessageRequest $request){
        $validatedData = $request->validated();
            $message = new Message();
            $message->fill($validatedData);
            if($message->save()){
                return response()->json([
                    'message' => 'success',
                    'SatusCode' => 200,
                    'Data' => $message
                ]);
            }
    }
}
