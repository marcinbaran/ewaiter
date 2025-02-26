<?php

namespace App\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use OwenIt\Auditing\Models\Audit as BaseAudit;

class Audit extends BaseAudit
{
    use UsesTenantConnection;

}
