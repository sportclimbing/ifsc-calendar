# IFSC Calendar

### TL;DR
Import the `.ics` from the [releases](https://github.com/nicoSWD/ifsc-calendar/releases/tag/2023) page file to your favorite calendar and never miss an IFSC event again

### About
If you're constantly missing IFSC events because of a lacking calendar, or timezone confusions,
then this is for you.

This command line tool uses the IFSC API plus some scraping to generate an up-to-date calendar with all necessary info.

### Usage
Build executable
```shell
$ make
```
Generate `.ics` file
```shell
$ ./build/ifsc-calendar.phar \
  --season 2023 \
  --league "World Cups and World Championships"
```

### Requirements
- PHP 8.2
- ext-dom
- ext-libxml
