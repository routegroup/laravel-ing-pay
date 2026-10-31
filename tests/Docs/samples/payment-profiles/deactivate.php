<?php

declare(strict_types=1);

/** @var string $profileId */

// #region sample
use Routegroup\IngPay\Api;

app(Api::class)->deactivateProfile($profileId);

$profile = app(Api::class)->getProfile($profileId)->paymentProfile;
// #endregion sample

return $profile;
