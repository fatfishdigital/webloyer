<?php

declare(strict_types=1);

namespace Webloyer\Infra\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\{
    Builder,
    Model,
    Relations,
};
use InvalidArgumentException;
use Ngmy\EloquentSerializedLob\SerializedLobTrait;
use Webloyer\Domain\Model\Project as ProjectDomainModel;

class Project extends Model implements ProjectDomainModel\ProjectInterest
{
    use SerializedLobTrait;

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'stage',
        'repository',
        'server_id',
        'email_notification_recipient',
        'attributes',
        'days_to_keep_deployments',
        'max_number_of_deployments_to_keep',
        'keep_last_deployment',
        'github_webhook_secret',
        'github_webhook_user_id',
    ];

    /**
     * @param Builder $query
     * @param string  $id
     * @return Builder
     */
    public function scopeOfId(Builder $query, string $id): Builder
    {
        return $query->where('uuid', $id);
    }

    /**
     * @return Relations\HasOne
     */
    public function maxDeployment(): Relations\HasOne
    {
        return $this->hasOne(MaxDeployment::class);
    }

    /**
     * @return Relations\BelongsToMany
     */
    public function recipes(): Relations\BelongsToMany
    {
        return $this->belongsToMany(Recipe::class)
            ->withPivot('recipe_order')
            ->orderBy('recipe_order');
    }

    /**
     * @return Relations\BelongsTo
     */
    public function server(): Relations\BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    /**
     * @param string $id
     * @return void
     * @see ProjectDomainModel\ProjectInterest::informId()
     */
    public function informId(string $id): void
    {
        $this->uuid = $id;
    }

    /**
     * @param string $name
     * @return void
     * @see ProjectDomainModel\ProjectInterest::informName()
     */
    public function informName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string ...$recipeIds
     * @return void
     * @see ProjectDomainModel\ProjectInterest::informRecipeIds()
     */
    public function informRecipeIds(string ...$recipeIds): void
    {
        if (empty($recipeIds)) {
            throw new InvalidArgumentException('Recipe is required.');
        }
        foreach ($recipeIds as $i => $recipeId) {
            $syncRecipeIds[$recipeId] = ['recipe_order' => $i + 1];
        }
        $this->recipes()->sync($syncRecipeIds);
    }

    /**
     * @param string $serverId
     * @return void
     * @see ProjectDomainModel\ProjectInterest::informServerId()
     */
    public function informServerId(string $serverId): void
    {
        $serverOrm = Server::ofId($serverId)->first();
        if (is_null($serverOrm)) {
            throw new InvalidArgumentException(
                'Server does not exists.' . PHP_EOL .
                'Server Id: ' . $serverId
            );
        }
        $this->server_id = $serverOrm->id;
    }

    /**
     * @param string $repositoryUrl
     * @return void
     * @see ProjectDomainModel\ProjectInterest::informRepositoryUrl()
     */
    public function informRepositoryUrl(string $repositoryUrl): void
    {
        $this->repository = $repositoryUrl;
    }

    /**
     * @param string $stageName
     * @return void
     * @see ProjectDomainModel\ProjectInterest::informStageName()
     */
    public function informStageName(string $stageName): void
    {
        $this->stage = $stageName;
    }

    /**
     * @param string|null $deployPath
     * @return void
     * @see ProjectDomainModel\ProjectInterest::informDeployPath()
     */
    public function informDeployPath(?string $deployPath): void
    {
        if (!is_null($deployPath)) {
            $this->attributes['deploy_path'] = $deployPath;
        }
    }

    /**
     * @param string|null $emailNotificationRecipient
     * @return void
     * @see ProjectDomainModel\ProjectInterest::informEmailNotificationRecipient()
     */
    public function informEmailNotificationRecipient(?string $emailNotificationRecipient): void
    {
        $this->email_notification_recipient = $emailNotificationRecipient;
    }

    /**
     * @param int|null $deploymentKeepDays
     * @return void
     * @see ProjectDomainModel\ProjectInterest::informDeploymentKeepDays()
     */
    public function informDeploymentKeepDays(?int $deploymentKeepDays): void
    {
        $this->days_to_keep_deployments = $deploymentKeepDays;
    }

    /**
     * @param bool $keepLastDeployment
     * @return void
     * @see ProjectDomainModel\ProjectInterest::informKeepLastDeployment()
     */
    public function informKeepLastDeployment(bool $keepLastDeployment): void
    {
        $this->keep_last_deployment = (int) $keepLastDeployment;
    }

    /**
     * @param int|null $deploymentKeepMaxNumber
     * @return void
     * @see ProjectDomainModel\ProjectInterest::informDeploymentKeepMaxNumber()
     */
    public function informDeploymentKeepMaxNumber(?int $deploymentKeepMaxNumber): void
    {
        $this->max_number_of_deployments_to_keep = $deploymentKeepMaxNumber;
    }

    /**
     * @param string|null $githubWebhookSecret
     * @return void
     * @see ProjectDomainModel\ProjectInterest::informGithubWebhookSecret()
     */
    public function informGithubWebhookSecret(?string $githubWebhookSecret): void
    {
        $this->github_webhook_secret = $githubWebhookSecret;
    }

    /**
     * @param string|null $githubWebhookExecutor
     * @return void
     * @see ProjectDomainModel\ProjectInterest::informGithubWebhookExecutor()
     */
    public function informGithubWebhookExecutor(?string $githubWebhookExecutor): void
    {
        $this->github_webhook_user_id = $githubWebhookExecutor;
    }

    /**
     * @return ProjectDomainModel\Project
     */
    public function toEntity(): ProjectDomainModel\Project
    {
        assert(!empty($this->recipes->isEmpty()));
        assert(!is_null($this->server));
        return ProjectDomainModel\Project::of(
            $this->uuid,
            $this->name,
            $this->recipes->map(function (Recipe $recipe) {
                return $recipe->uuid;
            })->toArray(),
            $this->server->uuid,
            $this->repository,
            $this->stage,
            $this->attributes['deploy_path'] ?? null,
            $this->email_notification_recipient,
            $this->days_to_keep_deployments,
            (bool) $this->keep_last_deployment,
            $this->max_number_of_deployments_to_keep,
            $this->github_webhook_secret,
            $this->github_webhook_user_id
        );
    }

    protected function getSerializationColumn(): string
    {
        return 'attributes';
    }

    protected function getSerializationType(): string
    {
        return 'json';
    }

    protected function getDeserializationType(): string
    {
        return 'array';
    }
}
