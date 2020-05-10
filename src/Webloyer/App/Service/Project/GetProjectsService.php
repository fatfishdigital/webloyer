<?php

declare(strict_types=1);

namespace Webloyer\App\Service\Project;

use Webloyer\Domain\Model\Project\Projects;

class GetProjectsService extends ProjectService
{
    /**
     * @param GetProjectsRequest $request
     * @return Projects
     */
    public function execute($request = null)
    {
        return $this->projectRepository->findAllByPage($request->getPage(), $request->getPerPage());
    }
}