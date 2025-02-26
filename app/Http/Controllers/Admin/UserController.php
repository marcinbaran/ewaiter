<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Select2Trait;
use App\Http\Requests\Admin\StatisticRequest;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Resources\Admin\StatisticResource;
use App\Http\Resources\Admin\UserResource;
use App\Managers\UserManager;
use App\Models\Settings;
use App\Models\User;
use App\Models\UserSystem;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\View\View;

class UserController extends Controller
{
    use Select2Trait;

    /**
     * @var TranslationService
     */
    private $transService;

    private $name = 'user';

    /**
     * @var UserManager
     */
    private $manager;

    public function __construct()
    {
        $this->manager = app(UserManager::class);
        UserResource::wrap('results');
    }

    /**
     * @param Request $request
     *
     * @return View|Factory|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            $type = $request->get('connection', 'tenant');

            return ($type == 'system') ? UserResource::collection(UserSystem::getPaginatedForPanel($request->get('query_category'), UserResource::LIMIT, ['name' => 'asc'])) :
                UserResource::collection(User::getPaginatedForPanel($request->get('query_category'), UserResource::LIMIT, ['name' => 'asc']));
        }

        return view('admin.users.index')->with([
            'controller' => $this->name,
        ]);
    }

    /**
     * @param Request $request
     * @param User $user
     *
     * @return UserResource|View|Factory
     */
    public function show(Request $request, User $user)
    {
        //select2
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return new UserResource($user);
        }

