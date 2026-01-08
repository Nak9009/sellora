<?php
// namespace App\Exceptions;

// use Exception;

// class Handler extends Exception {
// public function register()
// {
//     $this->reportable(function (Throwable $e) {
//         //
//     });

//     $this->renderable(function (ApiException $e, $request) {
//         return $e->render();
//     });

//     $this->renderable(function (ValidationException $e, $request) {
//         if ($request->expectsJson()) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Validation failed',
//                 'errors' => $e->errors(),
//             ], 422);
//         }
//     });

//     $this->renderable(function (ModelNotFoundException $e, $request) {
//         if ($request->expectsJson()) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Resource not found',
//             ], 404);
//         }
//     });

//     $this->renderable(function (AuthenticationException $e, $request) {
//         if ($request->expectsJson()) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Unauthenticated',
//             ], 401);
//         }
//     });

//     $this->renderable(function (AuthorizationException $e, $request) {
//         if ($request->expectsJson()) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Unauthorized',
//             ], 403);
//         }
//     });

//     $this->renderable(function (ThrottleRequestsException $e, $request) {
//         if ($request->expectsJson()) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Too many requests. Please try again later.',
//             ], 429);
//         }
//     });
// }
// }
