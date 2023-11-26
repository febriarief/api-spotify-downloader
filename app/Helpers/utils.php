<?php

/**
 * Return success response in json form
 *
 * @author Febri Arief<febriarief661@gmail.com>
 * @param  int  $code
 * @param  string  $message
 * @param  object|array|\Illuminate\Support\Collection  $data
 * @return \Illuminate\Http\JsonResponse
 */
if (!function_exists('json_success_response')) {
    function json_success_response($code = 200, $message = '', $data = []) {
        return response()->json([
            'status'  => $code,
			'message' => $message,
			'data'    => $data
		], $code);
    }
}

/**
 * Return error response in json form
 *
 * @author Febri Arief<febriarief661@gmail.com>
 * @param  int  $code
 * @param  string  $message
 * @param  object|array|\Illuminate\Support\Collection  $data
 * @return \Illuminate\Http\JsonResponse
 */
if (!function_exists('json_error_response')) {
    function json_error_response($code = 422, $message = '', $data = []) {
        return response()->json([
            'status'  => $code,
			'message' => $message,
			'data'    => $data
		], $code);
    }
}
