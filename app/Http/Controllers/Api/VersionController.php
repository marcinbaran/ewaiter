<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\VersionRequest;
use Illuminate\Support\Facades\DB;

class VersionController extends Controller
{
    public function __invoke(VersionRequest $request)
    {
        $platform = $request->input('platform');
        $version = $request->input('version');

        $currentVersion = $this->getCurrentVersion($platform);

        list($updateAvailable, $requiredUpdate) = $this->checkVersions($version, $currentVersion);

        return response()->json([
            'current_version' => $currentVersion,
            'update_available' => $updateAvailable,
            'required_update' => $requiredUpdate,
        ]);
    }

    public function getCurrentVersion(string $platform): ?string
    {
        $currentVersion = DB::table('settings')
            ->where('key', $platform . '_version')
            ->value('value');

        $currentVersion = json_decode($currentVersion);

        return $currentVersion;
    }

    public function checkVersions(string $version, ?string $currentVersion): array
    {
        $updateAvailable = version_compare($version, $currentVersion, '<');
        $currentMajorMinor = implode('.', array_slice(explode('.', $currentVersion), 0, 2));
        $inputMajorMinor = implode('.', array_slice(explode('.', $version), 0, 2));
        $requiredUpdate = version_compare($inputMajorMinor, $currentMajorMinor, '<');

        return array($updateAvailable, $requiredUpdate);
    }
}
