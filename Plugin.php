<?php

namespace Sv\JWTAuth;

use App, Config;
use Illuminate\Foundation\AliasLoader;
use System\Classes\PluginBase;
use System\Classes\SettingsManager;
use Sv\JWTAuth\Models\Settings as PluginSettings;

/**
 * JWTAuth Plugin Information File.
 */
class Plugin extends PluginBase
{
    /**
     * Plugin dependencies.
     *
     * @var array
     */
    public $require = ['RainLab.User'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'sv.jwtauth::lang.plugin.name',
            'description' => 'sv.jwtauth::lang.plugin.description',
            'author'      => 'Ricardo Lüders',
            'icon'        => 'icon-user-secret',
        ];
    }

    /**
     * Register the plugin settings
     *
     * @return array
     */
    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'sv.jwtauth::lang.settings.menu_label',
                'description' => 'sv.jwtauth::lang.settings.menu_description',
                'category'    => SettingsManager::CATEGORY_USERS,
                'icon'        => 'icon-user-secret',
                'class'       => 'Sv\JWTAuth\Models\Settings',
                'order'       => 600,
                'permissions' => ['sv.jwtauth.access_settings'],
            ]
        ];
    }

    /**
     * Register the plugin permissions
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'sv.jwtauth.access_settings' => [
                'tab' => 'sv.jwtauth::lang.plugin.name',
                'label' => 'sv.jwtauth::lang.permissions.settings'
            ]
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {
        App::register('\Sv\JWTAuth\Providers\AuthServiceProvider');
        $alias = AliasLoader::getInstance();
        $alias->alias('JWTAuth', '\Sv\JWTAuth\Facades\JWTAuth');

        // Handle error
        $this->app->error(function (\Exception $e) {
            if (!request()->isJson()) {
                return;
            }

            return [
                'error' => [
                    'code' => 'internal_error',
                    'http_code' => 500,
                    'message' => $e->getMessage(),
                ],
            ];
        });
    }
}
