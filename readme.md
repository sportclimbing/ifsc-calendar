# IFSC Climbing Calendar and Streams

![ifsc-logo](resources/images/ifsc-logo.png)

### TL;DR
This automatically generates a calendar you can subscribe to, to never miss an IFSC climbing event ever again.

Simply copy and paste `.ics` file URL from the **[IFSC Calendar 2023](https://ifsc.stream/)** page into your
calendar subscriptions, and it will automatically update. This works for Apple devices, Google Calendar,
Proton Calendar, etc...

Take a look at the **[setup guides](https://github.com/nicoSWD/ifsc-calendar/wiki)** for help.

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

### Todo
 - [ ] Make landing site prettier and responsive
 - [ ] Calculate average event duration and add it to `events.json` and to the calendar
 - [ ] Download posters and save them locally (preferably downsized versions too)
 - [ ] Add VPN note and link (to ProtonVPN?)
 - [ ] Validate newly generated calendar before publishing site
 - [ ] Finish writing calendar setup guides
 - [ ] Change favicon when an event started streaming
 - [ ] Show past and future events
 - [ ] Cleanup PHP code
 - [ ] Add tests
 - [ ] Fetch stream links from YouTube API if none can be scraped
 - [ ] Make scraping more robust and fail on errors or missing data
 - [ ] Show exact streaming date/time in addition to "in X hours" (maybe via tooltip?)
 - [ ] Add Google Analytics (and cookie notice)
 - [ ] Check if there's an API to fetch events from instead of relying on scraping
 - [ ] Add default poster if none exists
 - [x] Change `opacity` to 100 for next event in line (if not currenlty streaming)
 - [x] Automatically regenerate calendar and update release

### Requirements
- PHP 8.2
- ext-dom
- ext-libxml

### Legal note
This is in no way affiliated with, or endorsed by IFSC.
