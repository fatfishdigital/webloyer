<?php

declare(strict_types=1);

namespace Webloyer\Domain\Model\Deployment;

use Common\Enumerable;

/**
 * @method static self queued()
 * @method static self running()
 * @method static self succeeded()
 * @method static self failed()
 */
class DeploymentStatus
{
    use Enumerable;

    /** @var string */
    private const QUEUED = 'queued';
    /** @var string */
    private const RUNNING = 'running';
    /** @var string */
    private const SUCCEEDED = 'succeeded';
    /** @var string */
    private const FAILED = 'failed';
}