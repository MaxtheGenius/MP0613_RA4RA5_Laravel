<?php

/**
 * @author Maxime Pol Marcet
 *
 * This test class was added so that the Actor module is verified against the
 * acceptance criteria: the actors route is registered and accessible, the list
 * view is returned with the required columns, the welcome page includes a
 * hyperlink to the actor listing, and actor data is retrieved through Eloquent
 * and rendered correctly. Each test is written in a way that can be run with
 * the rest of the application (RefreshDatabase, existing seeders).
 */

namespace Tests\Feature;

use App\Models\Actor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ActorModuleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The route named 'actors' is asserted to be registered so that the actorout
     * group is correctly configured; a GET request to that route is asserted to
     * return 200 so that the listing is accessible.
     */
    public function test_actors_route_is_registered_and_accessible(): void
    {
        $this->assertTrue(Route::has('actors'), 'The route named "actors" must be registered.');

        $response = $this->get(route('actors'));
        $response->assertStatus(200);
    }

    /**
     * The actors route response is asserted to use the view 'actors.list' and to
     * contain the title and actors data so that the dedicated list view and
     * required columns (Id, Photo, Name, Surname, Birthdate, Country) are
     * verified. ActorFakerSeeder is run so that the list is non-empty and the
     * view structure is exercised.
     */
    public function test_actors_route_returns_list_view_with_actor_data(): void
    {
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\ActorFakerSeeder']);

        $response = $this->get(route('actors'));

        $response->assertStatus(200);
        $response->assertViewIs('actors.list');
        $response->assertViewHas('title', 'All Actors');
        $response->assertViewHas('actors');
        $response->assertSee('All Actors', false);
        $response->assertSee('Id', false);
        $response->assertSee('Photo', false);
        $response->assertSee('Name', false);
        $response->assertSee('Surname', false);
        $response->assertSee('Birthdate', false);
        $response->assertSee('Country', false);
    }

    /**
     * The welcome page is requested and the response is asserted to contain the
     * text 'All Actors' and the URL of the actors route so that the hyperlink
     * to the actor listing is verified to be present and correct.
     */
    public function test_welcome_page_includes_hyperlink_to_actors_listing(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('All Actors', false);
        $url = route('actors');
        $response->assertSee($url, false);
    }

    /**
     * Actors are seeded via ActorFakerSeeder and the list view is requested; the
     * response is asserted to contain the name, surname and country of the first
     * actor so that data retrieved through Eloquent ORM is verified to be
     * rendered correctly in the view.
     */
    public function test_actor_list_displays_actors_from_database(): void
    {
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\ActorFakerSeeder']);

        $actor = Actor::first();
        $this->assertNotNull($actor, 'At least one actor must exist after seeding.');

        $response = $this->get(route('actors'));

        $response->assertStatus(200);
        $response->assertSee($actor->name, false);
        $response->assertSee($actor->surname, false);
        $response->assertSee($actor->country, false);
    }
}
