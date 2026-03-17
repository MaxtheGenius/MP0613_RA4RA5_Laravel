<?php

/**
 * Actor controller – HTTP actions for the actors resource.
 *
 * This class was introduced so that the Actor module has a dedicated controller,
 * matching the pattern used by FilmController and establishing a clear place for
 * future actor-related actions. The first visible behaviour is a full listing of
 * all actors stored in the database; data is retrieved only via Eloquent ORM
 * (Actor model) so that no raw SQL or file-based sources are used.
 *
 * @author Maxime Pol Marcet
 */

namespace App\Http\Controllers;

// The Actor model is used so that all actor data is retrieved through Eloquent ORM and the list view is populated from the database.
use App\Models\Actor;
// Carbon is imported so that decade date ranges can be built in a robust and readable way when filtering actors by decade (FR2).
use Carbon\Carbon;
// JsonResponse is imported so that the FR4 REST endpoint can declare a clear JSON return type.
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ActorController extends Controller
{
    /**
     * The given callback is executed inside a try-catch so that any database
     * exception is reported and the user is redirected to the home page with
     * a database_error message. This pattern is used so that connection failures
     * are handled in one place and the application degrades gracefully.
     */
    private function handleDatabaseQuery(callable $callback): mixed
    {
        try {
            return $callback();
        } catch (\Throwable $e) {
            report($e);
            return redirect('/')->with(
                'database_error',
                'Database is temporarily unavailable. Please start MySQL (e.g. from XAMPP) and try again.'
            );
        }
    }

    /**
     * All actors are retrieved from the database via the Actor model (Eloquent
     * ORM) and the dedicated list view is returned. Eloquent is used so that
     * the listing is always built from the database and remains consistent with
     * the rest of the application. The list view is returned so that users are
     * presented with a full table of actors including the required columns
     * (id, photo, name, surname, birthdate, country). Ordering by surname and
     * name is applied so that the list is deterministic and easy to scan.
     */
    public function listActors(): View|\Illuminate\Http\RedirectResponse
    {
        return $this->handleDatabaseQuery(function () {
            $title = 'All Actors';
            $actors = Actor::orderBy('surname')->orderBy('name')->get();
            return view('actors.list', [
                'actors' => $actors,
                'title' => $title,
            ]);
        });
    }

    /**
     * This action was introduced to implement FR2 (actor listing by decade) as
     * an extension of the existing listing behaviour defined in FR1. Actors are
     * filtered by the decade of their birthdate so that the same list view can
     * be reused while adding decade-based filtering. The ValidateYear middleware
     * is applied at the route level so that only allowed decade values reach
     * this method, and the query is expressed entirely with Eloquent ORM to
     * keep the implementation consistent with the rest of the module.
     */
    public function listActorsByDecade(int $year): View|\Illuminate\Http\RedirectResponse
    {
        return $this->handleDatabaseQuery(function () use ($year) {
            $start = Carbon::create($year, 1, 1)->startOfDay();
            $end = Carbon::create($year + 9, 12, 31)->endOfDay();

            $title = "Actors born in the {$year}s";

            $actors = Actor::whereBetween('birthdate', [$start, $end])
                ->orderBy('surname')
                ->orderBy('name')
                ->get();

            return view('actors.list', [
                'actors' => $actors,
                'title' => $title,
            ]);
        });
    }

    /**
     * This action was introduced to implement FR3 (actor count view) once the Actor
     * module is operational. The total number of actors is retrieved using Eloquent
     * ORM so that the database remains the single source of truth, and a dedicated
     * view is returned so that the count can be displayed clearly and independently
     * from the listing pages.
     */
    public function countActors(): View|\Illuminate\Http\RedirectResponse
    {
        return $this->handleDatabaseQuery(function () {
            $title = 'Actor Count';
            $count = Actor::count();

            return view('actors.count', [
                'title' => $title,
                'count' => $count,
            ]);
        });
    }

    /**
     * This action was added to implement FR4 (actor deletion via REST API). It is
     * exposed only through the API routes so that actors can be removed by ID from
     * API clients such as Postman. A JSON response is always returned indicating
     * the delete action and whether it was successful; invalid or non-existent
     * IDs are handled gracefully so that no unhandled exceptions are exposed.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            // The actor is located by ID using Eloquent so that deletion is performed through the ORM
            // and the behaviour stays consistent with the rest of the Actor module.
            $actor = Actor::find($id);

            if (! $actor) {
                // A 404 response is returned when the actor does not exist so that the API client is informed
                // gracefully while still receiving the required JSON structure.
                return response()->json([
                    'action' => 'delete',
                    'status' => false,
                ], 404);
            }

            // The deletion is attempted on the found model so that the database record is removed by Eloquent.
            // The boolean result is returned as "status" to match the FR4 acceptance criteria.
            $deleted = (bool) $actor->delete();

            return response()->json([
                'action' => 'delete',
                'status' => $deleted,
            ], $deleted ? 200 : 500);
        } catch (\Throwable $e) {
            // Any unexpected failure is caught so that unhandled exceptions are not exposed to clients.
            // A "status": false payload is returned so that the response format remains stable.
            report($e);

            return response()->json([
                'action' => 'delete',
                'status' => false,
            ], 500);
        }
    }
}
