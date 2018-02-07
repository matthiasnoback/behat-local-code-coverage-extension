# Behat local code coverage extension

This extension can be used to collect code coverage data when using Behat.

To use this extension, enable it under `extensions` and for every suite that needs local code coverage collection, set `local_coverage_enabled` to `true`.

```yaml
default:
    extensions:
        BehatLocalCodeCoverage\LocalCodeCoverageExtension:
            target_directory: '%paths.base%/var/coverage'
    suites:
        default:
            local_coverage_enabled: true
```

After a test run, you'll find a `.cov` file in the target directory for every suite that has local code coverage enabled.

You can use these `.cov` files to generate nice reports, using [`phpcov`](https://github.com/sebastianbergmann/phpcov).

You could even configure PHPUnit to generate a `.cov` file in the same directory, so you can combine coverage data from PHPUnit and Behat in one report.
