<?php
declare(strict_types=1);
/**
 * This configuration will be read and overlaid on top of the
 * default configuration. Command line arguments will be applied
 * after this file is read.
 */

use Phan\Issue;

return [
    'target_php_version' => '7.3',
    'minimum_severity' => Issue::SEVERITY_LOW,

    // A list of directories that should be parsed for class and
    // method information. After excluding the directories
    // defined in exclude_analysis_directory_list, the remaining
    // files will be statically analyzed for errors.
    //
    // Thus, both first-party and third-party code being used by
    // your application should be included in this list.
    'directory_list' => [
        'src', 'vendor'
    ],

    // A directory list that defines files that will be excluded
    // from static analysis, but whose class and method
    // information should be included.
    //
    // Generally, you'll want to include the directories for
    // third-party code (such as "vendor/") in this list.
    //
    // n.b.: If you'd like to parse but not analyze 3rd
    //       party code, directories containing that code
    //       should be added to both the `directory_list`
    //       and `exclude_analysis_directory_list` arrays.
    'exclude_analysis_directory_list' => [
        'vendor/',
        'docs/',
        'src/DependencyInjection/Configuration.php',
    ],

    // A file list that defines files that will be excluded
    // from parsing and analysis and will not be read at all.
    //
    // This is useful for excluding hopelessly unanalyzable
    // files that can't be removed for whatever reason.
    'exclude_file_list' => [
        // Explicitly exclude until is fixed https://github.com/phan/phan/issues/972
        'vendor/pagerfanta/pagerfanta/src/Pagerfanta/PagerfantaInterface.php',
    ],

    'quick_mode' => false,
    'analyze_signature_compatibility' => true,
    'allow_missing_properties' => false,
    'null_casts_as_any_type' => false,
    'null_casts_as_array' => false,
    'array_casts_as_null' => false,
    'scalar_implicit_cast' => false,
    'ignore_undeclared_variables_in_global_scope' => false,

    // A regular expression to match files to be excluded
    // from parsing and analysis and will not be read at all.
    //
    // This is useful for excluding groups of test or example
    // directories/files, unanalyzable files, or files that
    // can't be removed for whatever reason.
    // (e.g. '@Test\.php$@', or '@vendor/.*/(tests|Tests)/@')
    'exclude_file_regex' => '@^vendor/.*/(tests?|Tests?)/@',
    'plugins' => [
        'vendor/drenso/phan-extensions/Plugin/Annotation/SymfonyAnnotationPlugin.php',
        'vendor/drenso/phan-extensions/Plugin/DocComment/InlineVarPlugin.php',
        'vendor/drenso/phan-extensions/Plugin/DocComment/MethodPlugin.php',
    ]
];