<?php

namespace Postgen\Publisher\Jobs;

use Postgen\Common\Jobs\BaseJob;
use Postgen\Common\Logic\Articles;

class PublishPostJob extends BaseJob
{
    private int $postId;
    private int $siteId;
    private int $userId;

    public function __construct(int $postId, int $siteId, int $userId)
    {
        $this->postId = $postId;
        $this->siteId = $siteId;
        $this->userId = $userId;
    }

    public function handle()
    {
        Articles::create($this->postId, $this->siteId, $this->userId);
        // @todo post to site API
        // @todo update link and external_id
    }
}
