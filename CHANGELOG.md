# Changelog

### 0.7.0 (2026-04-24)

- add support for properties documentation (`__properties_list__` / `__properties_index__` template placeholders)
- add support for enum cases documentation (`__enum_cases__` template placeholder)
- add support for traits (treated the same as classes/interfaces)
- add `skipPropertiesWithLeadingUnderscore`, `templateProperty`, and `templateEnumCase` public properties
- add `__properties_index__` anchor ID parameter to `getIndexHtmlTable`
- achieve 100% test coverage

### 0.6.4 (2022-09-01)

- update vendor
- fallback for not detected phpdocs params

### 0.6.3 (2021-10-18)

- update vendor

### 0.6.2 (2020-09-06)

- update vendor + fix BC

### 0.6.1 (2020-08-23)

- fix github links

### 0.6.0 (2020-08-04)

- generate API docs for interfaces in the same way as for classes

### 0.5.1 (2020-07-15)

- fix html-table output

### 0.5.0 (2020-07-15)

- update "voku/simple-php-code-parser"

### 0.4.0 (2020-05-23)

- update "voku/simple-php-code-parser"

### 0.3.0 (2020-05-16)

- added GenerateApi->hideTheFunctionIndex
- fix "href" usage (changed the id from "class-methods" into "voku-php-readme-class-methods")
- use templates (for return types)

### 0.2.0 (2020-05-15)

- "GenerateApi" -> make the template-strings changeable

### 0.1.0 (2020-05-14)

- initial commit