<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7258840dbab001024b3d728abc72effd
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Feycot\\PageAnalyzer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Feycot\\PageAnalyzer\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInit7258840dbab001024b3d728abc72effd::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7258840dbab001024b3d728abc72effd::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit7258840dbab001024b3d728abc72effd::$classMap;

        }, null, ClassLoader::class);
    }
}