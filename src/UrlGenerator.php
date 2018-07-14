<?php

namespace Laravelista\Loki;

use \Illuminate\Routing\UrlGenerator as LaravelUrlGenerator;

/**
 * This class overrides the default laravel methods
 * for url generation: route and url.
 *
 * It also adds a few helper methods.
 */
class UrlGenerator extends LaravelUrlGenerator
{
    /**
     * Generate an absolute URL to the given localized path.
     *
     * @param  string  $path
     * @param  mixed  $extra
     * @param  bool|null  $secure
     * @return string
     */
    public function to($path, $extra = [], $secure = null)
    {
        $locale = app()->getLocale();

        // important because of redirect()->route()
        if ($this->isValidUrl($path)) {
            return $path;
        }

        if (!$this->hideDefaultLocale($locale)) {
            $path = $locale . str_start($path, '/');
        }

        return parent::to($path, $extra, $secure);
    }

    /**
     * Get the URL to a named localized route.
     *
     * @param  string  $name
     * @param  mixed   $parameters
     * @param  bool  $absolute
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function route($name, $parameters = [], $absolute = true)
    {
        $locale = app()->getLocale();

        if (!$this->hideDefaultLocale($locale)) {
            $name = $locale . '.' . $name;
        }

        return parent::route($name, $parameters, $absolute);
    }

    public function getNonLocalizedRoute($name, $parameters = [], $absolute = true)
    {
        return parent::route($name, $parameters, $absolute);
    }

    public function getNonLocalizedUrl($path, $extra = [], $secure = null)
    {
        return parent::to($path, $extra, $secure);
    }

    public function getLocalizedRoute($locale, $name = null, $parameters = [], $absolute = true)
    {
        if (is_null($name)) {
            $route = request()->route();
            $name = $route->getName();
            $prefix = $route->getPrefix();
            $parameters = $route->parameters;

            if (!is_null($prefix)) {
                $name = str_replace_first($prefix . '.', '', $name);
            }

            if (!$this->hideDefaultLocale($locale)) {
                $name = $locale . '.' . $name;
            }
        } else {
            $name = $locale . '.' . $name;
        }

        return parent::route($name, $parameters, $absolute);
    }

    public function getLocalizedUrl($locale, $path = null, $extra = [], $secure = null)
    {
        if (is_null($path)) {
            $path = request()->path();
            $prefix = request()->route()->getPrefix();

            if (!is_null($prefix)) {
                $path = str_replace_first($prefix, '', $path);
            }

            if (!$this->hideDefaultLocale($locale)) {
                $path = $locale . str_start($path, '/');
            }
        } else {
            $path = $locale . str_start($path, '/');
        }

        return parent::to($path, $extra, $secure);
    }

    /**
     * It returns true if the current locale is the default locale and
     * the option to hide the default locale is set to true.
     */
    protected function hideDefaultLocale($locale)
    {
        if (config('loki.hideDefaultLocale') == true and
            $locale == config('loki.defaultLocale')) {
            return true;
        }

        return false;
    }
}