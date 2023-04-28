# IFSC Calendar

![ifsc-logo](resources/images/ifsc-logo.png)

### TL;DR
Import the `.ics` file from the **[IFSC Calendar 2023](https://ifsc.stream/)** page to your
favorite calendar and never miss an IFSC Climbing event again.

### Intro
If you're constantly missing IFSC events because of a lacking calendar, or timezone confusions,
then you're at the right place.

This command line tool uses IFSC's API, plus some scraping (because some endpoints require 
authentication) to generate an up-to-date calendar with all necessary info.

### Usage
#### Docker
Build Docker image
```shell
$ docker build --tag ifsc-calendar .
```
Generate `.ics` calendar file
```shell
$ docker run -it ifsc-calendar \
    --volume "$PWD:/calendar" ifsc-calendar \
    --season 2023 \
    --league "World Cups and World Championships" \
    --output "/calendar/ifsc-calendar.ics"
```

Generate `.json` calendar file
```shell
$ docker run -it ifsc-calendar \
    --volume "$PWD:/calendar" ifsc-calendar \
    --season 2023 \
    --league "World Cups and World Championships" \
    --output "/calendar/ifsc-calendar.json" \
    --format json
```

#### Build it yourself
Build executable
```shell
$ make
```
Generate `.ics` calendar file
```shell
$ ./build/ifsc-calendar.phar \
  --season 2023 \
  --league "World Cups and World Championships" \
  --output "ifsc-calendar.ics"
```

### Requirements
- PHP 8.2
- ext-dom
- ext-libxml

### Legal note
This is in no way affiliated with, or endorsed by IFSC.
