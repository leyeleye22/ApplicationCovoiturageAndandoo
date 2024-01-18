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
        $this->middleware('auth:api');
    }

    public function show()
    {
        try {
            $messages = new Message();
            return response()->json(
                ['data' => $messages],
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            return response()->json(
                ['error' => 'Failed to retrieve message. Unexpected error.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
