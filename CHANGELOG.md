# Release Notes

## [Unreleased](https://github.com/laravel/pail/compare/v1.3.0...main)

## [v1.3.0](https://github.com/laravel/pail/compare/v1.2.7...v1.3.0) - 2026-11-09

### What's Changed

* GitHub Actions hardening by [@nunomaduro](https://github.com/nunomaduro) in https://github.com/laravel/pail/pull/71
* Bump actions/checkout from 4.3.1 to 6.0.3 in the github-actions group by [@dependabot](https://github.com/dependabot)[bot] in https://github.com/laravel/pail/pull/72
* Add Dependabot cooldown of 5 days by [@nunomaduro](https://github.com/nunomaduro) in https://github.com/laravel/pail/pull/73
* Enable Dependabot auto-merge by [@nunomaduro](https://github.com/nunomaduro) in https://github.com/laravel/pail/pull/74
* Fix reusable workflow pin to match granted permissions by [@nunomaduro](https://github.com/nunomaduro) in https://github.com/laravel/pail/pull/75
* Bump shivammathur/setup-php from 2.37.1 to 2.37.2 in the github-actions group by [@dependabot](https://github.com/dependabot)[bot] in https://github.com/laravel/pail/pull/76
* Bump actions/checkout from 6.0.3 to 7.0.0 in the github-actions group by [@dependabot](https://github.com/dependabot)[bot] in https://github.com/laravel/pail/pull/77
* Bump actions/checkout from 7.0.0 to 7.0.1 in the github-actions group by [@dependabot](https://github.com/dependabot)[bot] in https://github.com/laravel/pail/pull/78
* Avoid accessing missing authenticated user email attributes by [@rossvasquez](https://github.com/rossvasquez) in https://github.com/laravel/pail/pull/83
* Remove security policy in favor of the org-wide default by [@pushpak1300](https://github.com/pushpak1300) in https://github.com/laravel/pail/pull/84
* Bump shivammathur/setup-php from 2.37.2 to 2.40.0 in the github-actions group by [@dependabot](https://github.com/dependabot)[bot] in https://github.com/laravel/pail/pull/85
* Raise dependency floor by [@joetannenbaum](https://github.com/joetannenbaum) in https://github.com/laravel/pail/pull/86

### New Contributors

* [@dependabot](https://github.com/dependabot)[bot] made their first contribution in https://github.com/laravel/pail/pull/72
* [@rossvasquez](https://github.com/rossvasquez) made their first contribution in https://github.com/laravel/pail/pull/83
* [@pushpak1300](https://github.com/pushpak1300) made their first contribution in https://github.com/laravel/pail/pull/84
* [@joetannenbaum](https://github.com/joetannenbaum) made their first contribution in https://github.com/laravel/pail/pull/86

**Full Changelog**: https://github.com/laravel/pail/compare/v1.2.7...v1.3.0

## [v1.2.7](https://github.com/laravel/pail/compare/v1.2.6...v1.2.7) - 2026-05-20

* Makes imports consistent by [@nunomaduro](https://github.com/nunomaduro) in https://github.com/laravel/pail/pull/64
* fix: prevent Pail from crashing on malformed JSON log lines by [@SagorIslamOfficial](https://github.com/SagorIslamOfficial) in https://github.com/laravel/pail/pull/67
* fix: avoid resolving the auth user when building log context by [@cosmicspork](https://github.com/cosmicspork) in https://github.com/laravel/pail/pull/70

## [v1.2.6](https://github.com/laravel/pail/compare/v1.2.5...v1.2.6) - 2026-02-09

* Update command description formatting to match all other Laravel Commands by [@matthewjohns0n](https://github.com/matthewjohns0n) in https://github.com/laravel/pail/pull/62

## [v1.2.5](https://github.com/laravel/pail/compare/v1.2.4...v1.2.5) - 2026-02-04

* Supports Laravel 13 by [@crynobone](https://github.com/crynobone) in https://github.com/laravel/pail/pull/61

## [v1.2.4](https://github.com/laravel/pail/compare/v1.2.3...v1.2.4) - 2025-11-20

* [1.x] PHP 8.5 Compatibility by [@crynobone](https://github.com/crynobone) in https://github.com/laravel/pail/pull/59

## [v1.2.3](https://github.com/laravel/pail/compare/v1.2.2...v1.2.3) - 2025-06-05

* Support multiline logs by [@gabus](https://github.com/gabus) in https://github.com/laravel/pail/pull/55
* Suggests `laravel/pail` as dev dependencies by [@crynobone](https://github.com/crynobone) in https://github.com/laravel/pail/pull/56
* Disable deprecation notice from being displayed via Pail by [@crynobone](https://github.com/crynobone) in https://github.com/laravel/pail/pull/57

## [v1.2.2](https://github.com/laravel/pail/compare/v1.2.1...v1.2.2) - 2025-01-28

* Supports Laravel 12 by [@crynobone](https://github.com/crynobone) in https://github.com/laravel/pail/pull/49

## [v1.2.1](https://github.com/laravel/pail/compare/v1.2.0...v1.2.1) - 2024-10-23

* Use `testbench-core` instead of `testbench` by [@crynobone](https://github.com/crynobone) in https://github.com/laravel/pail/pull/46

## [v1.2.0](https://github.com/laravel/pail/compare/v1.1.5...v1.2.0) - 2024-10-21

* [1.x] Supports PHP 8.4 by [@crynobone](https://github.com/crynobone) in https://github.com/laravel/pail/pull/44
* [1.x] Adds `--timeout` option by [@nunomaduro](https://github.com/nunomaduro) in https://github.com/laravel/pail/pull/45

## [v1.1.5](https://github.com/laravel/pail/compare/v1.1.4...v1.1.5) - 2024-10-15

* [1.x] Test Improvements by [@crynobone](https://github.com/crynobone) in https://github.com/laravel/pail/pull/42

## [v1.1.4](https://github.com/laravel/pail/compare/v1.1.3...v1.1.4) - 2024-09-16

* Replace dead link in Security Policy by [@Jubeki](https://github.com/Jubeki) in https://github.com/laravel/pail/pull/38

## [v1.1.3](https://github.com/laravel/pail/compare/v1.1.2...v1.1.3) - 2024-05-08

* Add gitignore to ignore pail files in storage by [@ahinkle](https://github.com/ahinkle) in https://github.com/laravel/pail/pull/35

## [v1.1.2](https://github.com/laravel/pail/compare/v1.1.1...v1.1.2) - 2024-04-02

* [1.x] Make commands lazy by [@timacdonald](https://github.com/timacdonald) in https://github.com/laravel/pail/pull/32
* [1.x] Adds `Context` support by [@nunomaduro](https://github.com/nunomaduro) in https://github.com/laravel/pail/pull/34

## [v1.1.1](https://github.com/laravel/pail/compare/v1.1.0...v1.1.1) - 2024-01-26

* [1.x] Update isStale to avoid errors from filemtime by [@cyppe](https://github.com/cyppe) in https://github.com/laravel/pail/pull/30

## [v1.1.0](https://github.com/laravel/pail/compare/v1.0.1...v1.1.0) - 2024-01-15

* [1.x] Laravel v11 support by [@nunomaduro](https://github.com/nunomaduro) in https://github.com/laravel/pail/pull/28

## [v1.0.1](https://github.com/laravel/pail/compare/v1.0.0-beta.2...v1.0.1) - 2023-11-23

* [1.x] Fixes exception as string by [@nunomaduro](https://github.com/nunomaduro) in https://github.com/laravel/pail/pull/22

## [v1.0.0-beta.2](https://github.com/livewire/pail/compare/v1.0.0-beta.1...v1.0.0-beta.2) - 2023-10-19

- Fixes unescaped HTML content causing runtime exception ([#11](https://github.com/laravel/pail/pull/11))

## v1.0.0-beta.1 - 2023-10-06

First pre-release.
