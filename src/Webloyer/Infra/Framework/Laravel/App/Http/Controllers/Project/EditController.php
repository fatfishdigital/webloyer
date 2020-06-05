<?php

declare(strict_types=1);

namespace Webloyer\Infra\Framework\Laravel\App\Http\Controllers\Project;

use Common\App\Service\ApplicationService;
use Webloyer\App\Service\Project\GetProjectRequest;
use Webloyer\App\Service\Recipe\GetRecipesService;
use Webloyer\App\Service\Server\GetServersService;
use Webloyer\App\Service\User\GetUsersService;
use Webloyer\Infra\Framework\Laravel\Resources\ViewModels\Project\EditViewModel;

class EditController extends BaseController
{
    private $recipeService;
    private $serverService;
    private $userService;

    public function __construct(
        ApplicationService $service,
        GetRecipesService $recipeService,
        GetServersService $serverService,
        GetUsersService $userService
    ) {
        parent::__construct($service);

        $this->recipeService = $recipeService;
        $this->serverService = $serverService;
        $this->userService = $userService;
    }

    /**
     * Handle the incoming request.
     *
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function __invoke(string $id)
    {
        $serviceRequest = (new GetProjectRequest())->setId($id);
        $project = $this->service->execute($serviceRequest);

        $recipes = $this->recipeService->execute();
        $servers = $this->serverService->execute();
        $users = $this->userService->execute();

        return (new EditViewModel(
            $project,
            $recipes,
            $servers,
            $users
        ))->view('webloyer::projects.edit');
    }
}