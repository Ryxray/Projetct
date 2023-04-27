<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6ce3bcb2065956e1ed78464dec20d68e
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'Aality\\RestrictAccess\\' => 22,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Aality\\RestrictAccess\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6ce3bcb2065956e1ed78464dec20d68e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6ce3bcb2065956e1ed78464dec20d68e::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit6ce3bcb2065956e1ed78464dec20d68e::$classMap;

        }, null, ClassLoader::class);
    }
}
