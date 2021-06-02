<?php
declare (strict_types = 1);

use App\Application\Actions\Department\ListDepartmentsAction;
use App\Application\Actions\UserDepartment\AddUserDepartmentAction;
use App\Application\Actions\UserDepartment\ViewUserDepartmentAction;
use App\Application\Actions\UserType\ListUserTypesAction;
use App\Application\Actions\User\AddUserAction;
use App\Application\Actions\User\ChangeUserPasswordAction;
use App\Application\Actions\User\DisableUserAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\LoginUserAction;
use App\Application\Actions\User\LogoutUserAction;
use App\Application\Actions\User\UpdateUserAction;
use App\Application\Actions\User\UserInfoAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $objContainer = $app->getContainer();

    $app->get(
        '/', function (Request $request, Response $response) {

            $response->getBody()->write('Welcome to Phoenix Global API portal !!!');
            return $response;
        }
    )->setName('welcome');

    $app->group(
        '/user', function (Group $group) use ($objContainer) {
            $group->post('/login', LoginUserAction::class)->setName('user-login');
            $group->get('/user-info', UserInfoAction::class)->setName('user-info');
            $group->get('/{id}', ViewUserAction::class)->setName('user-view');
            $group->post('', AddUserAction::class)->setName('user-add');
            $group->put('/{id}', UpdateUserAction::class)->setName('user-update');
            $group->delete('/{id}', DisableUserAction::class)->setName('user-disable');
        }
    );

    $app->group(
        '/users', function (Group $group) use ($objContainer) {
            $group->post('', ListUsersAction::class)->setName('users-list');
        }
    );

    $app->group(
        '/change-password', function (Group $group) use ($objContainer) {
            $group->post('', ChangeUserPasswordAction::class)->setName('change-user-password');
        }
    );

    $app->group(
        '/user-types', function (Group $group) use ($objContainer) {
            $group->get('', ListUserTypesAction::class)->setName('user-types-list');
        }
    );

    $app->group(
        '/departments', function (Group $group) use ($objContainer) {
            $group->get('', ListDepartmentsAction::class)->setName('departments-list');
        }
    );

    $app->group(
        '/user-department', function (Group $group) use ($objContainer) {
            $group->get('/{id}', ViewUserDepartmentAction::class)->setName('department-view');
            $group->post('', AddUserDepartmentAction::class)->setName('departments-add');
        }
    );

    $app->group(
        '/logout', function (Group $group) use ($objContainer) {
            $group->post('', LogoutUserAction::class)->setName('logout');
        }
    );
};