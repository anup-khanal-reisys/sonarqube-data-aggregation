# PHP based API Client for SonarQube API
PHP based API Client to query SonarQube Data

### Based On PHP Wrapper by
```
https://github.com/spirit-dev/php-sonarqube-api
```

Installation
------------
Install Composer (for Buzz HTTPClient)
```shell
$ curl -sS https://getcomposer.org/installer | php
$ sudo mv composer.phar /usr/local/bin/composer
```

1. `git clone` the app and `cd` into it
2. run `composer install`
3. Open index.php and supply the appropriate information (hostname, username and password or token) for SonarQube connections in `$client` (see [General API Usage](#general-api-usage) for details)
4. For multiple sources you can have `$client1`, `$client2` and so on.
5. Make sure `api-cache.array` and `settings.array` have write permissions by doing `chmod 777 api-cache.array` and `chmod 777 settings.array`. If you wish, you can use `settings.array` to declare other static variables too like hostname, tokens, etc.
6. You can run the application locally using XAMPP or any machine running a LAMP stack (although we don't need MySQL).
7. The application will be available on `localhost:8080` or simply `localhost`.

<a id="general-api-usage"></a>General API Usage
------------
```php
$client = new \SonarQube\Client('[hostname]/api', '[username]', '[password]');
/*---------------- Using Tokens --------------------------*/
//$client = new \SonarQube\Client('https://sonar.reisys.com/api/', '[token]', '');
$authentication = $client->api('authentication')->validate();
```

### Specific Project
```php
$projects = $client->projects->search(['search'=>'[Project Name]']);
```

### List of Projects
```php
$projects = $client->projects->search();
```

### Metrics Data
You can supply as many metics data as you need, as long as the key matches the available metric key. Refer to the [Available Metrics Data for SonarQube Web API](#metrics) for available metrics.
```php
$measures = $client->measures->component(['componentKey'=>'[Project Name]]','metricKeys'=>'[[Metric 1],[Metric 2],[Metric 3],...]']);
```

### Response Format
Following is the response format
```
[
    {
        https://sonar.reisys.com/api/: [
        {
            project: "Project Name",
            metrics: {
                reliability_rating: "A",
                new_code_smells: "4",
                sqale_rating: "A",
                vulnerabilities: "0",
                new_vulnerabilities: "0",
                coverage: "42.9",
                new_bugs: "0",
                bugs: "0",
                security_rating: "A",
                new_coverage: "1.2820512820512822",
                code_smells: "557",
                last_analyzed: "2018-01-19T10:53:51-0500"
            }
        },
        ...,
        ...
        ]
    },
    ...,
    ...
]
```

<a id="metrics"></a>Available Metrics Data for SonarQube Web API
------------
[https://docs.sonarqube.org/display/SONAR/Metric+Definitions](https://docs.sonarqube.org/display/SONAR/Metric+Definitions)

| Metric key | Description |
|------------|-------------|
| complexity | It is the complexity calculated based on the number of paths through the code. |
| cognitive_complexity | How hard it is to understand the code's control flow. |
| comment_lines | Number of lines containing either comment or commented-out code. |
| comment_lines_density | Density of comment lines = Comment lines / (Lines of code + Comment lines) * 100 |
| public_documented_api_density | Density of public documented API = (Public API - Public undocumented API) / Public API * 100 |
| public_undocumented_api | Public API without comments header. |
| commented_out_code_lines | Commented lines of code |
| duplicated_blocks | Number of duplicated blocks of lines. |
| duplicated_files | Number of files involved in duplications. |
| duplicated_lines | Number of lines involved in duplications. |
| duplicated_lines_density | Density of duplication = Duplicated lines / Lines * 100 |
| new_violations | Number of new issues.|
| new_xxxxx_violations | Number of new issues with severity xxxxx, xxxxx being blocker, critical, major, minor or info. |
| violations | Number of issues. |
| xxxxx_violations | Number of issues with severity xxxxx, xxxxx being blocker, critical, major, minor or info. |
| false_positive_issues |	Number of false positive issues |
|	open_issues	| Number of issues whose status is Open |
|	confirmed_issues | Number of issues whose status is Confirmed |
|	reopened_issues	| Number of issues whose status is Reopened |
| code_smells	| Number of code smells. |
|	new_code_smells	| Number of new code smells. |
| sqale_rating | Rating given to your project related to the value of your Technical Debt Ratio. |
| sqale_index	| Effort to fix all maintainability issues. |
| new_technical_debt	| Technical Debt of new code |
| sqale_debt_ratio	| Ratio between the cost to develop the software and the cost to fix it. 
|	new_sqale_debt_ratio | Ratio between the cost to develop the code changed in the leak period and the cost of the issues linked to it. |
| alert_status | State of the Quality Gate associated to your Project. |
| quality_gate_details | For all the conditions of your Quality Gate, you know which condition is failing and which is not. |
| bugs	| Number of bugs. |
| new_bugs	| Number of new bugs. |
| reliability_rating | A = 0 Bug; B = at least 1 Minor Bug; C = at least 1 Major Bug; D = at least 1 Critical Bug; E = at least 1 Blocker Bug |
| reliability_remediation_effort	| Effort to fix all bug issues. |
| new_reliability_remediation_effort	| Same as Reliability remediation effort by on the code changed in the leak period. |
| vulnerabilities	 | Number of vulnerabilities. |
| new_vulnerabilities	| Number of new vulnerabilities. |
| security_rating	| A = 0 Vulnerability; B = at least 1 Minor Vulnerability; C = at least 1 Major Vulnerability; D = at least 1 Critical Vulnerability; E = at least 1 Blocker Vulnerability |
| security_remediation_effort |	Effort to fix all vulnerability issues. |
| new_security_remediation_effort	| Same as Security remediation effort by on the code changed in the leak period. |
| classes	| Number of classes (including nested classes, interfaces, enums and annotations). |
| directories	| Number of directories. |
| files	| Number of files. |
| lines	| Number of physical lines (number of carriage returns). |
| ncloc	| Number of physical lines that contain at least one character which is neither a whitespace nor a tabulation nor part of a comment. |
| ncloc_language_distribution	| Non Commenting Lines of Code Distributed By Language |
| functions	| Number of functions. Depending on the language, a function is either a function or a method or a paragraph. |
| projects | Number of projects in a view. |
| public_api | Number of public Classes + number of public Functions + number of public Properties |
| statements	| Number of statements. |
| branch_coverage | This is the density of possible conditions in flow control structures that have been followed during unit tests execution. |
| new_branch_coverage | Identical to Condition coverage but restricted to new / updated source code. |
| branch_coverage_hits_data	| List of covered conditions. |
| conditions_by_line	| Number of conditions by line. |
| covered_conditions_by_line	| Number of covered conditions by line. |
| coverage | It is a mix of Line coverage and Condition coverage. Its goal is to provide an even more accurate answer to the following question: How much of the source code has been covered by the unit tests? |
| new_coverage | Identical to Coverage but restricted to new / updated source code. |
| line_coverage | Has this line of code been executed during the execution of the unit tests?. It is the density of covered lines by unit tests |
| new_line_coverage	| Identical to Line coverage but restricted to new / updated source code. |
| coverage_line_hits_data	| List of covered lines. |
| lines_to_cover	| Number of lines of code which could be covered by unit tests (for example, blank lines or full comments lines are not considered as lines to cover). |
| new_lines_to_cover	| Identical to Lines to cover but restricted to new / updated source code. |
| skipped_tests	| Number of skipped unit tests. |
| uncovered_conditions	| Number of conditions which are not covered by unit tests. |
| new_uncovered_conditions	| Identical to Uncovered conditions but restricted to new / updated source code. |
| uncovered_lines	| Number of lines of code which are not covered by unit tests. |
| new_uncovered_lines	| Identical to Uncovered lines but restricted to new / updated source code. |
| tests	| Number of unit tests. |
| test_execution_time	| Time required to execute all the unit tests. |
| test_errors	| Number of unit tests that have failed. |
| test_failures	| Number of unit tests that have failed with an unexpected exception. |
| test_success_density	| Test success density = (Unit tests - (Unit test errors + Unit test failures)) / Unit tests * 100 |

