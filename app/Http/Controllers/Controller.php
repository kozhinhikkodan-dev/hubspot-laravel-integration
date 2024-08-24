<?php

namespace App\Http\Controllers;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

abstract class Controller
{
    /**
     * Handle the given exception.
     *
     * @param  \Exception  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleException($e)
    {
        if ($e instanceof ModelNotFoundException) {
            return response()->json(['message' => 'Record not found'], 404);
        } elseif ($e instanceof ValidationException) {
            return response()->json(['errors' => $e->errors()], 422);
        } else {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
