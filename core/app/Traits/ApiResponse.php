<?php

namespace App\Traits;

trait ApiResponse
{
    protected function apiSuccess($message = null, $data = null, $statusCode = 200)
    {
        $response = ['status' => 'success'];
        if ($message) {
            $response['message'] = $message;
        }
        if ($data !== null) {
            $response['data'] = $data;
        }
        return response()->json($response, $statusCode);
    }

    protected function apiError($errors, $statusCode = 400, $remark = null)
    {
        if (is_string($errors)) {
            $errors = ['error' => [$errors]];
        } elseif (is_array($errors) && !isset($errors['error'])) {
            $errors = ['error' => array_values($errors)];
        }

        $response = ['status' => 'error', 'message' => $errors];
        if ($remark) {
            $response['remark'] = $remark;
        }
        return response()->json($response, $statusCode);
    }
}
