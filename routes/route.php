<?php
use App\Core\Route;

$router = new Route();

$router->get('/', function (array $request) {
    echo <<<HTML
        <h1>PHP ROUTER</h1>
        <p>
            A router is a mechanism that handles incoming HTTP requests and directs them to the appropriate endpoint or controller, based on the request's URL. Routers are a fundamental part of web applications and are used to map URLs to specific actions or resources within the application.
        </p>
    HTML;
});

$router->get('/get-route', function (array $request) {
    echo <<<HTML
        <h1>PHP ROUTER</h1>
        <h2>Simple get Route</h2>
        <p>
            This is simple route.
        </p>
    HTML;
});

$router->get('/get-route/dynamic/{value}/demo/{id}', function (array $request, string $value, int $demoId) {
    echo <<<HTML
        <h1>PHP ROUTER</h1>
        <h2>Get Route with dynamic variable</h2>
        <p>
            <ul>
                <li>
                    <strong>Fisrt dynamic variable value: </strong>
                    <span>$value</span>
                </li>
                <li>
                    <strong>Second dynamic variable value: </strong>
                    <span>$demoId</span>
                </li>
            </ul>
        </p>
    HTML;
});

$router->get('/post-form', function (array $request) {
    echo <<<HTML
        <h1>PHP ROUTER</h1>
        <div>
            <fieldset>
                <legend>
                    <h2>Form to test post route</h2>
                </legend>

                <form action="/post-route" method="post">
                    <label for="userNameInput">User name</label>
                    <input type="text" name="username" id="userNameInput"><br />
                    
                    <label for="userPasswordInput">Password</label>
                    <input type="password" name="password" id="userPasswordInput"><br />

                    <input type="submit" value="Submit form">
                </form>
            </fieldset>
        </div>
    HTML;
});

$router->post('/post-route', function (array $request) {
    var_dump("Post route request:", $request);
});

$router->dispatch();