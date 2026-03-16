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
}
