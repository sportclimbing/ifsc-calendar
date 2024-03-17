# IFSC Climbing Calendar and Streams
![downloads](https://img.shields.io/github/downloads/sportclimbing/ifsc-calendar/total?color=green&label=Downloads)
![update ralendar](https://github.com/sportclimbing/ifsc-calendar/actions/workflows/update-calendar.yml/badge.svg)

![ifsc-logo](resources/images/ifsc-logo.png)

### 📖 TL;DR
This automatically generates an up-to-date calendar you can subscribe to, to never miss an IFSC climbing event ever again.

#### How
Copy and paste this calendar URL (**https://calendar.ifsc.stream**) into your calendar subscriptions, and it will
automatically sync with your device. This works on iPhone, Google Calendar, Proton Calendar, etc... This should keep
you updated on future seasons as well.

Take a look at the **[setup guides](https://github.com/sportclimbing/ifsc-calendar/wiki)** for help.

Additionally, the calendar data is exported as `JSON` and can be viewed on this automatically updated
website:

## 👉 [https://ifsc.stream](https://ifsc.stream/)

### 👀 Intro
If you're constantly missing IFSC events because of a lacking calendar, or timezone confusions,
then you're at the right place.

This command line tool uses IFSC's API, plus some scraping (because some endpoints require 
authentication) to generate an up-to-date calendar with all necessary info.

<div align="center">
    <img src="resources/images/calendar.png" alt="calendar" />
</div>

### 🛠 Usage

#### Docker
Build Docker image
```shell
$ docker build --tag ifsc-calendar .
```
Generate `.ics` calendar file
```shell
$ docker run -it --volume "$PWD:/calendar" ifsc-calendar \
    --season 2024 \
    --league "World Cups and World Championships" \
    --output "/calendar/ifsc-calendar.ics"
```

Generate `.json` calendar file
```shell
$ docker run -it --volume "$PWD:/calendar" ifsc-calendar \
    --season 2024 \
    --league "World Cups and World Championships" \
    --output "/calendar/ifsc-calendar.json" \
    --format json
```

Build for multiple leagues
```shell
$ docker run -it --volume "$PWD:/calendar" ifsc-calendar \
    --season 2024 \
    --league "World Cups and World Championships" \
    --league "Games" \
    --league "IFSC Paraclimbing" \
    --output "/calendar/ifsc-calendar.json" \
    --format json
```

#### Build it yourself
Build executable
```shell
$ make
```

Generate `.ics` calendar file
```
$ ./build/ifsc-calendar.phar \
  --season 2024 \
  --league "World Cups and World Championships" \
  --output "ifsc-calendar.ics"
```

### 🔧 Todo
 - [ ] Calculate average event duration and add it to `events.json` and to the calendar
 - [ ] Finish writing calendar setup guides
 - [ ] Cleanup PHP code
 - [ ] Add more tests
 - [ ] Make scraping more robust and fail on errors or missing data
 - [ ] Show activity and warnings in console (domain events)
 - [ ] Fix scraper for older seasons (formatting changes drastically)
 - [x] Validate newly generated calendar before publishing site
 - [x] Search all YouTube API results (not only the first 50 results)
 - [x] Check if there's an API to fetch events from instead of relying on scraping
 - [x] Find a way to integrate events not posted on the official event page (e.g. Bern)
 - [x] Disable youtube-fetch by default
 - [x] Add links to specific events to calendar
 - [x] Add `latest` tag to latest release
 - [x] Always serve asset from latest release on calendar URL
 - [x] Fetch stream links from YouTube API if none can be scraped
 - [x] Automatically regenerate calendar and update release

### Requirements
- PHP 8.3
- ext-dom
- ext-libxml

### Legal note
This is in no way affiliated with, or endorsed by IFSC.
