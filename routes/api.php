<?php

/**
 * @author Maxime Pol Marcet
 */

// ActorController is imported so that the FR4 deletion endpoint can be mapped to a dedicated
// controller action while keeping routing consistent with the rest of the project.
// FilmController is imported so that the FR5 listing endpoint can be mapped to a dedicated
// controller action that returns films together with their associated actors as JSON.
use App\Http\Controllers\ActorController;
use App\Http\Controllers\FilmController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Actor deletion REST endpoint (FR4). This API route was introduced so that an actor can be
// removed from the database by its ID using standard REST semantics. A DELETE request to
// /api/actors/{id} is routed to ActorController@destroy, and a JSON payload is returned with:
// - "action": "delete"
// - "status": true|false (depending on the result)
// This design was chosen so that the feature can be tested easily via Postman or any API client
// without requiring any views.
Route::delete('actors/{id}', [ActorController::class, 'destroy']);

// Film listing REST endpoint (FR5). This API route was introduced so that all films can be
// retrieved together with their associated actors using Eloquent relationships. A GET request
// to /api/films is routed to FilmController@index, which returns a JSON array where each film
// includes its own fields and an "actors" array with the related actors. This allows the full
// dataset to be consumed directly by API clients such as Postman without involving Blade views.
Route::get('films', [FilmController::class, 'index']);