            return view('admin.users.show')->with([
            'controller' => 'user',
            'action' => 'show',
            'data' => new UserResource($user),
        ]);
    }

    /**
     * @param Request $request
     * @param UserSystem $user
     *
     * @return UserResource|View|Factory
     */
    public function show_system(Request $request, UserSystem $user)
    {
        //select2
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return $user;
        }

        return view('admin.users.show')->with([
            'controller' => 'user',
            'action' => 'show',
            'data' => new UserResource($user),
        ]);
    }

    /**
     * store function.
     *
     * @param UserRequest $request
     *
     * @return RedirectResponse
     */
    public function store(UserRequest $request)
    {
        $this->manager->create($request);

        $request->session()->flash('alert-success', __('admin.User was created'));

        return $this->redirectToIndex($request, 'admin.users.index');
    }

    /**
     * @return View|Factory
     */
    public function create(Request $request)
    {
        $user = new User;

        return view('admin.users.form')->with($this->hydrateData([
            'controller' => 'user',
            'action' => 'create',
            'data' => new UserResource($user),
            'oldRoles' => $this->getOldArrayForSelect2('roles', 'id'),
            'defaultRedirectUrl' => route('admin.users.index'),
        ], $request));
    }

    /**
     * @param User $user
     *
     * @return View|Factory
     */
    public function edit(Request $request, User $user)
    {
        return view('admin.users.form')->with($this->hydrateData([
            'controller' => 'user',
            'action' => 'edit',
            'data' => new UserResource($user),
            'oldRoles' => $this->getOldArrayForSelect2('roles', 'id'),
            'defaultRedirectUrl' => route('admin.users.index'),
        ], $request));
    }

    /**
     * @param UserRequest $request
     * @param User $user
     *
     * @return RedirectResponse
     */
    public function update(UserRequest $request, User $user)
    {
        $this->manager->update($request, $user, true);

        $request->session()->flash('alert-success', __('admin.User was updated'));

        return $this->redirectToIndex($request, 'admin.users.index');
    }

    /**
     * @param Request $request
     * @param User $user
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, User $user)
    {
        $this->manager->delete($user);

        $request->session()->flash('alert-success', __('admin.User was deleted'));

        return redirect()->route('admin.users.index');
    }

    /**
     * @param Request $request
     * @param User $user
     *
     * @return UserResource|View|Factory
     */
    public function profile(Request $request)
    {
        $user = User::where('id', auth()->user()->id)->first();

        return view('admin.users.profile')->with([
            'controller' => 'user',
            'action' => 'show',
            'data' => new UserResource($user),
        ]);
    }

    /**
     * @param UserRequest $request
     *
     * @return RedirectResponse
     */
    public function profile_update(UserRequest $request)
    {
        $user = User::where('id', auth()->user()->id)->first();

        $this->manager->update_profile($request, $user, true);

        $request->session()->flash('alert-success', __('admin.User profile was updated'));

        return redirect()->route('admin.users.profile');
    }

    /**
     * store function.
     *
     * @param UserRequest $request
     *
     * @return RedirectResponse
     */
    public function registerSave(UserRequest $request)
    {
        $user = $this->manager->register($request);

        $type = $request->has('type') ? $request->get('type') : null;
        $number = $request->has('number') ? $request->get('number') : null;

        //$request->session()->flash('alert-success', __('admin.Registration complete. Please check your e-mail.'));
        $request->session()->flash('alert-success', __('admin.Registration complete. Please check your phone.'));

        $logo = Settings::getSetting('logo', 'logo', true, false);
        if (! $logo) {
            $logo = '/images/logo_wk.png';
        }

        $salt = substr(md5($user->id.'authWK'), 0, 16);

        return view('admin.users.register_auth')->with([
            'logo' => $logo,
            'type' => $type,
            'number' => $number,
            'user' => $user,
            'salt' => $salt,
        ]);
    }

    public function register($referred_user_id, Request $request)
    {
        $logo = Settings::getSetting('logo', 'logo', true, false);
        if (! $logo) {
            $logo = '/images/logo_wk.png';
        }
        $res_id = $request->has('res_id') ? $request->get('res_id') : null;
        $type = $request->has('type') ? $request->get('type') : null;
        $number = $request->has('number') ? $request->get('number') : null;

        return view('admin.users.register')->with([
            'logo' => $logo,
            'referred_user_id' => $referred_user_id,
            'res_id' => $res_id,
            'type' => $type,
            'number' => $number,
        ]);
    }

    /**
     * @param Request $request
     * @param UserSystem $user
     *
     * @return View|Factory
     */
    public function registerAuth(UserSystem $user, Request $request)
    {
        $type = $request->has('type') ? $request->get('type') : null;
        $number = $request->has('number') ? $request->get('number') : null;

        $logo = Settings::getSetting('logo', 'logo', true, false);
        if (! $logo) {
            $logo = '/images/logo_wk.png';
        }

        $salt = substr(md5($user->id.'authWK'), 0, 16);

        return view('admin.users.register_auth')->with([
            'logo' => $logo,
            'type' => $type,
            'number' => $number,
            'user' => $user,
            'salt' => $salt,
        ]);
    }

    /**
     * store function.
     *
     * @param UserRequest $request
     *
     * @return RedirectResponse
     */
    public function registerAuthSave(UserRequest $request)
    {
        $user = UserSystem::where('auth_code', $request->get('auth_code'))->where('id', $request->get('id'))->first();

        $type = $request->has('type') ? $request->get('type') : null;
        $number = $request->has('number') ? $request->get('number') : null;

        if (! $user) {
            $request->session()->flash('alert-warning', __('admin.Wrong auth code or user does not exists'));

            return redirect()->route(
                'admin.register_auth',
                ['id' => $request->get('id'), 'type' => $type, 'number' => $number]
            )->withInput();
        }

        $user = $this->manager->register_auth($request, $user);

        $url = ($type && $number) ? env('POBIERZ_URL')."/?type=$type&number=$number" : null;

        $request->session()->flash('alert-success', __('admin.Registration complete.'));

        $logo = Settings::getSetting('logo', 'logo', true, false);
        if (! $logo) {
            $logo = '/images/logo_wk.png';
        }

        return view('admin.users.register_success')->with([
            'logo' => $logo,
            'url' => $url,
            'user' => $user,
        ]);
    }

    public function registerAuthAgain(Request $request)
    {
        $salt_s = $request->get('s');
        $id = $request->get('id');

        $salt = substr(md5($id.'authWK'), 0, 16);

        $user = UserSystem::where('id', $request->get('id'))->where('activated', 0)->first();
        if ($salt_s != $salt || ! $user) {
            return ['status' => 400, 'errors' => __('admin.Wrong params')];
        }
        $this->manager->register_auth_again($user);

        return ['status' => 200, 'data' => __('admin.Code was sent again')];
    }

    /**
     * @return type
     */
    public function points(UserSystem $user, StatisticRequest $request)
    {
        return StatisticResource::collection(User::getPoints($user, $request));
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function roles(?int $id = null)
    {
        $userResource = new UserResource($id ? User::findOrFail($id) : new User);
        $roles = collect(json_decode(json_encode([
            [
                'id' => 'ROLE_MANAGER',
                'name' => trans('admin.MANAGER'),
            ],
            [
                'id' => 'ROLE_ADMIN',
                'name' => trans('admin.ADMIN'),
            ],
            [
                'id' => 'ROLE_USER',
                'name' => trans('admin.USER'),
            ],
            [
                'id' => 'ROLE_WAITER',
                'name' => trans('admin.WAITER'),
            ],
        ])));
        $selectedRoles = [];
        foreach ($roles as $role) {
            if ($userResource->hasRole($role->id)) {
                $selectedRoles[] = $role;
            }
        }
        $selectedRoles = collect($selectedRoles);
        $response = $this->getJsonStringForSelect2($roles, $selectedRoles, 'id');

        return $response;
    }

    private function prepareCriteria(Request $request): array
    {
        $createdAt = $request->query->get('createdAt');
        $criteria = [];

        if (! empty($createdAt['start'])) {
            $criteria[] = ['created_at', '>=', $createdAt['start']];
        }
        if (! empty($createdAt['end'])) {
            $criteria[] = ['created_at', '<=', $createdAt['end']];
        }

        return $criteria;
    }
}
