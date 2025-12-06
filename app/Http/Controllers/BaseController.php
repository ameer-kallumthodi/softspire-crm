<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class BaseController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Return JSON success response
     */
    protected function jsonSuccess($msg, $data = [])
    {
        return response()->json(['success' => true, 'message' => $msg, 'data' => $data]);
    }

    /**
     * Return JSON error response
     */
    protected function jsonError($msg, $code = 400)
    {
        return response()->json(['success' => false, 'message' => $msg], $code);
    }

    /**
     * Return view with data
     */
    protected function view($file, $data = [])
    {
        return view($file, $data);
    }
}

